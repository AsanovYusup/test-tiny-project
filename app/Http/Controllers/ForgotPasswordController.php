<?php

namespace App\Http\Controllers;

use App\Services\ForgotPasswordService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\PasswordGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{

    private PasswordGeneratorService $passwordGenerator;

    public function __construct(PasswordGeneratorService $passwordGenerator)
    {
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request, ForgotPasswordService $forgotPasswordService)
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Get the user with the specified email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // User not found
            return redirect()->back()->withErrors(['email' => 'No account found with that email address.']);
        }

        // Generate a new random password for the user
        $newPassword = $this->passwordGenerator->generatePassword();

        try {
            DB::transaction(function () use ($user, $newPassword, $forgotPasswordService) {
                // Update the user's password with the new password
                $user->password = bcrypt($newPassword);
                $user->save();

                // Send an email to the user with the new password
                $forgotPasswordService->sendPasswordResetEmail($user, $newPassword);
            });
        } catch (\Exception $e) {
            // Log the exception message
            Log::error($e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the password reset email. Please try again later.']);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'An email has been sent to the address you provided with instructions on how to reset your password.');
    }
}
