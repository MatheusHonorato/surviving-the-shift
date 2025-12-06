<?php

declare(strict_types=1);

use App\Models\Answer;
use App\Models\AnswerKey;
use App\Models\Alternative;
use App\Models\Patient;
use App\Models\PatientMetric;
use App\Models\Step;
use App\Models\User;
use App\Services\AnswerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->answerService = new AnswerService();
    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
});

test('startAnswer creates answer and updates PatientMetric correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step1 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);

    $answer = $this->answerService->startAnswer($this->user->id, $step1->id);

    expect($answer)
        ->toBeInstanceOf(Answer::class)
        ->and($answer->user_id)->toBe($this->user->id)
        ->and($answer->step_id)->toBe($step1->id)
        ->and($answer->patient_id)->toBe($patient->id)
        ->and($answer->attempt)->toBe(1)
        ->and($answer->started_at)->not->toBeNull()
        ->and($answer->answered_at)->toBeNull();

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(0)
        ->and($metric->users_completed)->toBe(0);
});

test('finishAnswer creates answer with response and updates metrics correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $alternative = Alternative::create(['step_id' => $step->id, 'description' => ['pt' => 'Teste', 'en' => 'Test']]);
    AnswerKey::create([
        'step_id' => $step->id,
        'alternative_id' => $alternative->id,
    ]);

    $answer = $this->answerService->finishAnswer(
        $this->user->id,
        $step->id,
        $alternative->id
    );

    expect($answer)
        ->toBeInstanceOf(Answer::class)
        ->and($answer->user_id)->toBe($this->user->id)
        ->and($answer->step_id)->toBe($step->id)
        ->and($answer->patient_id)->toBe($patient->id)
        ->and($answer->alternative_id)->toBe($alternative->id)
        ->and($answer->is_correct)->toBeTrue()
        ->and($answer->started_at)->not->toBeNull()
        ->and($answer->answered_at)->not->toBeNull();

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(1)
        ->and((float) $metric->avg_correct_rate)->toBe(1.0);
});

test('finishAnswer with incorrect response updates metrics correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $correctAlternative = Alternative::create(['step_id' => $step->id, 'description' => ['pt' => 'Correto', 'en' => 'Correct']]);
    $wrongAlternative = Alternative::create(['step_id' => $step->id, 'description' => ['pt' => 'Errado', 'en' => 'Wrong']]);
    AnswerKey::create([
        'step_id' => $step->id,
        'alternative_id' => $correctAlternative->id,
    ]);

    $answer = $this->answerService->finishAnswer(
        $this->user->id,
        $step->id,
        $wrongAlternative->id
    );

    expect($answer->is_correct)->toBeFalse();

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(1)
        ->and((float) $metric->avg_correct_rate)->toBe(0.0);
});

test('storeAnswer creates answer and updates metrics correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $alternative = Alternative::create(['step_id' => $step->id, 'description' => ['pt' => 'Teste', 'en' => 'Test']]);
    AnswerKey::create([
        'step_id' => $step->id,
        'alternative_id' => $alternative->id,
    ]);

    $answer = $this->answerService->storeAnswer(
        $this->user->id,
        $step->id,
        $alternative->id
    );

    expect($answer)
        ->toBeInstanceOf(Answer::class)
        ->and($answer->user_id)->toBe($this->user->id)
        ->and($answer->step_id)->toBe($step->id)
        ->and($answer->patient_id)->toBe($patient->id)
        ->and($answer->alternative_id)->toBe($alternative->id)
        ->and($answer->is_correct)->toBeTrue()
        ->and($answer->answered_at)->not->toBeNull();

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(1)
        ->and((float) $metric->avg_correct_rate)->toBe(1.0);
});

