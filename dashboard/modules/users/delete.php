<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setAlert('success', 'User berhasil dihapus');
    } else {
        setAlert('error', 'Gagal menghapus user');
    }
}

header('Location: index.php?module=users');
exit();