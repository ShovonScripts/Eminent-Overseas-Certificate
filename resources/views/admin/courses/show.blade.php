@extends('layouts.admin')

@section('title', $course->name_en)
@section('eyebrow', 'Course Details')

@section('topbar_actions')
    <a class="btn btn-secondary" href="{{ route('admin.courses.edit', $course) }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
        </svg>
        <span>Edit Course</span>
    </a>
    <a class="btn btn-ghost" href="{{ route('admin.courses.index') }}">&larr; Back to list</a>
@endsection

@section('content')
    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Curriculum Specification</h2>
                    <p>{{ $course->name_ja }}</p>
                </div>
                <span class="badge badge-info" style="font-family: var(--font-mono); font-weight: 700; font-size: 0.75rem;">{{ $course->level_code }}</span>
            </div>
            <div class="card-body">
                <dl class="detail-grid">
                    <dt>English Name</dt>
                    <dd><strong>{{ $course->name_en }}</strong></dd>

                    <dt>Japanese Name</dt>
                    <dd>{{ $course->name_ja }}</dd>

                    <dt>Completion Hours</dt>
                    <dd><span class="badge badge-muted">{{ $course->total_hours }} total class hours</span></dd>

                    <dt>Target JLPT Level</dt>
                    <dd>{{ $course->aim_level_en }} <span class="text-muted">/ {{ $course->aim_level_ja }}</span></dd>

                    <dt>Core Textbook</dt>
                    <dd>{{ $course->book_en }} <span class="text-muted">/ {{ $course->book_ja }}</span></dd>

                    <dt>Default Schedule</dt>
                    <dd>{{ $course->default_hours_per_day }} hours/day, {{ $course->default_days_per_week }} days/week</dd>

                    <dt>Linked Certificates</dt>
                    <dd><strong>{{ $course->certificates_count }}</strong> certificate{{ $course->certificates_count === 1 ? '' : 's' }} issued</dd>
                </dl>

                <div class="section-title">Learning Context & Objectives</div>
                @if (empty($course->learning_context))
                    <p class="text-muted mb-0">No context descriptions defined.</p>
                @else
                    <ul class="mb-0" style="padding-left: 20px; line-height: 1.6;">
                        @foreach ($course->learning_context as $context)
                            <li><strong>{{ $context['en'] ?? '' }}</strong> <span class="text-muted">/ {{ $context['ja'] ?? '' }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h2>Usage & Active Credentials</h2>
                        <p>{{ $course->certificates_count }} certificate{{ $course->certificates_count === 1 ? '' : 's' }} enrolled</p>
                    </div>
                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.certificates.index', ['q' => $course->name_en]) }}">Find Certificates</a>
                </div>
                <div class="card-body">
                    @if ($course->certificates_count > 0)
                        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: var(--radius-md); padding: var(--space-4); color: #1E40AF; font-size: 0.8125rem;">
                            <strong>In Active Use:</strong> This course is currently attached to {{ $course->certificates_count }} student certificate{{ $course->certificates_count === 1 ? '' : 's' }}. To maintain cryptographic and historical integrity, it cannot be deleted while certificates reference it.
                        </div>
                    @else
                        <div style="background: #F8FAFC; border: 1px dashed var(--color-border); border-radius: var(--radius-md); padding: var(--space-4); color: #475569; font-size: 0.8125rem;">
                            <strong>Not Yet Enrolled:</strong> No certificates have been issued under this course yet. It can be freely edited or safely removed.
                        </div>
                    @endif
                </div>
            </div>

            @if ($course->certificates_count === 0)
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h2>Danger Zone</h2>
                            <p>Permanent course removal</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.courses.destroy', $course) }}"
                              onsubmit="return confirm('Permanently delete this course?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger" type="submit">Delete Course</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
