<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        return view('shop-admin.coupons.index', ['coupons' => Coupon::query()->latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('shop-admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'is_active' => true, 'min_amount' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::create($this->validated($request));

        return redirect()->route('shop-admin.coupons.index')->with('status', 'coupon-saved');
    }

    public function edit(Coupon $coupon): View
    {
        return view('shop-admin.coupons.form', ['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('shop-admin.coupons.index')->with('status', 'coupon-saved');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('status', 'coupon-deleted');
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => mb_strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'integer', 'min:1'],
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ], ['code.regex' => 'Le code ne contient que des lettres, des chiffres, des tirets ou des traits bas.']);

        if ($data['type'] === 'percent' && $data['value'] > 100) {
            throw ValidationException::withMessages(['value' => 'Un pourcentage ne peut pas dépasser 100.']);
        }

        return $data + ['min_amount' => $data['min_amount'] ?? 0, 'is_active' => $request->boolean('is_active')];
    }
}
