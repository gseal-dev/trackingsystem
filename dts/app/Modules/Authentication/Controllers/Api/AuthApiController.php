<?php

namespace App\Modules\Authentication\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Requests\LoginRequest;
use App\Modules\Authentication\Requests\RegisterRequest;
use App\Modules\Authentication\Services\AuthService;
use App\Modules\Authentication\Resources\UserResource;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->apiLogin($request->validated());
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'token' => $result['token'],
                'user' => new UserResource($result['user'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 401);
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->apiRegister($request->validated());
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'token' => $result['token'],
                'user' => new UserResource($result['user'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 422);
    }

    public function logout(Request $request)
    {
        $this->authService->apiLogout($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => new UserResource($request->user())
        ]);
    }
}