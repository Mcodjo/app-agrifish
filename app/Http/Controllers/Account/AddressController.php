<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Shop\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.addresses', ['addresses' => $request->user()->addresses]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $address = $user->addresses()->create($this->validated($request));

        if ($request->boolean('is_default') || $user->addresses()->count() === 1) {
            $this->makeDefault($address);
        }

        return back()->with('status', 'address-saved');
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $address->update($this->validated($request));

        if ($request->boolean('is_default')) {
            $this->makeDefault($address);
        }

        return back()->with('status', 'address-saved');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault && ($next = $request->user()->addresses()->first())) {
            $this->makeDefault($next);
        }

        return back()->with('status', 'address-deleted');
    }

    private function makeDefault(Address $address): void
    {
        Address::query()->where('user_id', $address->user_id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'min:8', 'max:30', 'regex:/^[0-9 +().-]+$/'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
