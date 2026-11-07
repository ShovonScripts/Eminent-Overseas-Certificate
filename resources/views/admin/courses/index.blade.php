@extends('layouts.admin')

@section('title', 'Courses')
@section('eyebrow', 'Reference Data')

@section('topbar_actions')
    <a class="btn btn-primary" href="{{ route('admin.courses.create') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Course</span>
    </a>
@endsection

@section('content')
    <div class="card">
        <form class="toolbar" method="get" action="{{ route('admin.courses.index') }}">
            <div style="position: relative;">
                <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search course name or JLPT level..." style="min-width: 300px; padding-left: 36px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <button class="btn btn-secondary" type="submit">Search</button>
            @if ($search !== '')
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.courses.index') }}">Clear</a>
            @endif
            <div class="spacer"></div>
            <span class="badge badge-info">{{ $courses->total() }} course{{ $courses->total() === 1 ? '' : 's' }}</span>
        </form>

        @if ($courses->isEmpty())
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                </svg>
                <strong>No courses found</strong>
                <p class="text-muted" style="margin-bottom: 16px;">Create a language curriculum course so certificates can reference it.</p>
                <a class="btn btn-primary btn-sm" href="{{ route('admin.courses.create') }}">Create First Course</a>
            </div>
        @else
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>JLPT Level</th>
                        <th class="num">Completion Hours</th>
                        <th class="num">Issued Certificates</th>
                        <th class="actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>
                                <a class="cell-title" href="{{ route('admin.courses.show', $course) }}">{{ $course->name_en }}</a>
                                <div class="cell-sub">{{ $course->name_ja }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-family: var(--font-mono); font-weight: 700;">{{ $course->level_code }}</span>
                            </td>
                            <td class="num">{{ $course->total_hours }} hrs</td>
                            <td class="num">
                                <span class="badge badge-muted">{{ $course->certificates_count }}</span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="{{ route('admin.courses.show', $course) }}">View</a>
                                <a class="btn btn-secondary btn-sm" href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
@endsection
