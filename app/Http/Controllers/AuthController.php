<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            return response()->json(['error' => 'Provider not supported.'], 400);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from the provider.
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            return response()->json(['error' => 'Provider not supported.'], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed.'], 401);
        }

        $user = User::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // Check if a user with this email already exists
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                // Link the provider to the existing user
                $user = $existingUser;
                $user->update([
                    'provider_name' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar_url' => $socialUser->getAvatar(),
                ]);
            } else {
                // Auto-create user on OAuth callback
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'provider_name' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar_url' => $socialUser->getAvatar(),
                    // Default role is "Team Member" (assuming ID 3 based on migrations)
                    'role_id' => 3, 
                ]);
            }
        }

        // Issue a Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log the user into the web session so Blade @auth directives work
        Auth::login($user);

        // Redirect back to the SPA with the token
        return redirect('/?token=' . $token);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()?->delete();
        }
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
