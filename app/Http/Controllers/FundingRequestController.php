<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundingRequest;
use Illuminate\Support\Facades\Storage;

class FundingRequestController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'companyName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'mission' => 'required|string',
            'vision' => 'required|string',
            'sector' => 'required|string|max:255',
            'productDescription' => 'required|string',
            'productStatus' => 'required|string|in:idées,prototype,sur le marché',
            'amountRequested' => 'required|numeric',
            'useOfFunds' => 'required|string',
            'businessPlan' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        if ($request->hasFile('businessPlan')) {
            $validatedData['businessPlan'] = $request->file('businessPlan')->store('uploads', 'public');
        }

        $fundingRequest = FundingRequest::create($validatedData);

        return response()->json($fundingRequest, 201);
    }
}
