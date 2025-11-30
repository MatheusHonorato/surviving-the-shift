<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\TranslationHelper;
use App\Models\Alternative;
use App\Models\AnswerKey;
use App\Models\Environment;
use App\Models\Patient;
use App\Models\Step;

class PatientSeederService
{
    public function createPatient(array $data): Patient
    {
        $patient = Patient::create([
            'video_url' => $data['video_url'],
            'length' => $data['length'],
        ]);

        $this->createEnvironments($patient, $data['environments'] ?? []);
        $this->createSteps($patient, $data['steps'] ?? []);

        return $patient;
    }

    private function createEnvironments(Patient $patient, array $environments): void
    {
        foreach ($environments as $key => $environmentValue) {
            $environment = $this->normalizeEnvironment($key, $environmentValue);
            Environment::create([
                'patient_id' => $patient->id,
                'name' => $environment['name'],
                'value' => $environment['value'],
            ]);
        }
    }

    private function normalizeEnvironment(string $key, mixed $environmentValue): array
    {
        if (is_array($environmentValue) && isset($environmentValue['name'], $environmentValue['value'])) {
            return $environmentValue;
        }

        return [
            'name' => TranslationHelper::toBilingual($key),
            'value' => is_array($environmentValue)
                ? $environmentValue
                : TranslationHelper::toBilingual((string) $environmentValue),
        ];
    }

    private function createSteps(Patient $patient, array $steps): void
    {
        foreach ($steps as $stepData) {
            $type = $stepData['type'] ?? 'default';
            $step = Step::create([
                'patient_id' => $patient->id,
                'type' => $type,
            ]);

            $alternatives = $this->normalizeAlternatives($stepData['alternatives'] ?? []);
            $response = $this->normalizeResponse($stepData['response'] ?? '');

            $this->createAlternativesAndAnswerKey($step, $alternatives, $response);
        }
    }

    private function normalizeAlternatives(array $alternatives): array
    {
        return array_map(function ($alt) {
            return is_array($alt) ? $alt : TranslationHelper::toBilingual($alt);
        }, $alternatives);
    }

    private function normalizeResponse(mixed $response): array
    {
        return is_array($response)
            ? $response
            : TranslationHelper::toBilingual((string) $response);
    }

    private function createAlternativesAndAnswerKey(Step $step, array $alternatives, array $response): void
    {
        foreach ($alternatives as $alternative) {
            $alternativeModel = Alternative::create([
                'step_id' => $step->id,
                'description' => $alternative,
            ]);

            if ($response['pt'] === $alternative['pt']) {
                AnswerKey::create([
                    'step_id' => $step->id,
                    'alternative_id' => $alternativeModel->id,
                ]);
            }
        }
    }
}

