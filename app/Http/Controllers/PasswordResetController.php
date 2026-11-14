<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PasswordResetController extends Controller
{
    private const CODE_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    public function sendCode(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email|max:255']);
        $email = strtolower($validated['email']);
        $rateKey = 'password-reset-request:'.$request->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            return response()->json(['message' => 'Please wait before requesting another code.'], 429);
        }

        RateLimiter::hit($rateKey, 600);
        $user = User::where('email', $email)->first();

        // Do not disclose whether a particular email is registered.
        $success = ['message' => 'If that email is registered, a verification code has been sent.'];
        if (! $user) {
            return response()->json($success);
        }

        $apiKey = config('services.brevo.api_key');
        $fromAddress = config('services.brevo.from_address');
        if (! $apiKey || ! $fromAddress) {
            Log::error('Brevo password reset email is not configured.');
            return response()->json(['message' => 'Password reset email is not configured. Contact your administrator.'], 503);
        }

        $code = (string) random_int(100000, 999999);
        $response = Http::timeout(10)
            ->acceptJson()
            ->withHeaders(['api-key' => $apiKey])
            ->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'email' => $fromAddress,
                    'name' => config('services.brevo.from_name'),
                ],
                'to' => [[
                    'email' => $user->email,
                    'name' => $user->name,
                ]],
                'subject' => 'Your password reset verification code',
                'htmlContent' => '<p>Hello '.e($user->name).',</p><p>Your verification code is:</p><p style="font-size:24px;font-weight:bold;letter-spacing:4px">'.$code.'</p><p>This code expires in '.self::CODE_TTL_MINUTES.' minutes. If you did not request it, you can safely ignore this email.</p>',
                'textContent' => "Hello {$user->name},\n\nYour password reset verification code is: {$code}\n\nThis code expires in ".self::CODE_TTL_MINUTES.' minutes.',
            ]);

        if ($response->failed()) {
            Log::warning('Brevo password reset email failed.', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['message' => 'Unable to send the verification email. Please try again later.'], 502);
        }

        Cache::put($this->codeKey($email), ['code_hash' => Hash::make($code), 'attempts' => 0], now()->addMinutes(self::CODE_TTL_MINUTES));

        return response()->json($success);
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower($validated['email']);
        $stored = Cache::get($this->codeKey($email));
        if (! $stored || ($stored['attempts'] ?? 0) >= self::MAX_ATTEMPTS || ! Hash::check($validated['code'], $stored['code_hash'])) {
            if ($stored) {
                $stored['attempts'] = ($stored['attempts'] ?? 0) + 1;
                Cache::put($this->codeKey($email), $stored, now()->addMinutes(self::CODE_TTL_MINUTES));
            }
            return response()->json(['message' => 'The verification code is invalid or has expired.'], 422);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return response()->json(['message' => 'The verification code is invalid or has expired.'], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);
        $user->tokens()->delete();
        Cache::forget($this->codeKey($email));

        return response()->json(['message' => 'Password reset successfully. You can now sign in.']);
    }

    private function codeKey(string $email): string
    {
        return 'password-reset-code:'.hash('sha256', $email);
    }
}
