<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PersonalReportService
{
    public function getReportData(int $userId): array
    {
        return [
            'summary' => $this->getSummary($userId),
            'patients' => $this->getPatientsData($userId),
            'steps' => $this->getStepsData($userId),
            'attempts' => $this->getAttemptsData($userId),
            'insights' => $this->getInsights($userId),
        ];
    }

    private function getSummary(int $userId): array
    {
        $totalPatients = DB::table('patients')->count();
        $completedPatients = $this->getCompletedPatients($userId);

        $attemptedPatients = DB::table('answers')
            ->where('user_id', $userId)
            ->whereNotNull('answered_at')
            ->select('patient_id')
            ->distinct()
            ->count('patient_id');

        $answersStats = DB::table('answers as a1')
            ->where('a1.user_id', $userId)
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
                SUM(CASE WHEN a1.is_correct = 1 THEN 1 ELSE 0 END) as correct,
                SUM(CASE WHEN a1.is_correct = 0 THEN 1 ELSE 0 END) as incorrect
            ')
            ->first();

        $totalAnswers = (int) ($answersStats->total ?? 0);
        $correctAnswers = (int) ($answersStats->correct ?? 0);
        $incorrectAnswers = (int) ($answersStats->incorrect ?? 0);
        $accuracyRate = $totalAnswers > 0
            ? round(($correctAnswers / $totalAnswers) * 100, 2)
            : 0.0;

        $timeStats = DB::table('answers')
            ->where('user_id', $userId)
            ->whereNotNull('started_at')
            ->whereNotNull('answered_at')
            ->selectRaw('
                SUM(TIMESTAMPDIFF(SECOND, started_at, answered_at)) as total_seconds,
                AVG(TIMESTAMPDIFF(SECOND, started_at, answered_at)) as avg_seconds
            ')
            ->first();

        $totalTimeSeconds = (int) ($timeStats->total_seconds ?? 0);
        $avgTimeSeconds = $timeStats->avg_seconds ? round((float) $timeStats->avg_seconds, 2) : 0.0;

        $totalAttempts = DB::table('answers')
            ->where('user_id', $userId)
            ->selectRaw('COUNT(DISTINCT CONCAT(patient_id, "-", attempt)) as total')
            ->value('total') ?? 0;

        return [
            'total_patients' => $totalPatients,
            'completed_patients' => $completedPatients,
            'attempted_patients' => $attemptedPatients,
            'completion_rate' => $totalPatients > 0
                ? round(($completedPatients / $totalPatients) * 100, 2)
                : 0.0,
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'accuracy_rate' => $accuracyRate,
            'total_time_seconds' => $totalTimeSeconds,
            'avg_time_seconds' => $avgTimeSeconds,
            'total_attempts' => $totalAttempts,
        ];
    }

    private function getCompletedPatients(int $userId): int
    {
        $patients = DB::table('patients')->pluck('id');
        $completed = 0;

        foreach ($patients as $patientId) {
            $totalSteps = DB::table('steps')
                ->where('patient_id', $patientId)
                ->count();

            if ($totalSteps === 0) {
                continue;
            }

            $hasCompleteAttempt = DB::table('answers')
                ->select('attempt')
                ->where('user_id', $userId)
                ->where('patient_id', $patientId)
                ->where('is_correct', true)
                ->groupBy('attempt')
                ->havingRaw('COUNT(DISTINCT step_id) >= ?', [$totalSteps])
                ->limit(1)
                ->exists();

            if ($hasCompleteAttempt) {
                $completed++;
            }
        }

        return $completed;
    }

    private function getPatientsData(int $userId): array
    {
        $patients = DB::table('patients')
            ->orderBy('id')
            ->get();

        $result = [];

        foreach ($patients as $patient) {
            $patientId = $patient->id;
            $totalSteps = DB::table('steps')
                ->where('patient_id', $patientId)
                ->count();

            $completedAttempts = DB::table('answers')
                ->select('attempt')
                ->where('user_id', $userId)
                ->where('patient_id', $patientId)
                ->where('is_correct', true)
                ->groupBy('attempt')
                ->havingRaw('COUNT(DISTINCT step_id) >= ?', [$totalSteps])
                ->pluck('attempt')
                ->toArray();

            $isCompleted = ! empty($completedAttempts);
            $bestAttempt = ! empty($completedAttempts) ? min($completedAttempts) : null;

            $totalAttempts = DB::table('answers')
                ->where('user_id', $userId)
                ->where('patient_id', $patientId)
                ->select('attempt')
                ->distinct()
                ->count('attempt');

            $patientAnswers = DB::table('answers as a1')
                ->where('a1.user_id', $userId)
                ->where('a1.patient_id', $patientId)
                ->whereNotNull('a1.answered_at')
                ->whereRaw('a1.id = (
                    SELECT MAX(a2.id)
                    FROM answers as a2
                    WHERE a2.user_id = a1.user_id
                    AND a2.step_id = a1.step_id
                    AND a2.answered_at IS NOT NULL
                )')
                ->get();

            $patientCorrect = $patientAnswers->where('is_correct', true)->count();
            $patientTotal = $patientAnswers->count();
            $patientAccuracy = $patientTotal > 0
                ? round(($patientCorrect / $patientTotal) * 100, 2)
                : 0.0;

            $bestAttemptTime = null;
            if ($bestAttempt !== null) {
                $bestAttemptTime = DB::table('answers')
                    ->where('user_id', $userId)
                    ->where('patient_id', $patientId)
                    ->where('attempt', $bestAttempt)
                    ->whereNotNull('started_at')
                    ->whereNotNull('answered_at')
                    ->selectRaw('SUM(TIMESTAMPDIFF(SECOND, started_at, answered_at)) as total_seconds')
                    ->value('total_seconds') ?? 0;
            }

            $result[] = [
                'patient_id' => $patientId,
                'total_steps' => $totalSteps,
                'is_completed' => $isCompleted,
                'total_attempts' => $totalAttempts,
                'best_attempt' => $bestAttempt,
                'accuracy_rate' => $patientAccuracy,
                'best_attempt_time_seconds' => (int) ($bestAttemptTime ?? 0),
            ];
        }

        return $result;
    }

    private function getStepsData(int $userId): array
    {
        $steps = DB::table('steps as s')
            ->join('patients as p', 'p.id', '=', 's.patient_id')
            ->select([
                's.id as step_id',
                's.patient_id'
            ])
            ->orderBy('s.patient_id')
            ->orderBy('s.id')
            ->get();

        $stepIndexMap = [];
        $patientStepCounters = [];
        foreach ($steps as $step) {
            $patientId = $step->patient_id;
            if (! isset($patientStepCounters[$patientId])) {
                $patientStepCounters[$patientId] = 0;
            }
            $patientStepCounters[$patientId]++;
            $stepIndexMap[$step->step_id] = $patientStepCounters[$patientId];
        }

        $result = [];

        foreach ($steps as $step) {
            $stepId = $step->step_id;
            $stepIndex = $stepIndexMap[$step->step_id] ?? 1;

            $stepAnswers = DB::table('answers')
                ->where('user_id', $userId)
                ->where('step_id', $stepId)
                ->whereNotNull('answered_at')
                ->orderBy('answered_at')
                ->get();

            $totalAnswers = $stepAnswers->count();
            $correctAnswers = $stepAnswers->where('is_correct', true)->count();
            $incorrectAnswers = $totalAnswers - $correctAnswers;

            $accuracyRate = $totalAnswers > 0
                ? round(($correctAnswers / $totalAnswers) * 100, 2)
                : 0.0;

            $avgTime = DB::table('answers')
                ->where('user_id', $userId)
                ->where('step_id', $stepId)
                ->whereNotNull('started_at')
                ->whereNotNull('answered_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, answered_at)) as avg_seconds')
                ->value('avg_seconds') ?? 0;

            $attemptsUntilCorrect = null;
            if ($stepAnswers->isNotEmpty()) {
                $attemptCount = 0;
                foreach ($stepAnswers as $answer) {
                    $attemptCount++;
                    if ($answer->is_correct) {
                        $attemptsUntilCorrect = $attemptCount;
                        break;
                    }
                }
            }

            $result[] = [
                'step_id' => $stepId,
                'patient_id' => $step->patient_id,
                'step_index' => $stepIndex,
                'total_answers' => $totalAnswers,
                'correct_answers' => $correctAnswers,
                'incorrect_answers' => $incorrectAnswers,
                'accuracy_rate' => $accuracyRate,
                'avg_time_seconds' => round((float) $avgTime, 2),
                'attempts_until_correct' => $attemptsUntilCorrect,
            ];
        }

        return $result;
    }

    private function getAttemptsData(int $userId): array
    {
        $attempts = DB::table('answers')
            ->where('user_id', $userId)
            ->whereNotNull('answered_at')
            ->select([
                'patient_id',
                'attempt',
                DB::raw('MIN(COALESCE(started_at, answered_at)) as started_at'),
                DB::raw('MAX(answered_at) as finished_at'),
                DB::raw('COUNT(*) as total_answers'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
            ])
            ->groupBy('patient_id', 'attempt')
            ->orderBy('patient_id')
            ->orderBy('attempt')
            ->get();

        $result = [];

        foreach ($attempts as $attempt) {
            $patientId = $attempt->patient_id;
            $totalSteps = DB::table('steps')
                ->where('patient_id', $patientId)
                ->count();

            $uniqueCorrectSteps = DB::table('answers')
                ->where('user_id', $userId)
                ->where('patient_id', $patientId)
                ->where('attempt', $attempt->attempt)
                ->where('is_correct', true)
                ->select('step_id')
                ->distinct()
                ->count('step_id');

            $isComplete = $uniqueCorrectSteps >= $totalSteps;

            $duration = 0;
            if ($attempt->started_at && $attempt->finished_at) {
                $start = is_string($attempt->started_at)
                    ? strtotime($attempt->started_at)
                    : $attempt->started_at;
                $end = is_string($attempt->finished_at)
                    ? strtotime($attempt->finished_at)
                    : $attempt->finished_at;
                $duration = $end - $start;
            }

            $patient = DB::table('patients')
                ->where('id', $patientId)
                ->first();

            $result[] = [
                'patient_id' => $patientId,
                'attempt' => $attempt->attempt,
                'started_at' => $attempt->started_at,
                'finished_at' => $attempt->finished_at,
                'duration_seconds' => max(0, $duration),
                'total_answers' => (int) $attempt->total_answers,
                'correct_answers' => (int) $uniqueCorrectSteps,
                'incorrect_answers' => (int) $attempt->total_answers - (int) $attempt->correct_answers,
                'is_complete' => $isComplete,
                'completion_rate' => $totalSteps > 0
                    ? round(($uniqueCorrectSteps / $totalSteps) * 100, 2)
                    : 0.0,
            ];
        }

        return $result;
    }

    private function getInsights(int $userId): array
    {
        $stepsData = $this->getStepsData($userId);

        $strongSteps = collect($stepsData)
            ->filter(fn ($step) => $step['accuracy_rate'] >= 80 && $step['total_answers'] > 0)
            ->sortByDesc('accuracy_rate')
            ->take(5)
            ->values()
            ->toArray();

        $weakSteps = collect($stepsData)
            ->filter(fn ($step) => ($step['accuracy_rate'] < 50 || $step['incorrect_answers'] >= 2)
                && $step['total_answers'] > 0
            )
            ->sortBy('accuracy_rate')
            ->take(5)
            ->values()
            ->toArray();

        $needsPractice = collect($stepsData)
            ->filter(fn ($step) => $step['attempts_until_correct'] !== null
                && $step['attempts_until_correct'] > 1
            )
            ->sortByDesc('attempts_until_correct')
            ->take(5)
            ->values()
            ->toArray();

        return [
            'strong_steps' => $strongSteps,
            'weak_steps' => $weakSteps,
            'needs_practice' => $needsPractice,
        ];
    }
}

