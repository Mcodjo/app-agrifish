<?php

namespace Tests\Feature\Shop;

use App\Models\Setting;
use App\Models\Shop\Coupon;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Notifications\Shop\NewShopOrder;
use App\Notifications\Shop\OrderPaid;
use App\Notifications\Shop\OrderPlaced;
use App\Notifications\Shop\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase, ShopHelpers;

    public function test_catalogue_shows_only_published_products_and_filters(): void
    {
        $fish = $this->makeCategory('Aquaculture');
        $feed = $this->makeCategory('Aliments poisson', $fish);
        $visible = $this->makeProduct(['name' => 'Granulés tilapia', 'category_id' => $feed->id], [['price' => 15000, 'stock' => 3]]);
        $cheap = $this->makeProduct(['name' => 'Épuisette'], [['price' => 2000, 'stock' => 0]]);
        $hidden = $this->makeProduct(['name' => 'Produit brouillon', 'is_published' => false]);

        $this->get(route('shop.index'))->assertOk()->assertSee('Granulés tilapia')->assertSee('Épuisette')->assertDontSee('Produit brouillon');
        $this->get(route('shop.index', ['categorie' => 'aquaculture']))->assertSee('Granulés tilapia')->assertDontSee('Épuisette');
        $this->get(route('shop.index', ['min' => 5000]))->assertSee('Granulés tilapia')->assertDontSee('Épuisette');
        $this->get(route('shop.index', ['disponible' => 1]))->assertSee('Granulés tilapia')->assertDontSee('Épuisette');
        $this->get(route('shop.index', ['q' => 'tilapia']))->assertSee('Granulés tilapia')->assertDontSee('Épuisette');

        $this->get(route('shop.product', $visible))->assertOk()->assertSee('application/ld+json', false);
        $this->get('/boutique/produit/'.$hidden->slug)->assertNotFound();
        $this->get(route('sitemap'))->assertOk()->assertSee(route('shop.product', $visible));
    }

    public function test_cart_respects_stock_and_coupons(): void
    {
        $product = $this->makeProduct([], [['price' => 10000, 'stock' => 3]]);
        $variant = $product->variants->first();

        $this->addToCart($variant, 2)->assertSessionHasNoErrors();
        $this->addToCart($variant, 2)->assertSessionHasErrors('quantity');

        Coupon::create(['code' => 'MOINS10', 'type' => 'percent', 'value' => 10, 'is_active' => true]);
        Coupon::create(['code' => 'GROS', 'type' => 'fixed', 'value' => 5000, 'min_amount' => 50000, 'is_active' => true]);

        $this->post(route('shop.cart.coupon'), ['code' => 'INCONNU'])->assertSessionHasErrors('code');
        $this->post(route('shop.cart.coupon'), ['code' => 'gros'])->assertSessionHasErrors('code');
        $this->post(route('shop.cart.coupon'), ['code' => 'moins10'])->assertSessionHasNoErrors();

        $this->get(route('shop.cart'))->assertOk()->assertSee('MOINS10')->assertSee(money(18000));
    }

    public function test_order_paid_on_delivery_reserves_stock_and_notifies(): void
    {
        Notification::fake();
        $manager = $this->userWithRole('manager');
        $product = $this->makeProduct([], [['price' => 12500, 'stock' => 4]]);
        $variant = $product->variants->first();

        $this->addToCart($variant, 3);
        $this->checkout()->assertRedirect();

        $order = $this->latestOrder();
        $this->assertSame('new', $order->status);
        $this->assertSame('to_collect', $order->payment_status);
        $this->assertSame(37500, $order->total);
        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertSame(-3, $variant->movements()->first()->quantity);
        $this->get(route('shop.cart'))->assertSee('Votre panier est vide');

        Notification::assertSentOnDemand(OrderPlaced::class, fn ($n, $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'awa@example.com');
        Notification::assertSentTo($manager, NewShopOrder::class);
    }

    public function test_last_item_cannot_be_ordered_twice(): void
    {
        $variant = $this->makeProduct([], [['price' => 5000, 'stock' => 1]])->variants->first();
        $cookie = config('shop.cart_cookie');

        // Deux clients ont chacun le dernier article dans leur panier.
        $tokenA = $this->withCookie($cookie, 'a')->addToCart($variant)->getCookie($cookie)->getValue();
        $tokenB = $this->withCookie($cookie, 'b')->addToCart($variant)->getCookie($cookie)->getValue();
        $this->assertNotSame($tokenA, $tokenB);

        $this->withCookie($cookie, $tokenA)->checkout()->assertSessionHasNoErrors();
        $this->withCookie($cookie, $tokenB)->checkout()->assertSessionHasErrors('cart');

        $this->assertSame(0, $variant->fresh()->stock);
        $this->assertSame(1, Order::query()->count());
    }

    public function test_online_payment_with_test_gateway_confirms_order(): void
    {
        Notification::fake();
        $variant = $this->makeProduct([], [['price' => 20000, 'stock' => 2]])->variants->first();

        $this->addToCart($variant);
        $response = $this->checkout(['payment_method' => 'online']);

        $order = $this->latestOrder();
        $payment = $order->payments()->firstOrFail();
        $this->assertSame('pending_payment', $order->status);
        $response->assertRedirect($payment->checkout_url);

        $this->get($payment->checkout_url)->assertOk()->assertSee('Simuler un paiement réussi');

        $this->post(\Illuminate\Support\Facades\URL::temporarySignedRoute('shop.payment.simulate.confirm', now()->addMinutes(5), $payment), ['outcome' => 'approved'])
            ->assertRedirect(route('shop.payment.return', $payment));
        $this->get(route('shop.payment.return', $payment))->assertRedirect($order->publicUrl());

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('paid', $order->payment_status);
        Notification::assertSentOnDemand(OrderPaid::class);

        // Rejouer le retour ne déclenche rien de plus.
        $this->get(route('shop.payment.return', $payment));
        Notification::assertSentOnDemandTimes(OrderPaid::class, 1);
    }

    public function test_unpaid_order_expires_and_stock_is_released(): void
    {
        Notification::fake();
        $variant = $this->makeProduct([], [['price' => 8000, 'stock' => 2]])->variants->first();

        $this->addToCart($variant, 2);
        $this->checkout(['payment_method' => 'online']);
        $order = $this->latestOrder();
        $this->assertSame(0, $variant->fresh()->stock);

        $this->travel(25)->hours();
        $this->artisan('shop:expire-orders')->assertSuccessful();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(2, $variant->fresh()->stock);
        $this->assertSame('canceled', $order->payments()->first()->status);
        Notification::assertSentOnDemand(OrderStatusUpdated::class);
    }

    public function test_webhook_without_valid_signature_is_rejected(): void
    {
        config(['services.fedapay.webhook_secret' => 'secret']);

        $this->postJson(route('shop.webhook', 'fedapay'), ['entity' => ['id' => 1]])->assertStatus(400);

        $payload = json_encode(['name' => 'transaction.approved', 'entity' => ['id' => 99]]);
        $t = time();
        $signature = hash_hmac('sha256', $t.'.'.$payload, 'secret');

        $this->call('POST', route('shop.webhook', 'fedapay'), [], [], [], ['HTTP_X_FEDAPAY_SIGNATURE' => "t=$t,s=$signature", 'CONTENT_TYPE' => 'application/json'], $payload)
            ->assertOk();
    }

    public function test_order_page_requires_the_private_link(): void
    {
        $variant = $this->makeProduct()->variants->first();
        $this->addToCart($variant);
        $this->checkout();
        $order = $this->latestOrder();

        $this->flushSession();
        $this->get(route('shop.orders.show', $order))->assertNotFound();
        $this->get(route('shop.orders.show', ['order' => $order->number, 'cle' => 'mauvais']))->assertNotFound();
        $this->get($order->publicUrl())->assertOk()->assertSee($order->number);
        $this->get(route('shop.orders.invoice', $order))->assertNotFound();
    }

    public function test_checkout_refuses_disabled_payment_method_and_requires_cgv(): void
    {
        Setting::put(['cash_on_delivery' => '0']);
        $variant = $this->makeProduct()->variants->first();
        $this->addToCart($variant);

        $this->checkout()->assertSessionHasErrors('payment_method');
        $this->checkout(['payment_method' => 'online', 'cgv' => null])->assertSessionHasErrors('cgv');
        $this->assertSame(0, Order::query()->count());
    }

    public function test_guest_orders_are_attached_once_the_email_is_verified(): void
    {
        $variant = $this->makeProduct()->variants->first();
        $this->addToCart($variant);
        $this->checkout(['email' => 'mariam@example.com']);
        $order = $this->latestOrder();

        $user = $this->userWithRole('client', ['email' => 'mariam@example.com', 'email_verified_at' => null]);
        $this->assertSame(0, $user->claimGuestOrders());

        $user->markEmailAsVerified();
        $this->assertSame(1, $user->claimGuestOrders());
        $this->assertSame($user->id, $order->fresh()->user_id);
        $this->actingAs($user)->get(route('account.orders.show', $order))->assertOk()->assertSee($order->number);
    }

    public function test_payment_model_status_labels(): void
    {
        $this->assertSame('Accepté', (new Payment(['status' => 'approved']))->statusLabel());
    }
}
