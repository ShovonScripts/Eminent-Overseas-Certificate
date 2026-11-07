<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AdminActivityLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    /** @use HasFactory<AdminActivityLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute(): string
    {
        $model = $this->model_type ? class_basename($this->model_type) : 'System';

        return match ($this->action) {
            'created' => "Created {$model}",
            'updated' => "Updated {$model}",
            'deleted' => "Deleted {$model}",
            'issued' => 'Issued certificate',
            'revoked' => 'Revoked certificate',
            'imported' => 'Imported certificates',
            'login' => 'Logged in',
            'logout' => 'Logged out',
            'settings_changed' => 'Changed settings',
            default => ucfirst($this->action)." {$model}",
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            'created', 'issued', 'imported', 'login' => 'issued',
            'updated', 'settings_changed' => 'gold',
            'deleted', 'revoked', 'logout' => 'revoked',
            default => 'muted',
        };
    }
}
