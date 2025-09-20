<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AppleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('apple')->redirect();
    }

    public function callback()
    {
        try {
            /** @var \SocialiteProviders\Apple\Provider $driver */
            $driver = Socialite::driver('apple');
            $appleUser = $driver->stateless()->user();

        } catch (\Exception $_) {
            return redirect()->route('login', ['error' => 'Error al autenticar con Apple.']);
        }

        if (! $appleUser->getEmail()) {
            return redirect()->route('login', [
                'error' => 'Apple no entregó email. Autoriza compartir tu email con la app.',
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => $appleUser->getEmail()],
            [
                'name' => $appleUser->getName() ?: $appleUser->getEmail(),
                'apple_id' => $appleUser->getId(),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->password) {
            $user->password = Hash::make(bin2hex(random_bytes(32)));
            $user->save();
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}
