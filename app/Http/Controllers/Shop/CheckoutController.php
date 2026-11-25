<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Shop\CartService;
use App\Services\Shop\OrderService;
use App\Services\Shop\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart, private OrderService $orders, private PaymentService $payments)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        abort_unless(Setting::enabled('shop_open'), 503, 'La boutique est momentanément fermée.');

        $summary = $this->cart->summary();

        if ($summary['count'] === 0) {
            return redirect()->route('shop.cart');
        }

        $user = $request->user();
        $address = $user?->addresses()->first();
        [$first, $last] = array_pad(explode(' ', (string) $user?->name, 2), 2, '');

        return view('shop.checkout', [
            'summary' => $summary,
            'methods' => $this->methods(),
            'addresses' => $user?->addresses ?? collect(),
            'defaults' => [
                'first_name' => $address?->first_name ?? $first,
                'last_name' => $address?->last_name ?? $last,
                'email' => $user?->email,
                'phone' => $address?->phone ?? $user?->phone,
                'city' => $address?->city,
                'district' => $address?->district,
                'address_details' => $address?->details,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Setting::enabled('shop_open'), 503);

        $methods = array_keys($this->methods());

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:30', 'regex:/^[0-9 +().-]+$/'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:120'],
            'address_details' => ['nullable', 'string', 'max:255'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in($methods)],
            'save_address' => ['nullable', 'boolean'],
            'cgv' => ['accepted'],
        ], [
            'cgv.accepted' => 'Vous devez accepter les conditions générales de vente.',
            'payment_method.in' => 'Ce mode de paiement n’est pas disponible.',
            'phone.regex' => 'Le numéro de téléphone contient des caractères non autorisés.',
        ]);

        $cart = $this->cart->current();
        abort_unless($cart, 404);

        $user = $request->user();
        $order = $this->orders->place($cart, $validated, $validated['payment_method'], $user);

        if ($user && $request->boolean('save_address') && ! $user->addresses()->where('city', $validated['city'])->where('details', $validated['address_details'] ?? null)->exists()) {
            $user->addresses()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'city' => $validated['city'],
                'district' => $validated['district'] ?? null,
                'details' => $validated['address_details'] ?? null,
                'is_default' => ! $user->addresses()->exists(),
            ]);
        }

        session()->put('orders_access.'.$order->number, true);

        if ($order->payment_method === 'online') {
            $url = $this->payments->start($order);

            return $url
                ? redirect()->away($url)
                : redirect($order->publicUrl())->with('status', 'payment-unavailable');
        }

        return redirect($order->publicUrl())->with('status', 'order-placed');
    }

    private function methods(): array
    {
        $methods = [];

        if ($this->payments->onlineAvailable()) {
            $methods['online'] = ['label' => 'Mobile Money ou carte bancaire', 'help' => 'Paiement sécurisé chez notre prestataire. Votre commande est confirmée dès le paiement reçu.'];
        }

        if ($this->payments->cashAvailable()) {
            $methods['on_delivery'] = ['label' => 'Paiement à la remise', 'help' => 'Vous réglez à la remise de la commande. '.Setting::get('pickup_instructions')];
        }

        return $methods;
    }
}
