@extends('layouts.admin')

@section('title', 'Staff accounts')
@section('eyebrow', 'System Administration')

@section('topbar_actions')
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Staff Account</span>
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-head">
            <div>
                <h2>Authorized Staff Accounts</h2>
                <p>Staff members can create and issue student certificates. Super Admins can revoke credentials and administer staff logins.</p>
            </div>
            <span class="badge badge-info">{{ $users->total() }} account{{ $users->total() === 1 ? '' : 's' }}</span>
        </div>

        <div class="table-wrap" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>System Role</th>
                    <th>Member Since</th>
                    <th class="actions">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <strong style="color: #0F172A;">{{ $user->name }}</strong>
                            @if ($user->id === auth()->id())
                                <span class="badge badge-gold" style="font-size: 0.65rem; margin-left: 6px;">Current User</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->isSuperAdmin() ? 'badge-issued' : 'badge-muted' }}">
                                {{ $user->isSuperAdmin() ? 'Super Admin' : 'Staff' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $user->created_at?->format('Y-m-d') }}</td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            @if ($user->id !== auth()->id())
                                <form method="post" action="{{ route('admin.users.destroy', $user) }}"
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Remove this staff account?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $users->links() }}
        </div>
    </div>
@endsection
