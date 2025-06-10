<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
  /*  //
  public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'type_compte' => 'required|string',
            'password' => 'required|string|min:8',
            'created_by' => 'nullable|integer', // Add created_by validation
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'type_compte' => $validatedData['type_compte'],
            'password' => Hash::make($validatedData['password']),
            'statut' => 'En attente',
            'created_by' => $validatedData['created_by'] ?? null, // Add created_by
        ]);

        return response()->json($user, 201);
    }
    */


    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'sometimes|string|max:20',
        'address' => 'sometimes|string|max:255',
        'type_compte' => 'required|string|in:advisor,user,manager',
        'password' => 'required|string|min:8',
        'created_by' => 'nullable|integer|exists:users,id'
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'last_name' => $validatedData['last_name'],
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'] ?? null,
        'address' => $validatedData['address'] ?? null,
        'type_compte' => $validatedData['type_compte'],
        'password' => Hash::make($validatedData['password']),
        'statut' => 'Actif', // Ou 'En attente' selon votre workflow
        'created_by' => $validatedData['created_by'] ?? null
    ]);

    return response()->json($user, 201);
}
}
