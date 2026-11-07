<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Sheikh Monujurl Islam',
            'Abdul Kaium Ovi',
        ] as $name) {
            Instructor::updateOrCreate(
                ['name' => $name],
                [
                    'title' => '担当講師 / Instructor',
                    'email' => 'info@eminentoverseas.uk',
                    'is_active' => true,
                ],
            );
        }
    }
}
