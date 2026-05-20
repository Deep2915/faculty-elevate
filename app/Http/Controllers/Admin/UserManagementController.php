<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FacultyWelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $sort = in_array($request->string('sort')->toString(), ['name', 'email', 'role'], true)
            ? $request->string('sort')->toString()
            : 'name';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $search = trim($request->string('q')->toString());
        $role = trim($request->string('role')->toString());

        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($role, ['admin', 'hod', 'faculty'], true), function ($query) use ($role): void {
                $query->where('role', $role);
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.users.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:admin,hod,faculty'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Use provided password or generate a secure one
        $plainPassword = $data['password'] ?? Str::random(12);

        $user = User::query()->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($plainPassword),
        ]);

        // Email credentials to the new user
        $emailStatus = 'Credentials emailed to ' . $user->email . '.';
        try {
            Mail::to($user->email)->send(new FacultyWelcomeMail(
                recipientName:  $user->name,
                recipientEmail: $user->email,
                plainPassword:  $plainPassword,
                role:           $user->role,
            ));
        } catch (\Throwable $e) {
            \Log::warning('Welcome email failed for '.$user->email.': '.$e->getMessage());
            $emailStatus = 'Email delivery failed. Share credentials manually.';
        }

        return redirect()->route('admin.users.index')
            ->with('status', "Account created for {$user->name}. {$emailStatus}")
            ->with('new_credentials', [
                'name'     => $user->name,
                'email'    => $user->email,
                'password' => $plainPassword,
                'role'     => $user->role,
            ]);
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.users.index', ['highlight' => $id]);
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('admin.users.index', ['edit' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id.',_id'],
            'role'     => ['required', 'in:admin,hod,faculty'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        User::query()->findOrFail($id)->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully.');
    }
}
