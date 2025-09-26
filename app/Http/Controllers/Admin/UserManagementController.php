<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $users = User::query()
            ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_ADVISER])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function edit(User $user)
    {
        abort_unless(in_array($user->role, [User::ROLE_STUDENT, User::ROLE_ADVISER], true), 404);

        $roles = [
            User::ROLE_STUDENT => 'Student',
            User::ROLE_ADVISER => 'Adviser',
        ];

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless(in_array($user->role, [User::ROLE_STUDENT, User::ROLE_ADVISER], true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_STUDENT, User::ROLE_ADVISER])],
        ]);

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }
}
