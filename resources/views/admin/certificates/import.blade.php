@extends('layouts.admin')

@section('title', 'Import Certificates')
@section('eyebrow', 'Batch Operations')

@section('topbar_actions')
    <a class="btn btn-secondary" href="{{ route('admin.certificates.import.template') }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span>Download CSV Template</span>
    </a>
    <a class="btn btn-ghost" href="{{ route('admin.certificates.index') }}">&larr; Back to list</a>
@endsection

@section('content')
    @isset($results)
        @if ($results['issued'] > 0)
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>
                    <strong>Batch Import Successful:</strong> {{ $results['issued'] }} certificate{{ $results['issued'] === 1 ? '' : 's' }} issued successfully.
                </div>
            </div>
        @endif

        @if ($results['errors'] !== [])
            <div class="card">
                <div class="card-head">
                    <div>
                        <h2>Rows That Failed Validation</h2>
                        <p>Correct these entries in your CSV spreadsheet and upload the file again. Successful rows have already been issued.</p>
                    </div>
                    <span class="badge badge-revoked">{{ count($results['errors']) }} issue{{ count($results['errors']) === 1 ? '' : 's' }}</span>
                </div>
                <div class="table-wrap" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="num" style="width: 100px;">CSV Row</th>
                            <th>Validation Issue</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($results['errors'] as $error)
                            <tr>
                                <td class="num mono" style="font-weight: 700; color: #DC2626;">Row #{{ $error['row'] }}</td>
                                <td style="color: #991B1B;">{{ $error['message'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endisset

    <div class="card">
        <div class="card-head">
            <div>
                <h2>Upload Spreadsheet (CSV)</h2>
                <p>Each valid row creates, calculates attendance, and immediately issues one authenticated certificate.</p>
            </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('admin.certificates.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="field" style="max-width: 600px;">
                    <label for="csv">Select CSV File</label>
                    <input id="csv" class="input" name="csv" type="file" accept=".csv,text/csv" required style="padding: 10px;">
                    <div class="help">Maximum file size: 2 MB. Make sure your columns match the required specification below.</div>
                </div>

                <div class="form-actions" style="border-radius: var(--radius-md); margin-top: var(--space-6); background: transparent; padding: 0;">
                    <a class="btn btn-secondary" href="{{ route('admin.certificates.index') }}">Cancel</a>
                    <button class="btn btn-primary btn-lg" type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span>Import & Issue Certificates</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h2>CSV Schema & Column Reference</h2>
                <p>Dates must follow the ISO format <code>YYYY-MM-DD</code>. Multi-value columns should be semicolon-delimited.</p>
            </div>
        </div>
        <div class="table-wrap" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                <tr>
                    <th>Column Name</th>
                    <th>Example Value</th>
                    <th>Requirement / Notes</th>
                </tr>
                </thead>
                <tbody>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_name_en</td><td class="mono">Md. Anik Rahman</td><td><strong>Required.</strong> Full name in English (Roman script).</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_name_ja</td><td class="mono">アニク・ラーマン</td><td><em>Optional.</em> Full name in Katakana / Kanji.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_date_of_birth</td><td class="mono">2004-12-09</td><td><strong>Required.</strong> Format: YYYY-MM-DD.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_gender</td><td class="mono">male</td><td><strong>Required.</strong> <code>male</code>, <code>female</code>, or <code>other</code>.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_email</td><td class="mono">anik@example.com</td><td><em>Optional.</em> Notification email.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">student_phone</td><td class="mono">+8801711111111</td><td><em>Optional.</em> Contact phone.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">course</td><td class="mono">N5</td><td>Course level code (e.g. <code>N5</code>) or course name.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">study_start / study_end / issued_on</td><td class="mono">2025-06-02</td><td>Issue date must be on or after the study end date.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">class_days</td><td class="mono">Saturday;Monday;Wednesday</td><td>Semicolon-separated English weekday names.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">hours_per_day</td><td class="mono">3</td><td>Numeric value between 1 and 12.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">national_holidays</td><td class="mono">6月9日;6月11日</td><td><em>Optional.</em> Japanese dates or YYYY-MM-DD format.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">instructors</td><td class="mono">Taro Yamada;Hanako Suzuki</td><td>Names or emails. Blank defaults to active instructors.</td></tr>
                <tr><td class="mono" style="font-weight: 700; color: #1E3A8A;">YYYY-MM</td><td class="mono">2025-06 = 33</td><td>Monthly attendance hours attended by student.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
