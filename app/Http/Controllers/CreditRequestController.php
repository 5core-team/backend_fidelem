<?php

// app/Http/Controllers/CreditRequestController.php

namespace App\Http\Controllers;

use App\Models\CreditRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditRequestController extends Controller
{
  // Dans votre contrôleur Laravel
// Dans votre contrôleur Laravel
// Dans votre contrôleur Laravel
public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1000|max:100000',
            'duration' => 'required|integer|min:12|max:120',
            'purpose' => 'required|string',
            'additional_details' => 'nullable|string',
        ]);

        $userId = Auth::id(); // Ou $request->clientId si vous voulez forcer l'id

        $creditRequest = CreditRequest::create([
            'user_id' => $userId,
            'amount' => $validatedData['amount'],
            'duration' => $validatedData['duration'],
            'purpose' => $validatedData['purpose'],
            'additional_details' => $validatedData['additional_details'] ?? null,
        ]);

        return response()->json(['message' => 'Demande enregistrée avec succès', 'data' => $creditRequest], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de l\'enregistrement', 'error' => $e->getMessage()], 500);
    }
}


     public function index()
    {
        $creditRequests = CreditRequest::with(['user', 'user.advisor'])->where('user_id', Auth::id())->get();
        return response()->json($creditRequests);
    }

    
    public function getActiveCredits()
    {
        $activeCredits = CreditRequest::with(['user', 'user.advisor'])->where('user_id', Auth::id())
                                      ->where('status', 'Approuvée')
                                      ->get();
        return response()->json($activeCredits);
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:Approuvée,Rejetée',
        ]);

        $creditRequest = CreditRequest::findOrFail($id);
        $creditRequest->status = $validatedData['status'];
        $creditRequest->save();

        return response()->json($creditRequest);
    }
}