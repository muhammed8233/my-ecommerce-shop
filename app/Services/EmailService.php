<?php

namespace App\Services;

interface EmailService
{
    /**
     * @param string $toEmail
     * @param string $token
     * @return void
     */
    public function sendVerificationEmail(string $toEmail, string $token): void;
}
