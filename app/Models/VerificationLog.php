<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VerificationLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationLog extends Model
{
    /** @use HasFactory<VerificationLogFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'certificate_id',
        'code_tried',
        'result',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
