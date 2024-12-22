<?php
// Mendeklarasikan variabel global dan memulai output buffering
global $usersList;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/users.css">

<div class="container">
    <div class="header-section d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="title">User Management</h1>
            <p class="subtitle">Manage and maintain user information</p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/users/create" class="btn-add">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>

    <div class="table-container">
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
            <?php if (empty($usersList)): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-users fa-3x"></i>
                            <p>No users have been added yet</p>
                            <a href="<?= $_SESSION['base_uri'] ?>/users/create" class="btn-add">
                                Add Your First User
                            </a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usersList as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                        <td><?= htmlspecialchars($user['fakultas_nama'] ?? '-') ?></td>
                        <td>
                            <?= !empty($user['last_login']) ? date('d M Y H:i', strtotime($user['last_login'])) : '-' ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= $_SESSION['base_uri'] ?>/users/edit?id=<?= $user['id'] ?>" 
                                   class="btn-edit" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-delete" onclick="confirmDelete(<?= $user['id'] ?>)" title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
    document.getElementById('deleteConfirmBtn').href = "<?= $_SESSION['base_uri'] ?>/users/delete?id=" + userId;
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

<?php
$content = ob_get_clean();
$pageTitle = "User Management";
$module = "users";
require __DIR__ . '/../../layouts/app.blade.php';
