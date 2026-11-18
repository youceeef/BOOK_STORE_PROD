<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str; // For potential code generation

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percent',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === 'percent' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                },
            ],
            'min_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date|after_or_equal:today',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['expires_at'])->endOfDay();
        }

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === 'percent' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                },
            ],
            'min_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date|after_or_equal:today',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['expires_at'])->endOfDay();
        }

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        // Maybe add checks here if coupon was used before deleting?
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
