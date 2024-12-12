<?php
    require_once '../config/database.php';
    require_once '../middleware/auth.php';
    require_once '../includes/functions.php';
    checkAuth();

    $fakultasName = '';
    if ($_SESSION['fakultas_id']) {
        $stmt = $conn->prepare("SELECT nama FROM fakultas WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['fakultas_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $fakultasName = $row['nama'];
        }
    }

    $module = isset($_GET['module']) ? $_GET['module'] : 'default';
    $modulePath = __DIR__ . "/modules/$module/index.php";

    $module = isset($_GET['module']) ? $_GET['module'] : 'default';
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';

    $modulePath = __DIR__ . "/modules/$module/";
    if ($action != 'index') {
        $modulePath .= "$action.php";
    } else {
        $modulePath .= "index.php";
    }

    $sql = "SELECT 
        COUNT(*) as total_risks,
        SUM(CASE WHEN likelihood_inherent * impact_inherent >= 15 THEN 1 ELSE 0 END) as high_risks,
        MAX(updated_at) as last_update
    FROM risk_registers";

    if ($_SESSION['fakultas_id']) {
        $sql .= " WHERE fakultas_id = " . $_SESSION['fakultas_id'];
    }

    $stats = $conn->query($sql)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Dashboard</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
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
    <input type="checkbox" id="nav-toggle">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h1><span class="fa fa-ge"></span></h1>
            <h2><span>Management Risk</span></h2>
        </div>
        
        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="index.php" class="<?= $module == 'default' ? 'active' : '' ?>">
                        <span class="fas fa-home"></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?module=risk_register" class="<?= $module == 'risk_register' ? 'active' : '' ?>">
                        <span class="fas fa-tasks"></span>
                        <span>Risk Register</span></a>
                </li>
                <li>
                    <a href="index.php?module=treatments" class="<?= $module == 'treatments' ? 'active' : '' ?>">
                        <span class="fas fa-tools"></span>
                        <span>Treatments</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?module=reports" class="<?= $module == 'reports' ? 'active' : '' ?>">
                        <span class="fas fa-file-alt"></span>
                        <span>Reports</span>
                    </a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a href="index.php?module=users" class="<?= $module == 'users' ? 'active' : '' ?>">
                            <span class="fas fa-users"></span>
                            <span>Manage Users</span>
                        </a>
                    </li>
                <?php endif; ?>
                    <li>
                        <a href="index.php?module=profile" class="<?= $module == 'profile' ? 'active' : '' ?>">
                            <span class="fas fa-user-alt"></span>
                            <span>Profile</span></a>
                    </li>
                    <li>
                        <a href="logout.php" onclick="return confirmLogout()">
                            <span class="fas fa-sign-out-alt"></span>
                            <span>Logout</span>
                        </a>
                    </li>
            </ul>
        </div>
    </div>

    <!-- main content -->
    <div class="main-content">
        <header>
            <h2>
                <label for="nav-toggle">
                    <span class="las la-bars"></span>
                </label>
                Dashboard
            </h2>  

            <!-- Risk Summary -->
            <div class="risk-summary">
                <div class="summary-item">
                  <span class="las la-calendar"></span>
                 <span>Period: <?= date('Y') ?></span>
                </div>
                <div class="summary-item <?= ($stats['high_risks'] ?? 0) > 0 ? 'warning' : 'safe' ?>">
                    <span class="las la-exclamation-circle"></span>
                    <span><?= $stats['high_risks'] ?? 0 ?> Critical Risks</span>
                </div>
        
                <div class="summary-item">
                    <span class="las la-sync"></span>
                    <span>Updated: <?= $stats['last_update'] ? date('d M Y', strtotime($stats['last_update'])) : date('d M Y') ?></span>
                </div>
            </div>
            
            <div class="user-wrapper">
                <img src="../assets/img/picture.jpg" width="30px" height="30px">
                <div>
                    <h4><?= htmlspecialchars($_SESSION['email']) ?></h4>
                    <small><?= $fakultasName ? $fakultasName : 'Administrator' ?></small>
                </div>
            </div>
        </header>   

        <main>
            <?php
            if (file_exists($modulePath)) {
                include $modulePath;
            } else {
                include __DIR__ . '/modules/default/index.php';
            }
            ?>
        </main>
    </div>

    <!-- Script -->
    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/logout.js"></script>
</body>
</html>   


