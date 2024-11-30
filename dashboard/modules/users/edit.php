<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Cek role admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    setAlert('error', 'User tidak ditemukan');
    header('Location: index.php?module=users');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email']);
    $role = cleanInput($_POST['role']);
    $fakultas_id = ($role == 'fakultas') ? (int)$_POST['fakultas_id'] : null;

    // Update user
    if (!empty($_POST['password'])) {
        // Jika password diubah
        $password = hash('sha256', $_POST['password']);
        $sql = "UPDATE users SET email=?, password=?, role=?, fakultas_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $email, $password, $role, $fakultas_id, $id);
    } else {
        // Jika password tidak diubah
        $sql = "UPDATE users SET email=?, role=?, fakultas_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $email, $role, $fakultas_id, $id);
    }

    if ($stmt->execute()) {
        setAlert('success', 'User berhasil diupdate');
        header('Location: index.php?module=users');
        exit();
    } else {
        setAlert('error', 'Gagal mengupdate user');
    }
}

// Get fakultas list untuk dropdown
$fakultas = $conn->query("SELECT * FROM fakultas ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
?>

[Form edit mirip dengan create, tapi field password optional]