<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Enums\Gender;
use App\Observers\CertificateObserver;
use App\Support\JapaneseDate;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CertificateObserver::class)]
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certificate_no',
        'verification_code',
        'course_id',
        'student_name_en',
        'student_name_ja',
        'student_date_of_birth',
        'student_gender',
        'student_email',
        'student_phone',
        'study_start',
        'study_end',
        'hours_per_day',
        'days_per_week',
        'class_days',
        'national_holidays',
        'total_class_hours',
        'total_attendance_hours',
        'attendance_rate',
        'issued_on',
        'status',
        'issued_by',
        'issued_at',
        'revoked_at',
        'revoke_reason',
        'pdf_path',
        'pdf_sha256',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(CertificateAttendance::class)
            ->orderBy('year')
            ->orderBy('month');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'certificate_instructor')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verificationLogs(): HasMany
    {
        return $this->hasMany(VerificationLog::class);
    }

    public function verifyUrl(): string
    {
        return rtrim((string) config('school.verify_base_url'), '/').'/'.$this->verification_code;
    }

    public function isDraft(): bool
    {
        return $this->status === CertificateStatus::Draft;
    }

    public function isIssued(): bool
    {
        return $this->status === CertificateStatus::Issued;
    }

    public function isRevoked(): bool
    {
        return $this->status === CertificateStatus::Revoked;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'study_start' => 'date',
            'study_end' => 'date',
            'issued_on' => 'date',
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
            'student_date_of_birth' => 'date',
            'student_gender' => Gender::class,
            'class_days' => 'array',
            'status' => CertificateStatus::class,
            'hours_per_day' => 'integer',
            'days_per_week' => 'integer',
            'total_class_hours' => 'integer',
            'total_attendance_hours' => 'integer',
            'attendance_rate' => 'decimal:2',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function classDaysJapanese(): array
    {
        return array_map(
            static fn (string $day): string => JapaneseDate::weekday($day),
            $this->class_days ?? [],
        );
    }

    public function studentGenderJapanese(): string
    {
        return $this->student_gender?->japaneseLabel() ?? '';
    }

    public function studentGenderBilingual(): string
    {
        if (! $this->student_gender) {
            return '—';
        }

        return "{$this->student_gender->japaneseLabel()} / {$this->student_gender->label()}";
    }
}
