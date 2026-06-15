<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    /**
     * Show the password reset form.
     */
    public function create(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle a password reset request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // Log activity
                activity('auth')
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('Password Changed');
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Auto-login after password reset
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();
            }

            return redirect('/dashboard')->with('success', __($status));
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}
