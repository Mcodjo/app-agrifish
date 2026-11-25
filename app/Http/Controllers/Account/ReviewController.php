<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Models\Shop\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** Seul un client qui a reçu le produit peut le noter ; l'avis est publié après validation. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);

        $user = $request->user();

        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $validated['product_id']))
            ->latest()
            ->first();

        abort_unless($order, 403, 'Vous pourrez noter ce produit une fois votre commande reçue.');

        ProductReview::query()->updateOrCreate(
            ['product_id' => $validated['product_id'], 'user_id' => $user->id],
            ['order_id' => $order->id, 'rating' => $validated['rating'], 'comment' => $validated['comment'] ?? null, 'status' => 'pending']
        );

        return back()->with('status', 'review-sent');
    }
}
