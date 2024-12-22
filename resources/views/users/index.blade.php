<?php
global $usersList;
ob_start();
?>
<link rel="stylesheet" href="assets/css/users.css">
<h2>User Management</h2>
<button class="btn-add" 
        onclick="window.location='<?= $_SESSION['base_uri'] ?>/users/create'">
    Add New User
</button>

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
    <?php foreach($usersList as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= ucfirst($user['role']) ?></td>
            <td><?= htmlspecialchars($user['fakultas_nama'] ?? '-') ?></td>
            <td>
                <?php
                  if (!empty($user['last_login'])) {
                    echo date('d/m/Y H:i', strtotime($user['last_login']));
                  } else {
                    echo '-';
                  }
                ?>
            </td>
            <td>
                <button class="btn-edit"
                        onclick="window.location='<?= $_SESSION['base_uri'] ?>/users/edit?id=<?= $user['id'] ?>'">
                    Edit
                </button>
                <button class="btn-delete"
                        onclick="confirmDelete(<?= $user['id'] ?>)">
                    Delete
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

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
    document.getElementById('deleteConfirmBtn').href = 
        "<?= $_SESSION['base_uri'] ?>/users/delete?id=" + userId;
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
$pageTitle = "Users";
$module    = "users";
require __DIR__ . '/../../layouts/app.blade.php';
