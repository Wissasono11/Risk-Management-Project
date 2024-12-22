<?php
global $profileUser;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/profile.css">

<div class="profile-container">
    <div class="profile-header">
        <div class="header-left">
            <h1 class="page-title">Profile</h1>
            <p class="header-subtitle">View and Customize your Profile</p>
            </div>
        </div>
        <div class="profile-section">
            <div class="profile-card">
                <div class="profile-picture">
                    <img src="<?= $_SESSION['base_uri'] ?>/uploads/profiles/<?= $profileUser['profile_picture'] ?? 'default.jpg' ?>" 
                        alt="Profile Picture"
                        class="profile-picture">
                </div>
                <h4 class="profile-name"><?= htmlspecialchars($profileUser['email']) ?></h4>
                <p class="profile-role"><?= htmlspecialchars($profileUser['role']) ?></p>
                
                <div class="profile-stats">
        <div class="stat-item">
            <div class="stat-value">
                <?= htmlspecialchars($profileUser['total_risks'] ?? 0) ?>
            </div>
            <div class="stat-label">Total Risks</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                <?= htmlspecialchars($profileUser['total_mitigations'] ?? 0) ?>
            </div>
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
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/profile.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "My Profile";
$module    = "profile";
require __DIR__ . '/../../layouts/app.blade.php';