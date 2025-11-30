<?php

declare(strict_types=1);

namespace App\Helpers;

class TranslationHelper
{
    private const TRANSLATION_MAP = [
        'Cronômetro' => 'Timer',
        'Ritmo Cardíaco' => 'Cardiac rhythm',
        'Frequência Cardiaca' => 'Heart rate',
        'Frequência Respiratória' => 'Respiratory rate',
        'Pressão Arterial' => 'Blood pressure',
        'Glasgow' => 'Glasgow',
        'Temperatura' => 'Temperature',
        'Diurese' => 'Urine output',
        'Glicemia' => 'Blood glucose',
        'Fibrilação ventricular' => 'Ventricular fibrillation',

        'Procedimentos' => 'Procedures',
        'Medicações' => 'Medications',
        'Exames laboratoriais' => 'Laboratory tests',
        'Exames de imagem' => 'Imaging exams',
        'História clínica' => 'Medical history',
        'Exame físico' => 'Physical examination',
        'POCUS' => 'POCUS',
        'Anamnese' => 'Anamnesis',
        'Medicações e Procedimentos' => 'Medications and Procedures',

        'Desfibrilação' => 'Defibrillation',
        'Cardioversão' => 'Cardioversion',
        'Intubação orotraqueal' => 'Orotracheal intubation',
        'Ventilação com ambu' => 'Bag-valve-mask ventilation',
        'Compressão torácica e ventilação' => 'Chest compressions and ventilation',
        'Toracocentese de alívio' => 'Needle thoracostomy',
        'Pericardiocentese' => 'Pericardiocentesis',

        'ECG' => 'ECG',
        'Tomografia de tórax' => 'Chest CT',
        'TC de abdome total' => 'Abdominal CT',
        'Rx de tórax' => 'Chest X-ray',
        'Angio TC de tórax' => 'Chest CT angiography',
        'Endoscopia digestiva' => 'Upper GI endoscopy',
        'Colonoscopia' => 'Colonoscopy',
        'Ecocardiograma' => 'Echocardiogram',
        'Angiocoronografia' => 'Coronary angiography',

        'Atropina' => 'Atropine',
        'Amiodarona' => 'Amiodarone',
        'Adrenalina' => 'Epinephrine',
        'Altoplato' => 'Alteplase',
        'Diazepam' => 'Diazepam',
        'Dobutamina' => 'Dobutamine',
        'Enxopirina' => 'Enoxaparin',
        'Etomidato' => 'Etomidate',
        'Glicose hipertônica' => 'Hypertonic glucose',
        'Gluconato de cálcio 10%' => 'Calcium gluconate 10%',
        'Heparina' => 'Heparin',
        'Hidrocortisona' => 'Hydrocortisone',
        'Nitroglicerina' => 'Nitroglycerin',
        'Nitroprussiato' => 'Nitroprusside',
        'Noradrenalina' => 'Norepinephrine',
        'Propotol' => 'Propofol',
        'Quetamina' => 'Ketamine',
        'Rocurônio' => 'Rocuronium',
        'Suxametônio' => 'Succinylcholine',
        'Tenecteplase' => 'Tenecteplase',
        'Tiamina' => 'Thiamine',
        'Soro fisiológico 500ml' => 'Normal saline 500 ml',
        'Soro fisiológico 1000ml' => 'Normal saline 1000 ml',
        'Ringer lactato 1000ml' => 'Lactated Ringer 1000 ml',
        'Ringer lactato 500ml' => 'Lactated Ringer 500 ml',
        'Bicarbonato de sódio 8,4%' => 'Sodium bicarbonate 8.4%',
        'Cloreto de potássio 10%' => 'Potassium chloride 10%',
        'Midazolam 5mg' => 'Midazolam 5 mg',
        'Midazolam 15mg' => 'Midazolam 15 mg',
        'Midazolam 50mg' => 'Midazolam 50 mg',
        'Midazolam 5 mg' => 'Midazolam 5 mg',
        'Midazolam 15 mg' => 'Midazolam 15 mg',
        'Midazolam 50 mg' => 'Midazolam 50 mg',
        'Metoprolol 2mg/ml' => 'Metoprolol 2 mg/ml',
        'Concentrado de hemácias' => 'Packed red blood cells',
        'Menitol' => 'Mannitol',
        'Soroglicado 5% 500ml' => 'Dextrose 5% 500 ml',
        'Soro glicado 5% 500ml' => 'Dextrose 5% 500 ml',

        'Ureia' => 'Urea',
        'Hemograma' => 'Complete blood count',
        'Creatinina' => 'Creatinine',
        'Potássio' => 'Potassium',
        'Sódio' => 'Sodium',
        'Magnésio' => 'Magnesium',
        'Cálcio iônico' => 'Ionized calcium',
        'Proteína c reativa' => 'C-reactive protein',
        'Gasometria arterial' => 'Arterial blood gas',
        'PTTA' => 'aPTT',
        'Atividade de protrombina' => 'Prothrombin activity',
        'TGO' => 'AST',
        'TGP' => 'ALT',
        'Bilirrubina' => 'Bilirubin',
        'Fribrinogênio' => 'Fibrinogen',
        'Troponina' => 'Troponin',
        'Dimero' => 'D-dimer',
        '0np.' => '0np.',

        'Desfibrilação e Adrenalina' => 'Defibrillation and Epinephrine',
        'Desfibrilação e Atropina' => 'Defibrillation and Atropine',
        'Desfibrilação e Amiodarona' => 'Defibrillation and Amiodarone',
        'Cardioversão e Soro fisiológico 500ml' => 'Cardioversion and Normal saline 500 ml',
        'Toracocentese de alívio e Soro fisiológico 1000ml' => 'Needle thoracostomy and Normal saline 1000 ml',
        'Desfibrilação e Ringer lactato 1000ml' => 'Defibrillation and Lactated Ringer 1000 ml',
        'Toracocentese de alívio e Cloreto de potássio 10%' => 'Needle thoracostomy and Potassium chloride 10%',
    ];

    public static function translatePtToEn(string $text): string
    {
        return self::TRANSLATION_MAP[$text] ?? $text;
    }

    public static function toBilingual(string $text): array
    {
        return [
            'pt' => $text,
            'en' => self::translatePtToEn($text),
        ];
    }
}

