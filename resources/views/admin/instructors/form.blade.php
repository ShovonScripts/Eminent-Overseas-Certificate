@extends('layouts.admin')

@section('title', $instructor->exists ? 'Edit Instructor' : 'New Instructor')
@section('eyebrow', 'Faculty Management')

@section('topbar_actions')
    <a class="btn btn-ghost" href="{{ route('admin.instructors.index') }}">&larr; Back to list</a>
@endsection

@section('content')
    <form method="post" action="{{ $instructor->exists ? route('admin.instructors.update', $instructor) : route('admin.instructors.store') }}">
        @csrf
        @if ($instructor->exists)
            @method('put')
        @endif

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Instructor Profile & Credentials</h2>
                    <p>The instructor's name and title print directly beneath the signature line on official completion certificates.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Full Name</label>
                        <input id="name" class="input" name="name" type="text" required
                               placeholder="e.g. Kenji Tanaka / Dr. Michael Davies"
                               value="{{ old('name', $instructor->name) }}">
                        @error('name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="title">Academic / Professional Title</label>
                        <input id="title" class="input" name="title" type="text" required
                               placeholder="e.g. Chief Japanese Language Instructor"
                               value="{{ old('title', $instructor->title) }}">
                        @error('title')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="email">Institutional Email <span class="text-muted">(optional)</span></label>
                        <input id="email" class="input" name="email" type="email"
                               placeholder="instructor@eminentoverseas.com"
                               value="{{ old('email', $instructor->email) }}">
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field flex items-center" style="margin-top: var(--space-4);">
                        <label class="check">
                            <input type="checkbox" name="is_active" value="1"
                                   @checked(old('is_active', $instructor->is_active ?? true))>
                            <span><strong>Active status</strong> (available for new certificates)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="{{ route('admin.instructors.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">{{ $instructor->exists ? 'Save Changes' : 'Create Instructor' }}</button>
            </div>
        </div>
    </form>

    @if ($instructor->exists)
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Danger Zone</h2>
                    <p>Instructors tied to existing certificates cannot be deleted to preserve historic certificates.</p>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.instructors.destroy', $instructor) }}"
                      onsubmit="return confirm('Permanently delete this instructor profile?')">
                    @csrf
                    @method('delete')
                    <button class="btn btn-danger" type="submit">Delete Instructor</button>
                </form>
            </div>
        </div>
    @endif
@endsection
