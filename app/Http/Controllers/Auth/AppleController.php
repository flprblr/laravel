<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AppleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('apple')->redirect();
    }

    public function callback()
    {
        $appleUser = Socialite::driver('apple')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $appleUser->getEmail()],
            [
                'name' => $appleUser->getName(),
                'apple_id' => $appleUser->getId(),
                'avatar' => $appleUser->getAvatar(),
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
