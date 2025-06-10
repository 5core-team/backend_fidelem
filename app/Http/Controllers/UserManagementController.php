<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\CreditRequest;
use Illuminate\Support\Facades\Auth;


class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type_compte');
        if ($type) {
            $users = User::where('type_compte', $type)->get();
        } else {
            $users = User::all();
        }
        return response()->json($users);
    }

    public function getUserStatsAdvisor()
    {
        $totalUsers = User::where('type_compte', 'user')->count();
        $pendingUsers = User::where('type_compte', 'user')->where('statut', 'En attente')->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'pendingUsers' => $pendingUsers,
        ]);
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->statut = 'Actif';
        $user->save();

        return response()->json(['message' => 'Utilisateur approuvé avec succès']);
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->statut = 'Rejeté';
        $user->save();

        return response()->json(['message' => 'Utilisateur rejeté avec succès']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }



public function getCreditStats()
{
    // Récupérer les statistiques de crédit depuis la base de données
    $creditStatsData = CreditRequest::select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('SUM(amount) as total_amount'),
        DB::raw('COUNT(*) as total_requests')
    )
    ->groupBy('month')
    ->orderBy('month')
    ->get()
    ->map(function ($item) {
        $monthNames = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];
        return [
            'name' => $monthNames[$item->month],
            'Montant' => $item->total_amount,
            'Demandes' => $item->total_requests
        ];
    });

    return response()->json($creditStatsData);
}

    public function getUserStats()
    {
        $totalUsers = User::count();
        $totalAdvisors = User::where('type_compte', 'advisor')->count();
        $pendingAccounts = User::where('statut', 'En attente')->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalAdvisors' => $totalAdvisors,
            'pendingAccounts' => $pendingAccounts
        ]);
    }


    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $user->update([
            'name' => $validatedData['firstName'],
            'last_name' => $validatedData['lastName'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6|confirmed', // Laravel attend newPassword_confirmation
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        if (!Hash::check($validatedData['currentPassword'], $user->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect'], 401);
        }

        $user->update([
            'password' => Hash::make($validatedData['newPassword']),
        ]);

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }
}
