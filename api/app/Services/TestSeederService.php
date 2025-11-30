<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Test;
use Illuminate\Support\Facades\DB;

class TestSeederService
{
    public function createTest(array $patientIds, bool $enabled = true): Test
    {
        return DB::transaction(function () use ($patientIds, $enabled) {
            $test = Test::create([
                'length' => count($patientIds),
                'enabled' => $enabled,
            ]);

            $test->patients()->attach($patientIds);

            return $test;
        });
    }
}

