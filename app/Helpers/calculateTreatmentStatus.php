<?php
function calculateTreatmentStatus($treatment) {
    // Decode timelines jika berupa string JSON
    if (is_string($treatment['timelines'])) {
        $treatment['timelines'] = json_decode($treatment['timelines'], true);
    }

    if (empty($treatment['timelines']) || !is_array($treatment['timelines'])) {
        error_log("Timelines empty or invalid for treatment ID: " . ($treatment['id'] ?? 'unknown'));
        return 'pending';
    }

    $allCompleted = true;
    $hasOngoing = false;
    $hasOverdue = false;
    $currentYear = date('Y');
    $currentQuarter = ceil(date('n') / 3);

    foreach ($treatment['timelines'] as $timeline) {
        if (!isset($timeline['realisasi'])) {
            continue; // Abaikan entri tanpa realisasi
        }

        if ($timeline['realisasi'] == '1') {
            continue; // Sudah selesai
        }

        if ($timeline['tahun'] < $currentYear || 
            ($timeline['tahun'] == $currentYear && $timeline['triwulan'] < $currentQuarter)) {
            $hasOverdue = true;
        } else {
            $hasOngoing = true;
        }
        $allCompleted = false;
    }

    if ($allCompleted) {
        return 'completed';
    } elseif ($hasOverdue) {
        return 'overdue';
    } elseif ($hasOngoing) {
        return 'ongoing';
    } else {
        return 'pending';
    }
}

?>