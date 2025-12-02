<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\AnswerKey;
use App\Models\PatientMetric;
use App\Models\Step;
use Illuminate\Support\Facades\DB;

class AnswerService
{
    public function getPatientIdAndCurrentAttempt(int $userId, int $stepId): array
    {
        $step = Step::findOrFail($stepId);
        $patientId = $step->patient_id;
        $attempt = 1;

        $totalSteps = DB::table('steps')
            ->where('patient_id', $patientId)
            ->count();

        $lastAnswer = Answer::where('user_id', $userId)
            ->where('patient_id', $patientId)
            ->orderBy('attempt', 'desc')
            ->first();

        if ($lastAnswer) {
            $attempt = $lastAnswer->attempt;

            $correctStepsCount = (int) DB::table('answers')
                ->where('user_id', $userId)
                ->where('patient_id', $patientId)
                ->where('attempt', $attempt)
                ->where('is_correct', true)
                ->select(DB::raw('COUNT(DISTINCT step_id) as count'))
                ->value('count') ?? 0;

            if ($correctStepsCount >= $totalSteps || $lastAnswer->is_correct == false) {
                $attempt++;
            }
        }

        return [
            'patient_id' => $patientId,
            'attempt' => $attempt,
        ];
    }

    private function isAlternativeCorrect(int $stepId, ?int $alternativeId): bool
    {
        $answerKey = AnswerKey::where('step_id', $stepId)->first();

        return ! empty($answerKey) && $answerKey->alternative_id === $alternativeId;
    }

    public function startAnswer(int $userId, int $stepId): Answer
    {
        $patientData = $this->getPatientIdAndCurrentAttempt($userId, $stepId);
        $patientId = $patientData['patient_id'];
        $attempt = $patientData['attempt'];

        return DB::transaction(function () use ($userId, $stepId, $attempt, $patientId) {
            $answer = Answer::where('user_id', $userId)
                ->where('step_id', $stepId)
                ->where('attempt', $attempt)
                ->whereNull('answered_at')
                ->latest('id')
                ->first();

            if (! $answer) {
                $answer = Answer::create([
                    'user_id' => $userId,
                    'step_id' => $stepId,
                    'patient_id' => $patientId,
                    'attempt' => $attempt,
                    'started_at' => now(),
                ]);
            } else {
                if (! $answer->attempt) {
                    $answer->attempt = $attempt;
                }
                if (! $answer->started_at) {
                    $answer->started_at = now();
                }
                $answer->save();
            }

            PatientMetric::updateMetricsForPatient($patientId);

            return $answer;
        });
    }

    public function finishAnswer(int $userId, int $stepId, ?int $alternativeId): Answer
    {
        $isCorrect = $this->isAlternativeCorrect($stepId, $alternativeId);

        return DB::transaction(function () use ($userId, $stepId, $alternativeId, $isCorrect) {
            $answer = Answer::where('user_id', $userId)
                ->where('step_id', $stepId)
                ->whereNotNull('started_at')
                ->whereNull('answered_at')
                ->latest('id')
                ->first();

            if (empty($answer)) {
                $patientData = $this->getPatientIdAndCurrentAttempt($userId, $stepId);
                $patientId = $patientData['patient_id'];
                $attempt = $patientData['attempt'];

                $answer = Answer::create([
                    'user_id' => $userId,
                    'step_id' => $stepId,
                    'patient_id' => $patientId,
                    'attempt' => $attempt,
                ]);
            } else {
                $patientId = $answer->patient_id;
            }

            $answer->alternative_id = $alternativeId ?? null;
            $answer->is_correct = $isCorrect;
            if (! $answer->started_at) {
                $answer->started_at = now();
            }
            $answer->answered_at = now();
            $answer->save();

            PatientMetric::updateMetricsForPatient($patientId);

            return $answer;
        });
    }

    public function storeAnswer(int $userId, int $stepId, ?int $alternativeId): Answer
    {
        $patientData = $this->getPatientIdAndCurrentAttempt($userId, $stepId);
        $patientId = $patientData['patient_id'];
        $attempt = $patientData['attempt'];
        $isCorrect = $this->isAlternativeCorrect($stepId, $alternativeId);

        return DB::transaction(function () use ($userId, $stepId, $patientId, $attempt, $alternativeId, $isCorrect) {
            $answer = Answer::create([
                'user_id' => $userId,
                'step_id' => $stepId,
                'patient_id' => $patientId,
                'attempt' => $attempt,
                'alternative_id' => $alternativeId ?? null,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]);

            PatientMetric::updateMetricsForPatient($patientId);

            return $answer;
        });
    }
}

