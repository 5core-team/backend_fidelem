<?php

namespace App\Http\Controllers;

use App\Models\CreditRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditRequestController extends Controller
{
  
public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1000|max:100000',
            'duration' => 'required|integer|min:12|max:120',
            'purpose' => 'required|string',
            'additional_details' => 'nullable|string',
        ]);

        $userId = Auth::id(); 

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
 
public function getCreditRequestsByAdvisor(Request $request)
{
    $advisorId = $request->user()->id;
    
    $creditRequests = CreditRequest::with(['user' => function($query) use ($advisorId) {
                        $query->where('created_by', $advisorId);
                      }])
                      ->whereHas('user', function($query) use ($advisorId) {
                        $query->where('created_by', $advisorId);
                      })
                      ->get();
    
    return response()->json($creditRequests);
}

public function getAdvisorStats(Request $request)
{
    $advisorId = $request->user()->id;
    
    $totalUsers = User::where('role', 'user')
                     ->where('created_by', $advisorId)
                     ->count();
                     
    $pendingUsers = User::where('role', 'user')
                       ->where('created_by', $advisorId)
                       ->where('statut', 'pending')
                       ->count();
                       
    $pendingRequests = CreditRequest::whereHas('user', function($query) use ($advisorId) {
                          $query->where('created_by', $advisorId);
                       })
                       ->where('status', 'pending')
                       ->count();
                       
    $approvedRequests = CreditRequest::whereHas('user', function($query) use ($advisorId) {
                           $query->where('created_by', $advisorId);
                        })
                        ->where('status', 'approved')
                        ->count();
    
    return response()->json([
        'totalUsers' => $totalUsers,
        'pendingUsers' => $pendingUsers,
        'pendingRequests' => $pendingRequests,
        'approvedRequests' => $approvedRequests,
    ]);
}

public function index(Request $request)
{
    $userId = $request->query('userId');

    $creditRequests = CreditRequest::with(['user'])
        ->whereHas('user', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
        ->get();

    return response()->json($creditRequests);
}

    
}