test('PatientMetric metrics reflect multiple correct answers', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step1 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $step2 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $step3 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);

    $alt1 = Alternative::create(['step_id' => $step1->id, 'description' => ['pt' => 'Alt 1', 'en' => 'Alt 1']]);
    $alt2 = Alternative::create(['step_id' => $step2->id, 'description' => ['pt' => 'Alt 2', 'en' => 'Alt 2']]);
    $alt3 = Alternative::create(['step_id' => $step3->id, 'description' => ['pt' => 'Alt 3', 'en' => 'Alt 3']]);

    AnswerKey::create(['step_id' => $step1->id, 'alternative_id' => $alt1->id]);
    AnswerKey::create(['step_id' => $step2->id, 'alternative_id' => $alt2->id]);
    AnswerKey::create(['step_id' => $step3->id, 'alternative_id' => $alt3->id]);

    $this->answerService->storeAnswer($this->user->id, $step1->id, $alt1->id);
    $this->answerService->storeAnswer($this->user->id, $step2->id, $alt2->id);
    $this->answerService->storeAnswer($this->user->id, $step3->id, $alt3->id);

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(1)
        ->and($metric->users_completed)->toBe(1)
        ->and((float) $metric->avg_correct_rate)->toBe(1.0);
});

test('PatientMetric metrics reflect mixed answers (correct and incorrect)', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step1 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $step2 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);

    $correctAlt1 = Alternative::create(['step_id' => $step1->id, 'description' => ['pt' => 'Correto 1', 'en' => 'Correct 1']]);
    $wrongAlt1 = Alternative::create(['step_id' => $step1->id, 'description' => ['pt' => 'Errado 1', 'en' => 'Wrong 1']]);
    $correctAlt2 = Alternative::create(['step_id' => $step2->id, 'description' => ['pt' => 'Correto 2', 'en' => 'Correct 2']]);

    AnswerKey::create(['step_id' => $step1->id, 'alternative_id' => $correctAlt1->id]);
    AnswerKey::create(['step_id' => $step2->id, 'alternative_id' => $correctAlt2->id]);

    $this->answerService->storeAnswer($this->user->id, $step1->id, $wrongAlt1->id);
    $this->answerService->storeAnswer($this->user->id, $step2->id, $correctAlt2->id);

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->users_attempted)->toBe(1)
        ->and($metric->users_completed)->toBe(0)
        ->and((float) $metric->avg_correct_rate)->toBe(0.5);
});

test('PatientMetric metrics calculate average time per step correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $alternative = Alternative::create(['step_id' => $step->id, 'description' => ['pt' => 'Teste', 'en' => 'Test']]);
    AnswerKey::create([
        'step_id' => $step->id,
        'alternative_id' => $alternative->id,
    ]);

    $answer = $this->answerService->startAnswer($this->user->id, $step->id);
    
    $answer->started_at = now()->subSeconds(2);
    $answer->save();
    
    $this->answerService->finishAnswer($this->user->id, $step->id, $alternative->id);

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->avg_step_time_sec)->toBeGreaterThanOrEqual(1);
});

test('PatientMetric metrics identify hardest step correctly', function () {
    $patient = Patient::create(['video_url' => 'test.mp4', 'length' => 100]);
    $step1 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);
    $step2 = Step::create(['patient_id' => $patient->id, 'type' => 'question']);

    $correctAlt1 = Alternative::create(['step_id' => $step1->id, 'description' => ['pt' => 'Correto 1', 'en' => 'Correct 1']]);
    $wrongAlt1 = Alternative::create(['step_id' => $step1->id, 'description' => ['pt' => 'Errado 1', 'en' => 'Wrong 1']]);
    $correctAlt2 = Alternative::create(['step_id' => $step2->id, 'description' => ['pt' => 'Correto 2', 'en' => 'Correct 2']]);
    $wrongAlt2 = Alternative::create(['step_id' => $step2->id, 'description' => ['pt' => 'Errado 2', 'en' => 'Wrong 2']]);

    AnswerKey::create(['step_id' => $step1->id, 'alternative_id' => $correctAlt1->id]);
    AnswerKey::create(['step_id' => $step2->id, 'alternative_id' => $correctAlt2->id]);

    $this->answerService->storeAnswer($this->user->id, $step1->id, $wrongAlt1->id);
    $this->answerService->storeAnswer($this->user->id, $step2->id, $wrongAlt2->id);
    $this->answerService->storeAnswer($this->user->id, $step2->id, $wrongAlt2->id);

    $metric = PatientMetric::where('patient_id', $patient->id)->first();
    expect($metric)->not->toBeNull()
        ->and($metric->hardest_step_id)->toBe($step2->id);
});

