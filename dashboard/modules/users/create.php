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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email']);
    $password = hash('sha256', $_POST['password']);
    $role = cleanInput($_POST['role']);
    $fakultas_id = ($role == 'fakultas') ? (int)$_POST['fakultas_id'] : null;

    $sql = "INSERT INTO users (email, password, role, fakultas_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $email, $password, $role, $fakultas_id);

    if ($stmt->execute()) {
        setAlert('success', 'User berhasil ditambahkan');
        header('Location: index.php?module=users');
        exit();
    } else {
        setAlert('error', 'Gagal menambahkan user');
    }
}

// Get fakultas list untuk dropdown
$fakultas = $conn->query("SELECT * FROM fakultas ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="../../../assets/css/users-create.css">
</head>
<body>
    <div class="container my-5">
        <div class="header-section">
            <h1 class="title">Add New User</h1>
            <a href="index.php?module=users" class="btn-back">Back</a>
        </div>

        <div class="form-section">
            <form method="POST" class="form">
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" required class="form-control" onchange="toggleFakultas(this.value)">
                        <option value="admin">Admin</option>
                        <option value="fakultas">Fakultas</option>
                    </select>
                </div>

                <div class="form-group" id="fakultas-group" style="display: none;">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas_id" class="form-control">
                        <option value="">Pilih Fakultas</option>
                        <?php foreach($fakultas as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Save User</button>
            </form>
        </div>
    </div>

    <script>
        function toggleFakultas(role) {
            document.getElementById('fakultas-group').style.display = 
                role === 'fakultas' ? 'block' : 'none';
        }
    </script>
</body>
</html>