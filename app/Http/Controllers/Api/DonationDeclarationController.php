<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationDeclaration;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class DonationDeclarationController extends Controller
{
    // POST /api/donations/declare — unauthenticated, called by any donor who says
    // "I've sent my payment". This never confirms money moved; it just gives staff
    // something to check against the real mobile money statement.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'amount' => 'required|string|max:20',
            'network' => 'required|in:'.implode(',', PaymentMethod::NETWORKS),
            'reference' => 'nullable|string|max:40',
        ]);
        DonationDeclaration::create($data);
        return response()->json(['ok' => true], 201);
    }

    // GET /api/donation-declarations — admin-only, so staff can see what's been
    // declared and tick items off as reconciled against the real statement.
    public function index()
    {
        return DonationDeclaration::orderByDesc('created_at')->get();
    }

    public function update(Request $request, DonationDeclaration $donationDeclaration)
    {
        $data = $request->validate(['reconciled' => 'required|boolean']);
        $donationDeclaration->update($data);
        return response()->json($donationDeclaration);
    }
}
