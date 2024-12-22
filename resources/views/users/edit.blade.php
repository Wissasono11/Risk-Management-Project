<?php
global $userEdit, $fakultasList;
ob_start();
?>

<!-- edit.blade.php -->
<div class="form-container">
    <div class="form-header">
        <h1>Edit User</h1>
    </div>

    <form method="POST" action="<?= $_SESSION['base_uri'] ?>/users/update" class="user-form">
        <input type="hidden" name="id" value="<?= $userEdit['id'] ?>">
        
        <div class="form-group">
            <label class="required">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($userEdit['email']) ?>">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <div class="form-group">
            <label class="required">Role</label>
            <select name="role" id="roleSelect">
                <option value="admin" <?= ($userEdit['role']=='admin' ? 'selected':'') ?>>Admin</option>
                <option value="fakultas" <?= ($userEdit['role']=='fakultas' ? 'selected':'') ?>>Fakultas</option>
            </select>
        </div>

        <div id="fakultas-group" class="form-group">
            <label class="required">Fakultas</label>
            <select name="fakultas_id">
                <option value="">Select Faculty</option>
                <?php foreach($fakultasList as $f): ?>
                    <option value="<?= $f['id'] ?>" 
                            <?= ($userEdit['fakultas_id']==$f['id']?'selected':'') ?>>
                        <?= htmlspecialchars($f['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="<?= $_SESSION['base_uri'] ?>/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/users-form.css">
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/users.js"></script>>


<?php
$content = ob_get_clean();
$pageTitle = "Edit User";
$module    = "users";
require __DIR__ . '/../../layouts/app.blade.php';
