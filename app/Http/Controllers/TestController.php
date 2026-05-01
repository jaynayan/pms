<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    /**
     * Show the test login form.
     */
    public function showLoginForm()
    {
        // Return a simple HTML page with the form
        return response(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Test Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f3f4f6; margin: 0; }
        .container { background-color: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-top: 0; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Test Login</h2>
        <form action="/test/login" method="POST">
            <!-- Note: CSRF token is necessary for POST requests in Laravel web middleware -->
            <input type="hidden" name="_token" value="{$this->csrfToken()}">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
HTML
        );
    }

    /**
     * Get the CSRF token.
     */
    protected function csrfToken()
    {
        return csrf_token();
    }

    /**
     * Handle the test login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $email = $request->input('email');
        $name = $request->input('name');

        // Check if user exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Auto-create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'provider_name' => 'test', // indicate it's a test user
                'provider_id' => 'test_' . time(),
                'role_id' => config('pms.roles.VIEWER', 4), // Default to global Viewer, no project access
            ]);
        }

        // Issue a Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log the user into the web session
        Auth::login($user);

        // Redirect back to the SPA with the token exactly like OAuth
        return redirect('/?token=' . $token);
    }
}
