<?php
require_once __DIR__ . '/../../app/Helpers/getRiskStats.php';
$showRiskSummary = true;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>BBP - <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/responsive.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Poppins:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- side bar -->
    <div class="sidebar">
        </label>
        <div class="sidebar-brand">
            <h2><span>Management Risk</span></h2>
        </div>
        
        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/dashboard" 
                       class="<?= ($module ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <span class="fas fa-home"></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/risk-register"
                       class="<?= ($module ?? '') === 'risk_register' ? 'active' : '' ?>">
                        <span class="fas fa-tasks"></span>
                        <span>Risk Register</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/treatments"
                    class="<?= ($module ?? '') === 'treatments' ? 'active' : '' ?>">
                        <span class="fas fa-tools"></span>
                        <span>Treatments</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/reports"
                    class="<?= ($module ?? '') === 'reports' ? 'active' : '' ?>">
                        <span class="fas fa-file-alt"></span>
                        <span>Reports</span>
                    </a>
                </li>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li>
                        <a href="<?= $_SESSION['base_uri'] ?>/users"
                        class="<?= ($module ?? '') === 'users' ? 'active' : '' ?>">
                            <span class="fas fa-users"></span>
                            <span>Manage Users</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/profile"
                    class="<?= ($module ?? '') === 'profile' ? 'active' : '' ?>">
                        <span class="fas fa-user-alt"></span>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $_SESSION['base_uri'] ?>/logout" onclick="return confirmLogout()">
                       <span class="fas fa-sign-out-alt"></span>
                       <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- main content -->
    <div class="main-content">
    <header class="top-header">
    <div class="header-left">
        <h2>
            <label for="nav-toggle" id="sidebar-toggle">
                <span class="las la-bars"></span>
            </label>
            <span class="dashboard-text"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
        </h2>
    </div>

             <!-- Risk Summary -->
            <?php if (isset($showRiskSummary) && $showRiskSummary): ?>
            <div class="risk-summary">
                <div class="summary-item">
                    <span class="las la-calendar"></span>
                    <span>Period: <?= date('Y') ?></span>
                </div>
                <div class="summary-item">
                    <span class="las la-sync"></span>
                    <span>Updated: <?= isset($stats['last_update']) ? date('d M Y', strtotime($stats['last_update'])) : date('d M Y') ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- User Profile -->            
            <div class="user-wrapper">
                <img src="<?= $_SESSION['base_uri'] ?>/uploads/profiles/<?= $_SESSION['user_profile_picture'] ?? 'default.jpg' ?>" 
                        width="30px" 
                        height="30px" 
                        alt="Profile picture"
                        class="profile-picture">
                    <div>
                    <h4><?= htmlspecialchars($_SESSION['user_email']) ?></h4>
                    <small><?= $_SESSION['user_role'] === 'admin' ? 'Administrator' : ($fakultasName ?? 'Faculty User') ?></small>
                </div>
            </div>
        </header>

        <main class="content-wrapper">
            <?php if (isset($showBreadcrumb) && $showBreadcrumb): ?>
            <div class="breadcrumb">
                <a href="<?= $_SESSION['base_uri'] ?>/dashboard">Dashboard</a>
                <?php if ($pageTitle !== 'Dashboard'): ?>
                <i class="fas fa-chevron-right"></i>
                <span><?= htmlspecialchars($pageTitle) ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Page content -->
            <?= $content ?? '' ?>
        </main>
    </div>
    <!-- Script -->
    <script src="<?= $_SESSION['base_uri'] ?>/assets/js/layout.js"></script>
    <script src="<?= $_SESSION['base_uri'] ?>/assets/js/logout.js"></script>
</body>
</html>
