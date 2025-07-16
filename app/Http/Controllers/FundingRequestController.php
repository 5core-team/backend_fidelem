<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundingRequest;
use Illuminate\Support\Facades\Storage;

class FundingRequestController extends Controller
{

    public function index()
{
    $fundingRequests = FundingRequest::all();
    return response()->json($fundingRequests);
}

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
    $path = $request->file('businessPlan')->store('public/uploads');
    $validatedData['businessPlan'] = str_replace('public/', 'storage/', $path);
}


        $fundingRequest = FundingRequest::create($validatedData);

        return response()->json($fundingRequest, 201);
    }


public function destroy($id)
{
    $fundingRequest = FundingRequest::findOrFail($id);
    $fundingRequest->delete();

    return response()->json(['message' => 'Demande de levée de fonds supprimée avec succès'], 200);
}

}