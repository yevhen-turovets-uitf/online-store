<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('password.reset');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResetPasswordRequest $request)
    {
        $request->validated();

        $response = Password::broker()->reset([
                'email' => $request->get('email'),
                'password' => $request->get('password'),
                'password_confirmation' => $request->get('password_confirmation'),
                'token' => $request->get('token'),
            ],
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                event(new PasswordReset($user));
        });

        if ($response == Password::INVALID_TOKEN || $response != Password::PASSWORD_RESET) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        return redirect('auth/login')->with('message', 'Your password has been changed!');
    }
}
