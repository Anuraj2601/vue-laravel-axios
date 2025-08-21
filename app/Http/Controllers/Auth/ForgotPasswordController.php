<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send reset link to the user's email.
     */
    public function sendResetLink(Request $request) {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            ResetPassword::createUrlUsing(function($notifiable, string $token) {
                $host = request()->getHost();
                $port = request()->getPort();
                return "http://{$host}:{$port}/reset-password?token={$token}&email=". urlencode($notifiable->getEmailForPasswordReset());
            });

            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                    ? response()->json(['status' => __($status)])
                    : response()->json(['email' => __($status)], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error',
                'email' => $th->getMessage()
            ]);
        }
    }

    /**
     * Handle resetting password
     */
    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        $status = Password::reset(
            $request->only('email','password', 'password_confirmation', 'token'),
            function(User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                            ? response()->json(['status' => __($status)])
                            : response()->json(['email' => __($status)], 400);
    }
}
