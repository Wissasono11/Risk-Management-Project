<?php
global $fakultasList;
ob_start();
?>

<div class="form-container">
    <div class="form-header">
        <h1>Add New User</h1>
    </div>

    <form method="POST" action="<?= $_SESSION['base_uri'] ?>/users/store" class="user-form">
        <div class="form-group">
            <label class="required">Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label class="required">Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label class="required">Role</label>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="fakultas">Fakultas</option>
            </select>
        </div>

        <div id="fakultas-group" class="form-group" style="display: none;">
            <label class="required">Fakultas</label>
            <select name="fakultas_id">
                <option value="">Pilih Fakultas</option>
                <?php foreach($fakultasList as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save User</button>
            <a href="<?= $_SESSION['base_uri'] ?>/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/users-form.css">
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/users.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "Create User";
$module    = "users";
require __DIR__ . '/../../layouts/app.blade.php';
