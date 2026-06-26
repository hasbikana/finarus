<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        $driver = Socialite::driver('google');

        if (Auth::check()) {
            $driver->scopes(['https://www.googleapis.com/auth/gmail.readonly'])
                   ->with(['access_type' => 'offline', 'prompt' => 'consent']);
        } else {
            // Force account chooser so users can switch Google accounts on login
            $driver->with(['prompt' => 'select_account']);
        }

        return $driver->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth error', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'state' => request()->input('state'),
                'code' => request()->input('code'),
                'error' => request()->input('error'),
                'error_description' => request()->input('error_description'),
            ]);

            $message = request()->input('error_description')
                ?? 'Gagal login dengan Google. Silakan coba lagi.';

            return redirect()->route('login')->with('error', $message);
        }

        if (Auth::check()) {
            return $this->handleConnectFlow($googleUser);
        }

        return $this->handleLoginFlow($googleUser);
    }

    protected function handleConnectFlow($googleUser): RedirectResponse
    {
        $user = Auth::user();

        UserOAuthToken::updateOrCreate(
            ['user_id' => $user->id, 'provider' => 'google'],
            [
                'access_token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
                'expires_at' => $googleUser->expiresIn ? now()->addSeconds($googleUser->expiresIn) : null,
                'email' => $googleUser->getEmail(),
                'scopes' => 'gmail.readonly',
            ]
        );

        $settings = $user->settings ?? UserSetting::create(['user_id' => $user->id]);
        $settings->email_fetch_enabled = true;
        $settings->save();

        Log::info('Google Gmail connected', ['user_id' => $user->id]);

        return redirect()->route('pengaturan')->with('success', 'Google berhasil dihubungkan! Email fetching aktif.');
    }

    protected function handleLoginFlow($googleUser): RedirectResponse
    {
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser);
            request()->session()->regenerate();
            Log::info('Google login', ['user_id' => $existingUser->id]);
            return redirect()->intended(route('dashboard'));
        }

        $user = User::create([
            'name' => $googleUser->getName() ?? $googleUser->getEmail(),
            'email' => $googleUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);

        UserSetting::create(['user_id' => $user->id]);

        $user->accounts()->create([
            'name' => 'Cash / Dompet',
            'provider' => 'Cash',
            'type' => 'cash',
            'balance' => 0,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        Log::info('Google register + login', ['user_id' => $user->id]);

        return redirect()->route('dashboard')->with('success', 'Selamat datang di Finarus, ' . $user->name . '!');
    }

    public function disconnect(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        try {
            UserOAuthToken::where('user_id', $user->id)->where('provider', 'google')->delete();

            $settings = $user->settings;
            if ($settings) {
                $settings->email_fetch_enabled = false;
                $settings->save();
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Google berhasil diputuskan.']);
            }
            return redirect()->route('pengaturan')->with('success', 'Google berhasil diputuskan.');
        } catch (\Exception $e) {
            Log::error('Google disconnect error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Google berhasil diputuskan.']);
            }
            return redirect()->route('pengaturan')->with('success', 'Google berhasil diputuskan.');
        }
    }

    public function status(): JsonResponse
    {
        $user = Auth::user();
        $token = UserOAuthToken::where('user_id', $user->id)->where('provider', 'google')->first();

        return response()->json([
            'connected' => $token !== null,
            'email' => $token?->email,
            'email_fetch_enabled' => $user->settings?->email_fetch_enabled ?? false,
            'expires_at' => $token?->expires_at,
        ]);
    }

    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $client = new GoogleClient(['client_id' => config('services.google.client_id')]);

        try {
            $payload = $client->verifyIdToken($request->id_token);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'id_token' => ['Token Google tidak valid.'],
            ]);
        }

        if (!$payload) {
            throw ValidationException::withMessages([
                'id_token' => ['Token Google tidak valid.'],
            ]);
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? $email;

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        $isNew = $user->wasRecentlyCreated;

        if ($isNew) {
            UserSetting::create(['user_id' => $user->id]);
            $user->accounts()->create([
                'name' => 'Cash / Dompet',
                'provider' => 'Cash',
                'type' => 'cash',
                'balance' => 0,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => $isNew ? 'Registrasi berhasil' : 'Login berhasil',
            'user' => $user,
            'token' => $token,
        ], $isNew ? 201 : 200);
    }
}
