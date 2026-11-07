<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CertificateAttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateAttendance extends Model
{
    /** @use HasFactory<CertificateAttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'year',
        'month',
        'class_hours',
        'attendance_hours',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function attendanceRate(): int
    {
        return $this->class_hours > 0
            ? (int) round($this->attendance_hours / $this->class_hours * 100)
            : 0;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'class_hours' => 'integer',
            'attendance_hours' => 'integer',
        ];
    }
}
