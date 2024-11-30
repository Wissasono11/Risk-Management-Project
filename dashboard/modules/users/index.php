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
<link rel="stylesheet" href="../../../assets/css/users.css">

<div class="content">
    <div class="header-section">
        <h2>User Management</h2>
        <button class="btn-add" onclick="window.location='index.php?module=users&action=create'">
            Add New User
        </button>
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
                        <td class="action-buttons">
                            <button class="btn-edit" onclick="window.location='index.php?module=users&action=edit&id=<?= $user['id'] ?>'">
                                Edit
                            </button>
                            <button class="btn-delete" onclick="confirmDelete(<?= $user['id'] ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Delete -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this user?</p>
        <div class="modal-footer">
            <button onclick="closeModal()" class="btn-cancel">Cancel</button>
            <a id="deleteConfirmBtn" href="#" class="btn-confirm">Delete</a>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId) {
    document.getElementById('deleteModal').classList.add('open');
    document.getElementById('deleteConfirmBtn').href = "index.php?module=users&action=delete&id=" + userId;
}

function closeModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
window.onclick = function(event) {
    if (event.target == document.getElementById('deleteModal')) {
        closeModal();
    }
}
</script>
