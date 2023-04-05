<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class ForgotPasswordService
{
    /**
     * The Mailer instance.
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Create a new ForgotPasswordService instance.
     *
     * @param Mailer $mailer The Mailer instance.
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a password reset email to the user.
     *
     * @param User $user The user to send the email to.
     * @param string $newPassword The new password for the user.
     *
     * @return void
     */
    public function sendPasswordResetEmail(User $user, $newPassword)
    {
        $this->mailer->send('emails.password_reset', ['user' => $user, 'password' => $newPassword], function (Message $message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset');
        });
    }
}
