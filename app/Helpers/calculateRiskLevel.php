<?php
function calculateRiskLevel($likelihood, $impact) {
    $score = $likelihood * $impact;
    if ($score >= 16) return ['level' => 'ST','label' => 'Sangat Tinggi','color' => 'red'];
    if ($score >= 10) return ['level' => 'T','label' => 'Tinggi','color' => 'orange'];
    if ($score >= 5)  return ['level' => 'S','label' => 'Sedang','color' => 'yellow'];
    if ($score >= 1)  return ['level' => 'R','label' => 'Rendah','color' => 'green'];
    return ['level' => 'SR','label' => 'Sangat Rendah','color' => 'blue'];
}