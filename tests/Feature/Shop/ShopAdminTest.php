<?php

namespace Tests\Feature\Shop;

use App\Models\Shop\Coupon;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductReview;
use App\Notifications\Shop\LowStockAlert;
use App\Notifications\Shop\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopAdminTest extends TestCase
{
    use RefreshDatabase, ShopHelpers;

    private function placeCashOrder(int $quantity = 2, int $stock = 5): Order
    {
        $variant = $this->makeProduct([], [['price' => 10000, 'stock' => $stock]])->variants->first();
        $this->addToCart($variant, $quantity);
        $this->checkout();
        $this->flushSession();

        return $this->latestOrder();
    }

    public function test_only_admins_and_managers_reach_the_back_office(): void
    {
        $this->actingAs($this->userWithRole('client'))->get(route('shop-admin.dashboard'))->assertForbidden();
        $this->actingAs($this->userWithRole('expert'))->get(route('shop-admin.orders.index'))->assertForbidden();
        $this->actingAs($this->userWithRole('manager'))->get(route('shop-admin.dashboard'))->assertOk();
        $this->actingAs($this->userWithRole('manager'))->get(route('admin.settings.edit'))->assertForbidden();
        $this->actingAs($this->userWithRole('manager'))->get(route('admin.requests.index'))->assertForbidden();
        $this->actingAs($this->userWithRole('manager'))->get(route('dashboard'))->assertRedirect(route('shop-admin.dashboard'));
    }

    public function test_every_management_screen_renders(): void
    {
        Storage::fake('public');
        $product = $this->makeProduct();
        $order = $this->placeCashOrder();
        $coupon = Coupon::create(['code' => 'TEST', 'type' => 'fixed', 'value' => 1000, 'is_active' => true]);
        $admin = $this->userWithRole('admin');

        foreach ([
            route('dashboard'), route('projects.index'), route('admin.requests.index'), route('admin.users.index'), route('admin.users.create'),
            route('admin.settings.edit'), route('profile.edit'),
            route('shop-admin.dashboard'), route('shop-admin.orders.index'), route('shop-admin.orders.show', $order),
            route('shop-admin.products.index'), route('shop-admin.products.create'), route('shop-admin.products.edit', $product),
            route('shop-admin.categories.index'), route('shop-admin.coupons.index'), route('shop-admin.coupons.create'),
            route('shop-admin.coupons.edit', $coupon), route('shop-admin.reviews.index'), route('shop-admin.import-export'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $client = $this->userWithRole('client');
        foreach ([route('dashboard'), route('projects.index'), route('account.orders.index'), route('account.addresses.index'), route('requests.create')] as $url) {
            $this->actingAs($client)->get($url)->assertOk();
        }

        $this->actingAs($this->userWithRole('expert'))->get(route('dashboard'))->assertOk()->assertSee('Échéances à venir');
    }

    public function test_order_goes_from_new_to_handed_over_with_cash_collected(): void
    {
        Notification::fake();
        $order = $this->placeCashOrder();
        $manager = $this->userWithRole('manager');

        $this->actingAs($manager)->post(route('shop-admin.orders.advance', $order))->assertRedirect();
        $this->assertSame('preparing', $order->fresh()->status);
        $this->actingAs($manager)->post(route('shop-admin.orders.advance', $order));
        $this->assertSame('ready', $order->fresh()->status);
        $this->actingAs($manager)->post(route('shop-admin.orders.advance', $order), ['collected' => 1]);

        $order->refresh();
        $this->assertSame('completed', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->completed_at);
        Notification::assertSentOnDemandTimes(OrderStatusUpdated::class, 3);

        $this->actingAs($manager)->get(route('shop.orders.invoice', $order))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_cancel_and_refund_restore_stock(): void
    {
        Notification::fake();
        $manager = $this->userWithRole('manager');

        $order = $this->placeCashOrder(2, 5);
        $variant = $order->items()->first()->variant;
        $this->assertSame(3, $variant->fresh()->stock);

        $this->actingAs($manager)->post(route('shop-admin.orders.cancel', $order), ['reason' => 'Rupture fournisseur']);
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $variant->fresh()->stock);

        $paid = $this->placeCashOrder(1, 5);
        $paidVariant = $paid->items()->first()->variant;
        $this->actingAs($manager)->post(route('shop-admin.orders.collect', $paid));
        $this->actingAs($manager)->post(route('shop-admin.orders.refund', $paid), ['amount' => 999999])->assertSessionHasErrors('amount');
        $this->actingAs($manager)->post(route('shop-admin.orders.refund', $paid), ['amount' => 10000, 'restock' => 1, 'reason' => 'Retour']);

        $paid->refresh();
        $this->assertSame('refunded', $paid->status);
        $this->assertSame(10000, $paid->refunded_amount);
        $this->assertSame(5, $paidVariant->fresh()->stock);
    }

    public function test_product_is_created_with_variants_photos_and_initial_stock(): void
    {
        Storage::fake('public');
        $manager = $this->userWithRole('manager');
        $category = $this->makeCategory('Intrants');

        $payload = [
            'name' => 'Engrais NPK',
            'reference' => 'NPK-1',
            'category_id' => $category->id,
            'low_stock_threshold' => 3,
            'is_published' => 1,
            'variants' => [
                ['name' => 'Sac de 25 kg', 'sku' => 'NPK-25', 'price' => 17500, 'stock' => 10],
                ['name' => 'Sac de 50 kg', 'sku' => 'NPK-50', 'price' => 32000, 'sale_price' => 29000, 'stock' => 4],
            ],
        ];

        $this->actingAs($manager)->post(route('shop-admin.products.store'), $payload)->assertSessionHasErrors('images');

        $payload['images'] = [UploadedFile::fake()->image('sac.jpg', 1200, 900)];
        $this->actingAs($manager)->post(route('shop-admin.products.store'), $payload)->assertSessionHasNoErrors();

        $product = Product::query()->where('reference', 'NPK-1')->firstOrFail();
        $this->assertSame(2, $product->variants()->count());
        $this->assertSame(10, $product->variants()->where('sku', 'NPK-25')->value('stock'));
        $this->assertSame(1, $product->images()->count());
        Storage::disk('public')->assertExists($product->images()->first()->path);
        $this->assertStringEndsWith('.webp', $product->images()->first()->path);
        $this->assertSame(2, \App\Models\Shop\StockMovement::query()->count());

        $this->actingAs($manager)->post(route('shop-admin.products.store'), array_merge($payload, ['reference' => 'NPK-2']))->assertSessionHasErrors('variants.0.sku');
    }

    public function test_stock_adjustment_is_traced_and_low_stock_alert_sent_once(): void
    {
        Notification::fake();
        $manager = $this->userWithRole('manager');
        $product = $this->makeProduct(['low_stock_threshold' => 3], [['price' => 1000, 'stock' => 6]]);
        $variant = $product->variants->first();

        $this->actingAs($manager)->post(route('shop-admin.products.stock', $product), ['variant_id' => $variant->id, 'type' => 'adjustment', 'quantity' => -10])->assertSessionHasErrors('quantity');
        $this->actingAs($manager)->post(route('shop-admin.products.stock', $product), ['variant_id' => $variant->id, 'type' => 'adjustment', 'quantity' => -4, 'note' => 'Casse']);
        $this->actingAs($manager)->post(route('shop-admin.products.stock', $product), ['variant_id' => $variant->id, 'type' => 'adjustment', 'quantity' => -1]);

        $this->assertSame(1, $variant->fresh()->stock);
        Notification::assertSentToTimes($manager, LowStockAlert::class, 1);

        $this->actingAs($manager)->post(route('shop-admin.products.stock', $product), ['variant_id' => $variant->id, 'type' => 'restock', 'quantity' => 20]);
        $this->assertNull($variant->fresh()->low_stock_notified_at);
    }

    public function test_catalog_import_and_exports(): void
    {
        $manager = $this->userWithRole('manager');
        $csv = "reference_produit;nom;categorie;resume;description;publie;seuil_alerte;declinaison;sku;prix;prix_promo;stock\n"
            ."ALV-1;Alevins de tilapia;Aquaculture > Alevins;;;oui;100;Lot de 100;ALV-100;25000;;40\n"
            ."ALV-1;Alevins de tilapia;Aquaculture > Alevins;;;oui;100;Lot de 500;ALV-500;110000;99000;10\n"
            .";Sans référence;;;;non;;;X-1;1000;;1\n";

        $this->actingAs($manager)->post(route('shop-admin.import.catalog'), ['file' => UploadedFile::fake()->createWithContent('catalogue.csv', $csv)])
            ->assertSessionHas('import_result', fn ($r) => $r['created'] === 1 && $r['updated'] === 1 && isset($r['errors'][4]));

        $product = Product::query()->where('reference', 'ALV-1')->firstOrFail();
        $this->assertSame('Alevins', $product->category->name);
        $this->assertSame('Aquaculture', $product->category->parent->name);
        $this->assertSame(50, $product->variants()->sum('stock'));

        $this->actingAs($manager)->get(route('shop-admin.export.catalog'))->assertOk()->assertDownload();
        $this->actingAs($manager)->get(route('shop-admin.export.sales'))->assertOk()->assertDownload();
    }

    public function test_reviews_require_a_completed_order_and_moderation(): void
    {
        Notification::fake();
        $client = $this->userWithRole('client');
        $manager = $this->userWithRole('manager');
        $product = $this->makeProduct();

        $this->actingAs($client)->post(route('account.reviews.store'), ['product_id' => $product->id, 'rating' => 5])->assertForbidden();

        $this->actingAs($client);
        $this->addToCart($product->variants->first());
        $this->checkout(['email' => $client->email]);
        $order = $this->latestOrder();
        $this->assertSame($client->id, $order->user_id);
        $order->forceFill(['status' => 'completed'])->save();

        $this->actingAs($client)->post(route('account.reviews.store'), ['product_id' => $product->id, 'rating' => 4, 'comment' => 'Très bon produit'])->assertSessionHasNoErrors();
        $review = ProductReview::query()->firstOrFail();
        $this->assertSame('pending', $review->status);
        $this->get(route('shop.product', $product))->assertDontSee('Très bon produit');

        $this->actingAs($manager)->patch(route('shop-admin.reviews.update', $review), ['status' => 'approved']);
        $this->get(route('shop.product', $product))->assertSee('Très bon produit');
    }

    public function test_client_can_reorder_and_manage_addresses(): void
    {
        $client = $this->userWithRole('client');
        $other = $this->userWithRole('client');
        $this->actingAs($client);
        $product = $this->makeProduct([], [['price' => 3000, 'stock' => 10]]);
        $this->addToCart($product->variants->first(), 2);
        $this->checkout(['email' => $client->email, 'save_address' => 1]);
        $order = $this->latestOrder();

        $this->assertSame(1, $client->addresses()->count());
        $this->actingAs($other)->get(route('account.orders.show', $order))->assertNotFound();
        $this->actingAs($other)->delete(route('account.addresses.destroy', $client->addresses()->first()))->assertNotFound();

        $this->actingAs($client)->post(route('account.orders.reorder', $order))->assertRedirect(route('shop.cart'));
        $this->actingAs($client)->get(route('shop.cart'))->assertSee($product->name);
    }
}
