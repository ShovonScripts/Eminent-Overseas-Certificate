<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.form', ['user' => new User]);
    }

    public function store(UserRequest $request, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        $logger->logCreated('App\Models\User', $user->id, $user->toArray(), $request);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "{$user->name} can now sign in to the admin panel.");
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.form', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('update', $user);

        $oldValues = $user->toArray();
        $data = $request->validated();

        if ($user->isSuperAdmin()
            && $data['role'] !== User::ROLE_SUPER_ADMIN
            && $this->isLastSuperAdmin($user)) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'The last super admin cannot be demoted.']);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if (! blank($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $logger->logUpdated('App\Models\User', $user->id, $oldValues, $user->fresh()->toArray(), $request);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Staff account was updated.');
    }

    public function destroy(Request $request, User $user, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($this->isLastSuperAdmin($user)) {
            return back()->with('error', 'The last super admin cannot be deleted.');
        }

        $oldValues = $user->toArray();
        $user->delete();

        $logger->logDeleted('App\Models\User', $user->id, $oldValues, $request);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Staff account was removed.');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin()
            && User::where('role', User::ROLE_SUPER_ADMIN)->count() <= 1;
    }
}
