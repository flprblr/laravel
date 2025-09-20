<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();

        } catch (\Exception $_) {
            return redirect()->route('login', ['error' => 'Error al autenticar con Google.']);
        }

        if (! $this->validateAllowedDomain($googleUser->getEmail())) {

            $allowedDomain = config('services.google.allowed_domain');

            return redirect()->route('login', [
                'error' => "Solo se permite iniciar sesión con correos de los dominios: {$allowedDomain}",
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                // 'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->password) {
            $user->password = Hash::make($user->email);
            $user->save();
        }

        Auth::login($user);

        return redirect('/dashboard');
    }

    protected function validateAllowedDomain(string $email): bool
    {
        $allowedDomains = config('services.google.allowed_domain');

        if (! $allowedDomains) {
            return true;
        }

        $domainsArray = array_map('trim', explode(',', strtolower($allowedDomains)));

        $emailDomain = strtolower(trim(substr(strrchr($email, '@'), 1)));

        return in_array($emailDomain, $domainsArray, true);
    }
}
