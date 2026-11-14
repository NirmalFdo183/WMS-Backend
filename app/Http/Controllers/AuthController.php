<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|string|in:super_admin,admin,data_entry,cashier',
        ]);

        $requestedRole = $validatedData['role'] ?? 'cashier';
        if (! $request->user()->isSuperAdmin() && ! in_array($requestedRole, ['data_entry', 'cashier'], true)) {
            abort(403, 'Administrators can manage data-entry users and cashiers only.');
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'] ?? null,
            'password' => Hash::make($validatedData['password']),
            'role' => $requestedRole,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // The login field may contain a username, phone number, or email address.
        $identifier = $validated['username'];
        $loginField = str_contains($identifier, '@')
            ? 'email'
            : (preg_match('/^\+?[0-9\s()-]+$/', $identifier) ? 'phone' : 'username');
        $credentials = [
            $loginField => $identifier,
            'password' => $validated['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where($loginField, $identifier)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
