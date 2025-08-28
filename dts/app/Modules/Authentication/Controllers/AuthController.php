<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Requests\LoginRequest;
use App\Modules\Authentication\Requests\RegisterRequest;
use App\Modules\Authentication\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function loginPost(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());
        
        if ($result['success']) {
            return redirect()->route('docuReg')->with('success', 'Welcome!');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function registrationPost(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());
        
        if ($result['success']) {
            return redirect()->route('login')->with('success', 'Registration successful!');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}