@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Staff Account' : 'New Staff Account')
@section('eyebrow', 'Staff Administration')

@section('topbar_actions')
    <a class="btn btn-ghost" href="{{ route('admin.users.index') }}">&larr; Back to accounts</a>
@endsection

@section('content')
    <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists)
            @method('put')
        @endif

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Staff Account Credentials & Role</h2>
                    <p>Passwords must be at least 12 characters with upper and lower case letters, a number, and a symbol.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Staff Member Full Name</label>
                        <input id="name" class="input" name="name" type="text" required
                               placeholder="e.g. Sarah Jenkins"
                               value="{{ old('name', $user->name) }}">
                        @error('name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="email">Institutional Email Address</label>
                        <input id="email" class="input" name="email" type="email" required
                               placeholder="staff@eminentoverseas.com"
                               value="{{ old('email', $user->email) }}">
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field span-2">
                        <label for="role">Access Role</label>
                        <select id="role" class="select" name="role" required>
                            <option value="{{ \App\Models\User::ROLE_STAFF }}" @selected(old('role', $user->role ?? \App\Models\User::ROLE_STAFF) === \App\Models\User::ROLE_STAFF)>
                                Staff (Can create drafts, preview and issue certificates)
                            </option>
                            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected(old('role', $user->role) === \App\Models\User::ROLE_SUPER_ADMIN)>
                                Super Admin (Full access: revoke certificates, audit logs, staff management, settings)
                            </option>
                        </select>
                        <div class="help">Grant Super Admin role only to authorized administrative leads.</div>
                        @error('role')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="password">Password{{ $user->exists ? ' (leave blank to retain current)' : '' }}</label>
                        <input id="password" class="input" name="password" type="password" autocomplete="new-password"
                               placeholder="Min. 12 characters"
                               @required(! $user->exists)>
                        @error('password')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" class="input" name="password_confirmation" type="password"
                               placeholder="Re-enter password"
                               autocomplete="new-password" @required(! $user->exists)>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">{{ $user->exists ? 'Save Changes' : 'Create Staff Account' }}</button>
            </div>
        </div>
    </form>
@endsection
