<?php

namespace App\Modules\Authentication\Services;

use App\Modules\Authentication\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthService
{
    public function login($credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Session::put('user_data', $user);
            
            return [
                'success' => true,
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    public function apiLogin($credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    public function register($userData)
    {
        try {
            $userData['password'] = Hash::make($userData['password']);
            $userData['roleID'] = $userData['roleID'] ?? 2; // Default role
            
            $user = User::create($userData);

            return [
                'success' => true,
                'user' => $user
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    public function apiRegister($userData)
    {
        $result = $this->register($userData);
        
        if ($result['success']) {
            $token = $result['user']->createToken('auth-token')->plainTextToken;
            $result['token'] = $token;
        }
        
        return $result;
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
    }

    public function apiLogout($user)
    {
        $user->currentAccessToken()->delete();
    }
}