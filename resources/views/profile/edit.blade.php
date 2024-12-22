<?php
global $profileUser;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/profile.css">

<div class="form-container">
    <div class="form-header">
        <h2>Edit Profile</h2>
        <a href="<?= $_SESSION['base_uri'] ?>/profile" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form method="POST" action="<?= $_SESSION['base_uri'] ?>/profile/update" enctype="multipart/form-data" class="profile-form">
        <div class="form-section">
            <div class="profile-upload">
                <div class="current-photo">
                    <img src="<?= $_SESSION['base_uri'] ?>/uploads/profiles/<?= $profileUser['profile_picture'] ?? 'default.jpg' ?>" 
                         alt="Profile" id="profilePreview">
                </div>
                <div class="upload-controls">
                    <label for="profile_picture" class="btn btn-primary">
                        <i class="fas fa-camera"></i> Change Photo
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" hidden>
                    <p class="upload-hint">JPG, PNG or WEBP, max 2MB</p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Faculty</label>
                <input type="text" value="<?= htmlspecialchars($profileUser['fakultas_nama'] ?? '-') ?>" readonly>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= ucfirst(htmlspecialchars($profileUser['role'])) ?>" readonly>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= $_SESSION['base_uri'] ?>/profile" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // File size check (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = "Edit Profile";
$module = "profile";
require __DIR__ . '/../../layouts/app.blade.php';
?>