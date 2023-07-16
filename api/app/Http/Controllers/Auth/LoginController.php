<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {
        $request->validated();

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('home')->with('success','Signed in');
        }

        return redirect("auth/login")->with('success','Login details are not valid');
    }

}
