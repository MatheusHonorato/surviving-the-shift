<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PatientMetric extends Model
{
    protected $fillable = [
        'patient_id',
        'users_completed',
        'users_attempted',
        'users_attempted_not_completed',
        'avg_step_time_sec',
        'avg_correct_rate',
        'hardest_step_id',
        'hardest_step_index',
        'hardest_step_correct_rate',
        'last_updated_at',
    ];

    protected $casts = [
        'users_completed' => 'integer',
        'users_attempted' => 'integer',
        'users_attempted_not_completed' => 'integer',
        'avg_step_time_sec' => 'integer',
        'avg_correct_rate' => 'decimal:4',
        'hardest_step_index' => 'integer',
        'hardest_step_correct_rate' => 'decimal:4',
        'last_updated_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function hardestStep(): BelongsTo
    {
        return $this->belongsTo(Step::class, 'hardest_step_id');
    }

    public static function updateMetricsForPatient(int $patientId): void
    {
        // Total de steps do patient
        $totalSteps = DB::table('steps')
            ->where('patient_id', $patientId)
            ->count();

        // Usuários que completaram: verifica se há pelo menos uma tentativa onde o usuário
        // completou todos os steps corretamente na mesma tentativa
        $usersCompleted = DB::table('answers as ua')
            ->select('ua.user_id')
            ->where('ua.patient_id', $patientId)
            ->where('ua.is_correct', true)
            ->groupBy('ua.user_id', 'ua.attempt')
            ->havingRaw('COUNT(DISTINCT ua.step_id) >= ?', [$totalSteps])
            ->pluck('ua.user_id')
            ->unique()
            ->count();

        // Usuários que tentaram (responderam ao menos um step)
        $usersAttempted = DB::table('answers as ua')
            ->where('ua.patient_id', $patientId)
            ->whereNotNull('ua.answered_at')
            ->pluck('ua.user_id')
            ->unique()
            ->count();

        $usersAttemptedNotCompleted = max(0, $usersAttempted - $usersCompleted);

        // Tempo médio por passo (em segundos) - média das médias por step
        $avgStepTime = DB::table('answers as ua')
            ->select('ua.step_id')
            ->where('ua.patient_id', $patientId)
            ->whereNotNull('ua.started_at')
            ->whereNotNull('ua.answered_at')
            ->groupBy('ua.step_id')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, ua.started_at, ua.answered_at)) as step_avg_time')
            ->pluck('step_avg_time')
            ->avg();

        $avgStepTime = $avgStepTime !== null ? (float) $avgStepTime : 0.0;

        // Taxa média de acerto
        $correctAgg = DB::table('answers as ua')
            ->where('ua.patient_id', $patientId)
            ->whereNotNull('ua.answered_at')
            ->selectRaw('
                SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                COUNT(*) as total_answers
            ')
            ->first();

        $avgCorrectRate = 0.0;
        if ($correctAgg && $correctAgg->total_answers > 0) {
            $avgCorrectRate = (float) $correctAgg->correct_answers / (float) $correctAgg->total_answers;
        }

        // Passo mais difícil: step com maior número de erros (independente da tentativa/attempt)
        $stepIncorrectCounts = DB::table('answers as ua')
            ->where('ua.patient_id', $patientId)
            ->whereNotNull('ua.answered_at')
            ->groupBy('ua.step_id')
            ->selectRaw('
                ua.step_id,
                SUM(CASE WHEN ua.is_correct = 0 THEN 1 ELSE 0 END) as incorrect_count,
                COUNT(*) as total_count,
                COALESCE(SUM(CASE WHEN ua.is_correct = 0 THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0), 0) as error_rate
            ')
            ->get();

        $hardestStepId = null;
        $hardestStepIndex = null;
        $hardestStepCorrectRate = null;

        if ($stepIncorrectCounts->isNotEmpty()) {
            // Encontra o step com maior número de erros (incorrect_count)
            $maxIncorrectCount = $stepIncorrectCounts->max(function ($step) {
                return (int) $step->incorrect_count;
            });

            // Filtra steps com esse número de erros e escolhe o com mais tentativas totais em caso de empate
            $hardestStep = $stepIncorrectCounts
                ->filter(function ($step) use ($maxIncorrectCount) {
                    return (int) $step->incorrect_count === $maxIncorrectCount;
                })
                ->sortByDesc('total_count')
                ->first();

            if ($hardestStep && $hardestStep->total_count > 0) {
                $hardestStepId = (int) $hardestStep->step_id;
                $errorRate = (float) $hardestStep->error_rate;
                $hardestStepCorrectRate = 1.0 - $errorRate;

                $orderedSteps = DB::table('steps')
                    ->where('patient_id', $patientId)
                    ->orderBy('id')
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->toArray();

                $stepIndex = array_search($hardestStepId, $orderedSteps, true);
                $hardestStepIndex = $stepIndex !== false ? $stepIndex + 1 : null;
            }
        }

        // Atualiza ou cria o registro
        self::updateOrCreate(
            ['patient_id' => $patientId],
            [
                'users_completed' => $usersCompleted,
                'users_attempted' => $usersAttempted,
                'users_attempted_not_completed' => $usersAttemptedNotCompleted,
                'avg_step_time_sec' => (int) round($avgStepTime),
                'avg_correct_rate' => round($avgCorrectRate, 4),
                'hardest_step_id' => $hardestStepId,
                'hardest_step_index' => $hardestStepIndex,
                'hardest_step_correct_rate' => $hardestStepCorrectRate !== null ? round($hardestStepCorrectRate, 4) : null,
                'last_updated_at' => now(),
            ]
        );
    }
}
