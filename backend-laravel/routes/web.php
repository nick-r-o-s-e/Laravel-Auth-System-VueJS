<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return Response([
            'message' => 'Successful login!'
        ], 200);
    }

    return Response([
        'message' => 'Mismatch email and password!'
    ], 401);
});
