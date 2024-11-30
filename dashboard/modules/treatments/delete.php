<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
   $id = (int)$_GET['id'];
   
   // Get treatment data untuk cek permission dan return URL
   $sql = "SELECT t.*, r.id as risk_id 
           FROM risk_treatments t
           JOIN risk_registers r ON t.risk_register_id = r.id
           WHERE t.id = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $id);
   $stmt->execute();
   $treatment = $stmt->get_result()->fetch_assoc();

   if (!$treatment || !canAccessRisk($treatment['risk_id'])) {
       setAlert('error', 'Access denied');
       header('Location: index.php');
       exit();
   }

   try {
       // Delete timeline entries first
       $sql = "DELETE FROM mitigation_timeline WHERE treatment_id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $id);
       $stmt->execute();

       // Delete treatment
       $sql = "DELETE FROM risk_treatments WHERE id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $id);
       
       if ($stmt->execute()) {
           setAlert('success', 'Treatment berhasil dihapus');
       } else {
           setAlert('error', 'Gagal menghapus treatment');
       }

       // Return ke halaman detail risk
       header('Location: ../risk_register/view.php?id=' . $treatment['risk_id']);
       exit();
       
   } catch (Exception $e) {
       setAlert('error', 'Error: ' . $e->getMessage());
       header('Location: ../risk_register/view.php?id=' . $treatment['risk_id']);
       exit();
   }
}

// Jika tidak ada ID, redirect ke index
header('Location: index.php');
exit();