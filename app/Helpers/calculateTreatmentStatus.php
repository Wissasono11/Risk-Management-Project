<?php
function calculateTreatmentStatus($treatment) {
    if (empty($treatment['timelines'])) {
        return 'pending';
    }

    $allCompleted = true;
    $hasOngoing = false;
    $hasOverdue = false;
    $currentYear = date('Y');
    $currentQuarter = ceil(date('n') / 3);

    foreach ($treatment['timelines'] as $timeline) {
        if ($timeline['realisasi']) {
            continue;
        }
        // Tentukan apakah timeline sudah melewati triwulan saat ini
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

function canAccessRisk($riskId) {
    $userFakultasId = $_SESSION['fakultas_id'] ?? null;
    if (!$userFakultasId) {
        return false;
    }

    $riskService = new \App\Services\RiskService();
    $risk = $riskService->findById($riskId);

    if (!$risk) {
        return false;
    }

    return $risk['fakultas_id'] === $userFakultasId;
}

?>