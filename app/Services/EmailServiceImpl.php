<?php

namespace App\Services;

use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

class EmailServiceImpl implements EmailService
{
    public function sendVerificationEmail(string $toEmail, string $token): void
    {
        // By using 'queue', Laravel returns the response immediately 
        // while the email sends in the background.
        Mail::to($toEmail)->queue(new VerificationMail($token));
    }
}
