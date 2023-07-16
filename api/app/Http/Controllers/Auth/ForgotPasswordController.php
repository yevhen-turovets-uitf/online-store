<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('password.forgot');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Auth\ForgotRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ForgotRequest $request)
    {
        $request->validated();

        Password::broker()->sendResetLink(['email' => $request->get('email')]);

        return back()->with('message', __('passwords.sent'));
    }
}
