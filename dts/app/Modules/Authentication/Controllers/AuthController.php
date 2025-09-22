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

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($credentials);

        if ($result['success']) {
            $user = $result['user'];
            // Redirect based on role
            if ($user->roleID == 1) { // Admin
                return redirect()->route('admin.dashboard');
            } elseif ($user->roleID == 2) { // Document Owner
                return redirect()->route('owner.dashboard');
            } elseif ($user->roleID == 3) { // Staff
                return redirect()->route('staff.dashboard');
            } else {
                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors(['email' => $result['message']]);
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
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