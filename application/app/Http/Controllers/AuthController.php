<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->where('password', $request->password)
            ->first();

        $user->token = $user->getJWTToken();
        $user->save();

        return response()->json(['token' => $user->token]);
    }
}


