<?php

namespace App\Http\Controllers;

use App\User;
use Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->post('login'))->where('password', Hash::make($request->post('password')))->first();
        if (!$user) {
            abort(403);
        }
        return $user;
    }
}
