<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::ordered()->get();
    }

    public function store(Request $request)
    {
        if (PaymentMethod::count() >= ContentCaps::MAX_PAYMENT_METHODS) {
            return response()->json(['error' => 'You can only have '.ContentCaps::MAX_PAYMENT_METHODS.' active payment networks at a time. Remove one before adding another.'], 422);
        }

        $data = $request->validate([
            'network' => 'required|in:'.implode(',', PaymentMethod::NETWORKS),
            'lipa_number' => 'required|string|max:30',
            'account_name' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
        ]);

        if (PaymentMethod::where('network', $data['network'])->exists()) {
            return response()->json(['error' => PaymentMethod::LABELS[$data['network']].' is already configured. Edit it instead of adding it again.'], 422);
        }

        // First network added is automatically primary — there's always exactly
        // one "on top" network once at least one exists, never zero and never two.
        $data['is_primary'] = PaymentMethod::count() === 0 ? true : (bool) ($data['is_primary'] ?? false);
        if ($data['is_primary']) {
            PaymentMethod::query()->update(['is_primary' => false]);
        }

        return response()->json(PaymentMethod::create($data), 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'lipa_number' => 'sometimes|string|max:30',
            'account_name' => 'nullable|string|max:100',
            'is_primary' => 'sometimes|boolean',
        ]);

        // Making this one primary means every other one stops being primary —
        // that's the entire mechanism behind "clients only see Mpesa on top".
        if (! empty($data['is_primary'])) {
            PaymentMethod::where('id', '!=', $paymentMethod->id)->update(['is_primary' => false]);
        }

        $paymentMethod->update($data);
        return response()->json($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $wasPrimary = $paymentMethod->is_primary;
        $paymentMethod->delete();

        // If the primary network was just removed and one network is left,
        // promote it automatically so there's never a moment with no primary
        // while a network is actually configured.
        if ($wasPrimary) {
            PaymentMethod::query()->orderBy('id')->first()?->update(['is_primary' => true]);
        }

        return response()->json(['ok' => true]);
    }
}
