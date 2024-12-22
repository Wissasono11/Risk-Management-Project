<?php
namespace App\Helpers;

function canAccessRisk($risk_id)
{
    $userRole = $_SESSION['user_role'] ?? 'guest';
    $fakultasId = $_SESSION['fakultas_id'] ?? null;


    if ($userRole === 'admin') {
        return true; 
    }

    if ($userRole === 'fakultas' && $fakultasId) {
        return checkRiskOwnership($risk_id, $fakultasId);
    }

    return false;
}

function checkRiskOwnership($risk_id, $fakultasId)
{
    // Logika untuk cek apakah risk_id milik fakultasId
    $db = \Config\Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM risk_registers WHERE id = ? AND fakultas_id = ?");
    $stmt->bind_param("ii", $risk_id, $fakultasId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0] > 0;
}