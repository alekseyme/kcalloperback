<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Неверный логин или пароль',
            ], 400);
        }
        
        $token = $user->createToken($user->username.'_token')->plainTextToken;

        $cookie = cookie('stkn', $token, 60*24);

        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'projects' => [
                'in' => $user->role === 0 || $user->role === 1 ? Project::where('type', 0)->orderBy('name', 'ASC')->get() : $user->projects()->where('type', 0)->orderBy('name', 'ASC')->get(),
                'out' => $user->role === 0 || $user->role === 1 ? Project::where('type', 1)->orderBy('name', 'ASC')->get() : $user->projects()->where('type', 1)->orderBy('name', 'ASC')->get()
            ],
            'passwords' => [
                'infrapwd' => $user->infrapwds()->where('username', $user->username)->first() ? $user->infrapwds()->where('username', $user->username)->first()->password : null,
                'rocketpwd' => $user->rocketpwd,
                'teampasspwd' => $user->teampasspwd,
            ],
            'infralogins' => $user->infrapwds()->get()
        ], 200)->withCookie($cookie);
    }

    public function me()
    {
        return response()->json([
            'name' => auth()->user()->name,
            'username' => auth()->user()->username,
            'role' => auth()->user()->role,
            'projects' => [
                'in' => auth()->user()->role === 0 || auth()->user()->role === 1 ? Project::where('type', 0)->orderBy('name', 'ASC')->get() : auth()->user()->projects()->where('type', 0)->orderBy('name', 'ASC')->get(),
                'out' => auth()->user()->role === 0 || auth()->user()->role === 1 ? Project::where('type', 1)->orderBy('name', 'ASC')->get() : auth()->user()->projects()->where('type', 1)->orderBy('name', 'ASC')->get()
            ],
            'passwords' => [
                'infrapwd' => auth()->user()->infrapwds()->where('username', auth()->user()->username)->first() ? auth()->user()->infrapwds()->where('username', auth()->user()->username)->first()->password : null,
                'rocketpwd' => auth()->user()->rocketpwd,
                'teampasspwd' => auth()->user()->teampasspwd,
            ],
            'infralogins' => auth()->user()->infrapwds()->get()
        ]);
        // return auth()->user();
    }

    public function logout()
    {
        // auth()->user()->tokens()->delete();
        $cookie = Cookie::forget('stkn');

        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Успешный выход'
        ], 200)->withCookie($cookie);
    }
}
