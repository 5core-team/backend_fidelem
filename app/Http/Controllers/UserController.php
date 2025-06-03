<?php

namespace App\Http\Controllers;
use App\Models\User;
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

}
