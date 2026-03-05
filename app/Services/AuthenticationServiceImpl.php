<?php

namespace App\Services;

use App\Models\User;
use App\Models\Token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class AuthenticationServiceImpl implements AutheticationService
{
    public function register(array $request): array
    {
        // 1. Save User (Laravel handles the 'builder' logic via create)
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'], // Hashing handled by User model mutator
        ]);

        // 2. Trigger Initial Token Generation (matches your resend logic)
        $this->resendVerificationToken($user->email);

        // 3. Generate Token (Equivalent to JwtService)
        return [
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function verifyUser(string $email, string $token)
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) throw new Exception("User not found");
        if ($user->is_enabled) throw new Exception("Account is already verified");

        // Match your Spring logic: findByTokenAndUserId
        $storedToken = Token::where('token', $token)
            ->where('userId', $user->id)
            ->first();

        if (!$storedToken || $storedToken->created_at->isBefore(now()->subHour())) {
            return response()->json("Token has expired. please try again with new token", 400);
        }

        $user->is_enabled = true;
        $user->save();

        $storedToken->delete();

        return response()->json("Account verified successfully! You can now log in.");
    }

    public function resendVerificationToken(string $email)
    {
        $user = User::where('email', strtolower(trim($email)))->firstOrFail();

        if ($user->is_enabled) {
            return response()->json("Account is already verified.", 400);
        }

        // Equivalent to tokenService.deleteByUserId
        Token::where('userId', $user->id)->delete();

        // Secure Random 4-digit code
        $newToken = (string) random_int(1000, 9999);

        Token::create([
            'token' => $newToken,
            'userId' => $user->id,
            'created_at' => now(),
        ]);

        try {
            // Logic for EmailService would go here
            // Mail::to($user->email)->send(new \App\Mail\VerificationMail($newToken));
        } catch (Exception $e) {
            return response()->json("Error sending email. Please try again.", 500);
        }

        return response()->json("A new 4-digit verification code has been sent to your email.");
    }

    public function authenticate(array $request): array
    {
        $user = User::where('email', $request['email'])->firstOrFail();

        if (!$user->is_enabled) {
            throw new Exception("Please verify your account before logging in.");
        }

        if (!Hash::check($request['password'], $user->password)) {
            throw new Exception("Invalid credentials");
        }

        return [
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }
}
