<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display users the current manager is allowed to manage.
     */
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'username', 'email', 'phone', 'address', 'role', 'created_at');

        $manageableRoles = $request->user()->isSuperAdmin()
            ? ['super_admin', 'admin', 'data_entry', 'cashier']
            : ['data_entry', 'cashier'];

        if ($request->has('role') && in_array($request->role, $manageableRoles, true)) {
            $query->where('role', $request->role);
        } else {
            $query->whereIn('role', $manageableRoles);
        }

        $users = $query->withCount('sales')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user within the manager's role boundary.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'phone' => 'required|string|max:255|unique:users,phone',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:super_admin,admin,data_entry,cashier',
        ]);

        $this->ensureRoleCanBeManaged($request, $validated['role']);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::withCount('sales')->findOrFail($id);
        $this->ensureUserCanBeManaged(request(), $user);
        return response()->json($user);
    }

    /**
     * Update the specified user details.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureUserCanBeManaged($request, $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'role' => 'required|string|in:super_admin,admin,data_entry,cashier',
            'password' => 'nullable|string|min:8',
        ]);

        $this->ensureRoleCanBeManaged($request, $validated['role']);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json($user);
    }

    /**
     * Update user password specifically.
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureUserCanBeManaged($request, $user);

        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->id == $id) {
            return response()->json([
                'message' => 'You cannot delete your own logged-in administrator account.',
            ], 400);
        }

        $user = User::findOrFail($id);
        $this->ensureUserCanBeManaged($request, $user);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    private function ensureRoleCanBeManaged(Request $request, string $role): void
    {
        if ($request->user()->isSuperAdmin() || in_array($role, ['data_entry', 'cashier'], true)) {
            return;
        }

        abort(403, 'Administrators can manage data-entry users and cashiers only.');
    }

    private function ensureUserCanBeManaged(Request $request, User $user): void
    {
        $this->ensureRoleCanBeManaged($request, $user->role);
    }
}
