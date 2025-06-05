<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\CreditRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function validateUser($userId)
{
    $user = User::findOrFail($userId);
    $user->statut = 'Actif';
    $user->save();

    return response()->json($user, 200);
}



public function getClientsByAdvisor($advisorId)
{
    $clients = User::where('type_compte', 'user')
                  ->where('created_by', $advisorId)
                  ->get();
    
    return response()->json($clients);
}

public function getAdvisorStats($advisorId)
{
    $totalUsers = User::where('type_compte', 'user')
                     ->where('created_by', $advisorId)
                     ->count();
                     
    $pendingUsers = User::where('type_compte', 'user')
                       ->where('created_by', $advisorId)
                       ->where('statut', 'En attente')
                       ->count();
                       
    $pendingRequests = CreditRequest::whereHas('user', function($query) use ($advisorId) {
                          $query->where('created_by', $advisorId);
                       })
                       ->where('status', 'En attente')
                       ->count();
                       
    $approvedRequests = CreditRequest::whereHas('user', function($query) use ($advisorId) {
                           $query->where('created_by', $advisorId);
                        })
                        ->where('status', 'Approuvée')
                        ->count();
    
    return response()->json([
        'totalUsers' => $totalUsers,
        'pendingUsers' => $pendingUsers,
        'pendingRequests' => $pendingRequests,
        'approvedRequests' => $approvedRequests,
    ]);
}
}
