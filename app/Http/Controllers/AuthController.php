<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            return response()->json(['error' => 'Provider not supported.'], 400);
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Obtain the user information from the provider.
     */
    public function callback(string $provider): JsonResponse
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

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
