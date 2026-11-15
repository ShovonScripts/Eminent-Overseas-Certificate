@extends('verify.layout')

@section('title', 'Certificate Verified — ' . $certificate->certificate_no)

@section('content')
<div class="credential-card">
    <!-- Print Header (only when printing) -->
    <div class="print-only print-header">
        <div class="print-logo-wrap">
            <img src="{{ asset($school['images']['logo']) }}" alt="{{ $school['name'] }}">
        </div>
        <div class="print-header-titles">
            <h2>{{ $school['name'] }}</h2>
            <div class="print-subhead">OFFICIAL CREDENTIAL VERIFICATION &middot; 公的修了証明書 認証確認</div>
            <div class="print-meta">{{ $school['address'] }} &middot; {{ $school['phone'] }} &middot; {{ $school['website'] }}</div>
        </div>
    </div>

    <!-- Print Record Bar -->
    <div class="print-only print-record">
        <span class="rec-no">{{ $certificate->certificate_no }}</span>
        <span class="rec-status">OFFICIALLY REGISTERED & GENUINE (有効)</span>
        <span class="rec-meta">Issued: {{ $certificate->issued_on?->format('Y-m-d') }} &middot; Record ID: {{ $code }}</span>
    </div>

    <!-- Status Banner -->
    <div class="screen-only status-banner is-valid">
        <div class="status-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <div>
            <h1>Certificate Verified</h1>
            <p>This Japanese language study credential is authentic and registered with {{ $school['name'] }}.</p>
        </div>
        <div class="status-meta">
            <span class="badge">Officially Registered</span>
            <span class="record-id">Record ID: {{ $code }}</span>
        </div>
    </div>

    <div class="card-body">
        <div class="watermark" style="background-image: url('{{ asset($school['images']['seal'] ?? 'images/certificate/seal.png') }}');" aria-hidden="true"></div>

        <!-- Certificate Reference -->
        <div class="screen-only cert-ref">
            <div class="cert-no-block">
                <span class="cert-no-label">Certificate No. (証明書番号)</span>
                <div class="cert-no-row">
                    <span class="cert-no-pill mono">{{ $certificate->certificate_no }}</span>
                    <button class="btn-copy" onclick="copyText('{{ $certificate->certificate_no }}', 'Certificate Number')" title="Copy">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
            <div class="cert-status-row">
                <span class="badge active">Active & Genuine (有効)</span>
                <time>Issued {{ $certificate->issued_on?->format('Y-m-d') }}</time>
            </div>
        </div>

        <!-- Student & Course (combined) -->
        <section class="section">
            <h2>
                <span class="section-num">1</span>
                <span>Student & Course</span>
            </h2>
            <dl class="info-grid">
                <div class="item span-2">
                    <dt>Student Name (氏名)</dt>
                    <dd class="name">
                        {{ $certificate->student_name_en }}
                        @if ($certificate->student_name_ja)
                            <span class="ja-tag">{{ $certificate->student_name_ja }}</span>
                        @endif
                    </dd>
                </div>
                <div class="item">
                    <dt>Gender (性別)</dt>
                    <dd>{{ $certificate->student_gender?->label() }}</dd>
                </div>
                <div class="item">
                    <dt>Course (受講コース)</dt>
                    <dd>
                        <strong>{{ $certificate->course->name_en }}</strong>
                        @if ($certificate->course->name_ja)
                            <span class="ja-tag">/ {{ $certificate->course->name_ja }}</span>
                        @endif
                        <span class="level-badge">{{ $certificate->course->level_code }}</span>
                    </dd>
                </div>
                <div class="item">
                    <dt>Study Period (就学期間)</dt>
                    <dd>{{ $certificate->study_start?->format('Y-m-d') }} &rarr; {{ $certificate->study_end?->format('Y-m-d') }}</dd>
                </div>
                <div class="item">
                    <dt>Schedule (受講形態)</dt>
                    <dd>{{ $certificate->days_per_week }} days/wk &middot; {{ $certificate->hours_per_day }} h/session</dd>
                </div>
            </dl>

            <!-- Attendance Table -->
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Curriculum (MOJ)</th>
                        <th>Attended</th>
                        <th>Rate</th>
                        <th>Standing</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="num">{{ $certificate->total_class_hours }}</span> Hours</td>
                        <td><span class="num">{{ $certificate->total_attendance_hours }}</span> Hours</td>
                        <td><span class="num">{{ round((float) $certificate->attendance_rate) }}%</span></td>
                        <td><span class="badge done">Completed & Certified</span></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Authority & Verification -->
        <section class="section">
            <h2>
                <span class="section-num">2</span>
                <span>Issuing Authority & Verification</span>
            </h2>
            <dl class="info-grid">
                <div class="item span-2">
                    <dt>Issuing Authority</dt>
                    <dd><strong>{{ $school['name'] }}</strong></dd>
                </div>
                <div class="item span-2">
                    <dt>Campus</dt>
                    <dd>{{ $school['address'] }}</dd>
                </div>
                @if ($certificate->instructors && $certificate->instructors->isNotEmpty())
                    <div class="item span-2">
                        <dt>Faculty</dt>
                        <dd>{{ $certificate->instructors->pluck('name')->join(', ') }}</dd>
                    </div>
                @endif
                @if ($certificate->pdf_sha256)
                    <details class="item span-2 sha256">
                        <summary>
                            <dt>PDF SHA-256</dt>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </summary>
                        <div class="sha256-box">
                            <code>{{ $certificate->pdf_sha256 }}</code>
                            <button class="btn-copy" onclick="copyText('{{ $certificate->pdf_sha256 }}', 'SHA-256')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                <span>Copy</span>
                            </button>
                        </div>
                    </details>
                @endif
            </dl>
        </section>

        <!-- Attestation + Seal -->
        <div class="attestation">
            <div class="attestation-text">
                <strong>Official Registry Attestation:</strong>
                The above credentials are verified against the archival database of <strong>{{ $school['name'] }}</strong>. Valid for consular review, embassy visa, and Immigration Services Agency of Japan.
                <div class="attestation-meta">
                    <span>Authorized by: Office of the Registrar</span>
                    <span>Verified: {{ now()->format('Y-m-d') }}</span>
                    <span>Audit ID: <code>{{ $code }}</code></span>
                </div>
            </div>
            <div class="seal" title="Official Seal" aria-label="Official institutional seal">
                <svg viewBox="0 0 80 80" width="80" height="80">
                    <circle cx="40" cy="40" r="37" fill="#fff" stroke="#0A192F" stroke-width="1.5" stroke-dasharray="3,2"/>
                    <circle cx="40" cy="40" r="33" fill="none" stroke="#0A192F" stroke-width="1"/>
                    <circle cx="40" cy="40" r="22" fill="none" stroke="#0A192F" stroke-width="0.6"/>
                    <path id="arcTop" d="M 13,40 A 27,27 0 0,1 67,40" fill="none"/>
                    <path id="arcBot" d="M 67,40 A 27,27 0 0,1 13,40" fill="none"/>
                    <text font-size="4.5" font-family="'Cinzel',serif" font-weight="800" fill="#0A192F" letter-spacing="0.8"><textPath href="#arcTop" startOffset="50%" text-anchor="middle">EMINENT OVERSEAS</textPath></text>
                    <text font-size="4" font-family="'Plus Jakarta Sans',sans-serif" font-weight="700" fill="#475569" letter-spacing="0.8"><textPath href="#arcBot" startOffset="50%" text-anchor="middle">&middot; VERIFIED &middot;</textPath></text>
                    <text x="40" y="35" font-size="4" font-family="'Plus Jakarta Sans',sans-serif" font-weight="800" fill="#0A192F" text-anchor="middle">REGISTRAR</text>
                    <line x1="26" y1="37.5" x2="54" y2="37.5" stroke="#0A192F" stroke-width="0.6"/>
                    <text x="40" y="43" font-size="5" font-family="'Plus Jakarta Sans',sans-serif" font-weight="900" fill="#065F46" text-anchor="middle">AUTHENTIC</text>
                    <line x1="26" y1="45.5" x2="54" y2="45.5" stroke="#0A192F" stroke-width="0.6"/>
                    <text x="40" y="51" font-size="3.2" font-family="'Plus Jakarta Sans',sans-serif" font-weight="700" fill="#64748B" text-anchor="middle">OFFICIAL SEAL</text>
                </svg>
            </div>
        </div>

        <!-- Privacy Notice -->
        <p class="privacy-notice">Personal contact data (date of birth, phone, email) is protected and omitted from this public record.</p>

        <!-- Actions -->
        <div class="actions screen-only">
            <a href="{{ $school['website'] ?? 'https://eminentoverseas.uk' }}" target="_blank" rel="noopener" class="btn btn-web">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Official Website</span>
            </a>
            <button onclick="window.print()" class="btn btn-print" title="Print verification">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                <span>Print</span>
            </button>
            <a href="{{ route('verify.form') }}" class="btn btn-verify">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Verify Another</span>
            </a>
        </div>
    </div>
</div>
@endsection