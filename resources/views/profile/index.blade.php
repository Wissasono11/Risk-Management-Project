<?php
global $profileUser;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/profile.css">

<h2>Profile</h2>

<div class="profile-container">
    <div class="profile-section">
        <div class="profile-card">
            <div class="profile-picture">
                <img src="<?= $_SESSION['base_uri'] ?>/assets/img/picture.jpg" alt="Profile Picture">
                <div class="edit-avatar" onclick="document.getElementById('profilePicture').click()">
                <i class="fas fa-camera"></i>
                <input type="file" id="profilePicture" hidden accept="image/*" 
                       onchange="handleProfilePictureChange(this)">
                </div>
            </div>
            <h4 class="profile-name"><?= htmlspecialchars($profileUser['email']) ?></h4>
            <p class="profile-role"><?= htmlspecialchars($profileUser['role']) ?></p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Risks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Mitigations</div>
                </div>
            </div>
        </div>

        <div class="profile-details">
            <div class="info-card">
                <div class="card-header">
                    <h4>Profile Information</h4>
                    <button onclick="window.location.href='<?= $_SESSION['base_uri'] ?>/profile/edit'" class="btn btn-edit">
                        <i class="fas fa-pen"></i> Edit Profile
                    </button>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($profileUser['email']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Role</div>
                        <div class="info-value"><?= ucfirst(htmlspecialchars($profileUser['role'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fakultas</div>
                        <div class="info-value"><?= htmlspecialchars($profileUser['fakultas_nama'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            <?php 
                            if (!empty($profileUser['last_login'])) {
                                echo date('d M Y H:i', strtotime($profileUser['last_login']));
                            } else {
                                echo '-';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card activity-log">
                <div class="card-header">
                    <h4>Recent Activity</h4>
                </div>
                
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Last Login</div>
                            <div class="activity-time">
                                <?= !empty($profileUser['last_login']) ? date('d M Y H:i', strtotime($profileUser['last_login'])) : '-' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$pageTitle = "My Profile";
$module    = "profile";
require __DIR__ . '/../../layouts/app.blade.php';
