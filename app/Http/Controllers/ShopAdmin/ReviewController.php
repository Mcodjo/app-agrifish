<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = array_key_exists((string) $request->query('statut'), ProductReview::STATUSES) ? $request->query('statut') : 'pending';

        return view('shop-admin.reviews', [
            'reviews' => ProductReview::query()->with(['product', 'user'])->where('status', $status)->latest()->paginate(20)->withQueryString(),
            'status' => $status,
            'counts' => ProductReview::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function update(Request $request, ProductReview $review): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['approved', 'rejected'])]]);
        $review->update($validated);

        return back()->with('status', $validated['status'] === 'approved' ? 'review-approved' : 'review-rejected');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'review-deleted');
    }
}
