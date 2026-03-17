<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $settings = Settings::first();
        return view('auth.forgot-password', [
            'page'     => 'Reset Password',
            'selected' => 'Reset Password',
            'title'    => 'Reset Password',
            'setting'  => $settings,
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak ditemukan'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __($status));
    }
}
