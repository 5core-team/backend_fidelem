<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->statut === 'Rejeté') {
                return response()->json(['message' => 'Votre compte a été rejeté.'], 403);
            }

            if ($user->statut === 'En attente') {
                return response()->json(['message' => 'Votre compte est en attente de validation.'], 403);
            }

            $token = $user->createToken('AuthToken')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'address' => $user->address,
                    'phone' => $user->phone,
                    'role' => $user->type_compte,
                    'created_by' => $user->created_by,
                ]
            ], 200);
        }

        return response()->json(['message' => 'Identifiants invalides'], 401);
    }
}
