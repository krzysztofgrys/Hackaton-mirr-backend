<?php

namespace App\Http\Controllers;

use App\User;
use Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->post('email'))->firstOrFail();
        if (!Hash::check($request->post('password'), $user->password)) {
            abort(403);
        }

        return $user;
    }
}
