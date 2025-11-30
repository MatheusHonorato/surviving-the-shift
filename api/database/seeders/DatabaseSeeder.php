<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Patient;
use App\Services\PatientSeederService;
use App\Services\TestSeederService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $patientSeederService = app(PatientSeederService::class);
        $testSeederService = app(TestSeederService::class);

        $patients = $this->seedPatients($patientSeederService);

        $testSeederService->createTest(
            array_map(fn (Patient $patient) => $patient->id, $patients),
            enabled: true
        );
    }

    private function seedPatients(PatientSeederService $patientSeederService): array
    {
        return [
            $patientSeederService->createPatient($this->getPatient1Data()),
            $patientSeederService->createPatient($this->getPatient2Data()),
            $patientSeederService->createPatient($this->getPatient3Data()),
            $patientSeederService->createPatient($this->getPatient4Data()),
            $patientSeederService->createPatient($this->getPatient5Data()),
        ];
    }

    private function getPatient1Data(): array
    {
        return [
            'video_url' => 'https://www.youtube.com/embed/LDi4Ith6sDY?si=ojFqEx5Tbc6XIFSE',
            'length' => 4,
            'environments' => [
                'Cronômetro' => '02:30',
                'Ritmo Cardíaco' => 'Fibrilação ventricular',
                'Frequência Cardiaca' => '0',
                'Frequência Respiratória' => '0',
                'Pressão Arterial' => '0',
                'Glasgow' => '3',
                'Temperatura' => '35ºC',
                'Diurese' => '0',
            ],
            'steps' => [
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                        'Anamnese',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Compressão torácica e ventilação',
                ],
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Anamnese',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Desfibrilação',
                ],
            ],
        ];
    }

    private function getPatient2Data(): array
    {
        return [
            'video_url' => 'https://www.youtube.com/embed/LDi4Ith6sDY?si=ojFqEx5Tbc6XIFSE',
            'length' => 10,
            'environments' => [
                'Cronômetro' => '02:30',
                'Ritmo Cardíaco' => 'Fibrilação ventricular',
                'Frequência Cardiaca' => '0',
                'Frequência Respiratória' => '0',
                'Pressão Arterial' => '0',
                'Glasgow' => '3',
                'Temperatura' => '35ºC',
                'Diurese' => '0',
            ],
            'steps' => [
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                        'Anamnese',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Compressão torácica e ventilação',
                ],
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Anamnese',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Desfibrilação',
                ],
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                        'Anamnese',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Compressão torácica e ventilação',
                ],
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Anamnese',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'POCUS',
                        'Exame físico',
                    ],
                    'response' => 'Procedimentos',
                ],
                [
                    'alternatives' => [
                        'Desfibrilação',
                        'Cardioversão',
                        'Compressão torácica e ventilação',
                        'Intubação orotraqueal',
                        'Toracocentese de alívio',
                        'Pericardiocentese',
                    ],
                    'response' => 'Desfibrilação',
                ],
                [
                    'alternatives' => [
                        'Procedimentos',
                        'Medicações',
                        'Exames laboratoriais',
                        'Exames de imagem',
                        'História clínica',
                        'Exame físico',
                    ],
                    'response' => 'Medicações',
                ],
                [
                    'alternatives' => [
                        'Adrenalina',
                        'Altoplato',
                        'Amiodarona',
                        'Atropina',
                        'Bicarbonato de sódio 8,4%',
                        'Cloreto de potássio 10%',
                        'Concentrado de hemácias',
                        'Diazepam',
                        'Dobutamina',
                        'Enxopirina',
                        'Etomidato',
                        'Glicose hipertônica',
                        'Gluconato de cálcio 10%',
                        'Heparina',
                        'Hidrocortisona',
                        'Menitol',
                        'Metoprolol 2mg/ml',
                        'Midazolam 15 mg',
                        'Midazolam 5 mg',
                        'Midazolam 50 mg',
                        'Nitroglicerina',
                        'Nitroprussiato',
                        'Noradrenalina',
                        'Propotol',
                        'Quetamina',
                        'Ringer lactato 1000ml',
                        'Ringer lactato 500ml',
                        'Rocurônio',
                        'Soro fisiológico 1000ml',
                        'Soro fisiológico 500ml',
                        'Soro glicado 5% 500ml',
                        'Suxametônio',
                        'Tenecteplase',
                        'Tiamina',
                    ],
                    'type' => 'medication',
                    'response' => 'Adrenalina',
                ],
            ],
        ];
    }

    private function getPatient3Data(): array
    {
        return [
            'video_url' => 'https://www.youtube.com/embed/LDi4Ith6sDY?si=ojFqEx5Tbc6XIFSE',
            'length' => 16,
            'environments' => [
                'Cronômetro' => '02:30',
                'Ritmo Cardíaco' => 'Fibrilação ventricular',
                'Frequência Cardiaca' => '0',
                'Frequência Respiratória' => '0',
                'Pressão Arterial' => '0',
                'Glasgow' => '3',
                'Temperatura' => '35ºC',
                'Diurese' => '0',
            ],
            'steps' => $this->getPatient3Steps(),
        ];
    }

    private function getPatient3Steps(): array
    {
        $medicationStep = $this->getMedicationStep();

        return [
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Adrenalina'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Amiodarona'],
        ];
    }

    private function getPatient4Data(): array
    {
        return [
            'video_url' => 'https://www.youtube.com/embed/LDi4Ith6sDY?si=ojFqEx5Tbc6XIFSE',
            'length' => 22,
            'environments' => [
                'Cronômetro' => '02:30',
                'Ritmo Cardíaco' => 'Fibrilação ventricular',
                'Frequência Cardiaca' => '0',
                'Frequência Respiratória' => '0',
                'Pressão Arterial' => '0',
                'Glasgow' => '3',
                'Temperatura' => '35ºC',
                'Diurese' => '0',
            ],
            'steps' => $this->getPatient4Steps(),
        ];
    }

    private function getPatient4Steps(): array
    {
        $medicationStep = $this->getMedicationStep();

        return [
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Adrenalina'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Amiodarona'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Adrenalina'],
        ];
    }

    private function getPatient5Data(): array
    {
        return [
            'video_url' => 'https://www.youtube.com/embed/LDi4Ith6sDY?si=ojFqEx5Tbc6XIFSE',
            'length' => 28,
            'environments' => [
                'Cronômetro' => '02:30',
                'Ritmo Cardíaco' => 'Fibrilação ventricular',
                'Frequência Cardiaca' => '0',
                'Frequência Respiratória' => '0',
                'Pressão Arterial' => '0',
                'Glasgow' => '3',
                'Temperatura' => '35ºC',
                'Diurese' => '0',
            ],
            'steps' => $this->getPatient5Steps(),
        ];
    }

    private function getPatient5Steps(): array
    {
        $medicationStep = $this->getMedicationStep();

        return [
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Adrenalina'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Amiodarona'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Adrenalina'],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                    'Anamnese',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Compressão torácica e ventilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Anamnese',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'POCUS',
                    'Exame físico',
                ],
                'response' => 'Procedimentos',
            ],
            [
                'alternatives' => [
                    'Desfibrilação',
                    'Cardioversão',
                    'Compressão torácica e ventilação',
                    'Intubação orotraqueal',
                    'Toracocentese de alívio',
                    'Pericardiocentese',
                ],
                'response' => 'Desfibrilação',
            ],
            [
                'alternatives' => [
                    'Procedimentos',
                    'Medicações',
                    'Exames laboratoriais',
                    'Exames de imagem',
                    'História clínica',
                    'Exame físico',
                ],
                'response' => 'Medicações',
            ],
            [...$medicationStep, 'response' => 'Amiodarona'],
        ];
    }

    private function getMedicationStep(): array
    {
        return [
            'alternatives' => [
                'Adrenalina',
                'Altoplato',
                'Amiodarona',
                'Atropina',
                'Bicarbonato de sódio 8,4%',
                'Cloreto de potássio 10%',
                'Concentrado de hemácias',
                'Diazepam',
                'Dobutamina',
                'Enxopirina',
                'Etomidato',
                'Glicose hipertônica',
                'Gluconato de cálcio 10%',
                'Heparina',
                'Hidrocortisona',
                'Menitol',
                'Metoprolol 2mg/ml',
                'Midazolam 15 mg',
                'Midazolam 5 mg',
                'Midazolam 50 mg',
                'Nitroglicerina',
                'Nitroprussiato',
                'Noradrenalina',
                'Propotol',
                'Quetamina',
                'Ringer lactato 1000ml',
                'Ringer lactato 500ml',
                'Rocurônio',
                'Soro fisiológico 1000ml',
                'Soro fisiológico 500ml',
                'Soro glicado 5% 500ml',
                'Suxametônio',
                'Tenecteplase',
                'Tiamina',
            ],
            'type' => 'medication',
        ];
    }
}
