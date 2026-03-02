<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthenticationController extends Controller
{
    // Constructor Injection (Equivalent to @RequiredArgsConstructor)
    public function __construct(
        protected AuthenticationService $authenticationService
    ) {}

    /**
     * POST /api/v1/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        // Validation (Equivalent to @Valid RegisterRequestDto)
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return response()->json($this->authenticationService->register($validated));
    }

    /**
     * POST /api/v1/auth/login
     */
    public function authenticate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        return response()->json($this->authenticationService->authenticate($validated));
    }

    /**
     * POST /api/v1/auth/verify-token
     */
    public function verifyUser(Request $request): JsonResponse
    {
        $email = $request->query('email');
        $token = $request->query('token');

        $result = $this->authenticationService->verifyUser($email, $token);
        
        return response()->json($result);
    }

    /**
     * POST /api/v1/auth/resend-token
     */
    public function resendToken(Request $request): JsonResponse
    {
        $email = $request->query('email');
        
        $result = $this->authenticationService->resendVerificationToken($email);

        return response()->json($result);
    }
}
