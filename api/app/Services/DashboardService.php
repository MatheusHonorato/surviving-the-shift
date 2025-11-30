<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): Collection
    {
        return DB::table('patients as p')
            ->leftJoin('patient_metrics as pm', 'pm.patient_id', '=', 'p.id')
            ->select([
                'p.id as patient_id',
                DB::raw('COALESCE(pm.users_completed, 0) as users_completed'),
                DB::raw('COALESCE(pm.users_attempted, 0) as users_attempted'),
                DB::raw('COALESCE(pm.users_attempted_not_completed, 0) as users_attempted_not_completed'),
                DB::raw('COALESCE(pm.avg_step_time_sec, 0) as avg_step_time_sec'),
                DB::raw('COALESCE(pm.avg_correct_rate, 0) as avg_correct_rate'),
                DB::raw('pm.hardest_step_id as hardest_step_id'),
                DB::raw('COALESCE(pm.hardest_step_index, 0) as hardest_step_index'),
                DB::raw('COALESCE(pm.hardest_step_correct_rate, 0) as hardest_step_correct_rate'),
            ])
            ->orderBy('p.id')
            ->get();
    }
}

