<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('auth.registration');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);;

        Auth::login($user);

        return redirect('home')->with('success', "Account successfully registered.");
    }
}
