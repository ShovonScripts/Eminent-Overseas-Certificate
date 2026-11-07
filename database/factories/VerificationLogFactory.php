<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\VerificationLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VerificationLog>
 */
class VerificationLogFactory extends Factory
{
    protected $model = VerificationLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'certificate_id' => null,
            'code_tried' => strtoupper((string) Str::ulid()),
            'result' => 'not_found',
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => now(),
        ];
    }
}
