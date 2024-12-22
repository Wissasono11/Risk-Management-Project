<?php
function calculateRiskLevel($likelihood, $impact) {
    $score = $likelihood * $impact;
    if (
        ($impact == 5 && $likelihood == 1) ||
        ($impact == 1 && $likelihood == 5)
    ) {
        return [
            'level' => 'High',
            'label' => 'High',
            'color' => '#ea580c',
            'background' => '#ffebee'
        ];
    }

    if ($score >= 16) {
        return [
            'level' => 'Very-High',
            'label' => 'Very High',
            'color' => '#dc2626',
            'background' => '#8345ef'
        ];
    }

    if ($score >= 10) { 
        return [
            'level' => 'High',
            'label' => 'High',
            'color' => '#ea580c',
            'background' => '#ffebee'
        ];
    }

    if ($score >= 6) { 
        return [
            'level' => 'Medium',
            'label' => 'Medium',
            'color' => '#ca8a04',
            'background' => '#f1f8e9'
        ];
    }

    return [
        'level' => 'Low',
        'label' => 'Low',
        'color' => '#16a34a',
        'background' => '#e8f5e9'
    ];
}
?>