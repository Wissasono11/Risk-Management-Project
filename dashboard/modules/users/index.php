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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">User Management</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Users List</h3>
            <button class="btn btn-success" onclick="window.location='index.php?module=users&action=create'">
                Add New User
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
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
                                <a href="index.php?module=users&action=edit&id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $user['id'] ?>)">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="deleteConfirmBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
