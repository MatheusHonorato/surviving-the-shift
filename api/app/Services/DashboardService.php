<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        return [
            'patients' => $this->getPatientsData(),
            'users' => $this->getUsersData(),
        ];
    }

    private function getPatientsData(): Collection
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

    private function getUsersData(): array
    {
        $totalUsers = DB::table('users')->count();

        $activeUsers = DB::table('answers')
            ->whereNotNull('answered_at')
            ->select('user_id')
            ->distinct()
            ->count('user_id');

        $usersWithCompletedPatients = DB::table('answers as a')
            ->select('a.user_id')
            ->where('a.is_correct', true)
            ->groupBy('a.user_id', 'a.patient_id', 'a.attempt')
            ->havingRaw('COUNT(DISTINCT a.step_id) >= (
                SELECT COUNT(*) FROM steps WHERE patient_id = a.patient_id
            )')
            ->pluck('a.user_id')
            ->unique()
            ->count();

        $answersStats = DB::table('answers as a1')
            ->whereNotNull('a1.answered_at')
            ->whereRaw('a1.id = (
                SELECT MAX(a2.id)
                FROM answers as a2
                WHERE a2.user_id = a1.user_id
                AND a2.step_id = a1.step_id
                AND a2.answered_at IS NOT NULL
            )')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN a1.is_correct = 1 THEN 1 ELSE 0 END) as correct
            ')
            ->first();

        $totalAnswers = (int) ($answersStats->total ?? 0);
        $totalCorrectAnswers = (int) ($answersStats->correct ?? 0);

        $overallAccuracyRate = $totalAnswers > 0
            ? round($totalCorrectAnswers / $totalAnswers, 4)
            : 0.0;

        $engagementRate = $totalUsers > 0
            ? round($activeUsers / $totalUsers, 4)
            : 0.0;

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'users_with_completed_patients' => $usersWithCompletedPatients,
            'total_answers' => $totalAnswers,
            'total_correct_answers' => $totalCorrectAnswers,
            'overall_accuracy_rate' => $overallAccuracyRate,
            'engagement_rate' => $engagementRate,
        ];
    }
}

