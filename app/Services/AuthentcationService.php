<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;

interface AuthenticationService
{
    /**
     * @param string $email
     * @param string $otpCode
     * @return JsonResponse
     */
    public function verifyUser(string $email, string $otpCode): JsonResponse;

    /**
     * @param string $email
     * @return JsonResponse
     */
    public function resendVerificationToken(string $email): JsonResponse;

    /**
     * Equivalent to AuthenticationResponseDto (Returns array with token)
     * @param array $request (Matches AuthenticationRequestDto)
     * @return array
     */
    public function authenticate(array $request): array;

    /**
     * Equivalent to AuthenticationResponseDto (Returns array with token)
     * @param array $request (Matches RegisterRequestDto)
     * @return array
     */
    public function register(array $request): array;
}
