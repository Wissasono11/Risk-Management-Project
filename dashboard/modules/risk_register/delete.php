<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check permission
    if (!canAccessRisk($id)) {
        setAlert('error', 'Access denied');
        header('Location: index.php');
        exit();
    }

    try {
        // Delete treatments first (foreign key constraint)
        $sql = "DELETE FROM risk_treatments WHERE risk_register_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Delete risk
        $sql = "DELETE FROM risk_registers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            setAlert('success', 'Risk berhasil dihapus');
        } else {
            setAlert('error', 'Gagal menghapus risk');
        }
    } catch (Exception $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();