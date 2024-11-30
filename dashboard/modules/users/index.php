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

// Get users list
$sql = "SELECT u.*, f.nama as fakultas_nama 
        FROM users u 
        LEFT JOIN fakultas f ON u.fakultas_id = f.id
        ORDER BY u.created_at DESC";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../../assets/css/users.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="title">User Management</h1>

        <div class="header-section">
            <h3 class="section-title">Users List</h3>
            <a href="index.php?module=users&action=create" class="btn-add">Add New User</a>
        </div>

        <div class="table-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Fakultas</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= ucfirst($user['role']) ?></td>
                            <td><?= $user['fakultas_nama'] ?? '-' ?></td>
                            <td><?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?module=users&action=edit&id=<?= $user['id'] ?>" class="btn-edit">Edit</a>
                                    <button class="btn-delete" onclick="confirmDelete(<?= $user['id'] ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="deleteConfirmBtn" href="#" class="btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
            deleteConfirmBtn.href = `index.php?module=users&action=delete&id=${userId}`;
            deleteModal.show();
        }
    </script>
</body>
</html>