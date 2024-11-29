<?php
require_once '../config/database.php';
require_once '../middleware/auth.php';
checkAuth();

// Get fakultas name if fakultas role
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Risk Management UIN Sunan Kalijaga</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #1a3c40;
            padding: 20px;
            color: white;
        }

        .sidebar .logo {
            font-size: 24px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #2d5a5e;
        }

        .sidebar .menu a {
            display: block;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar .menu a:hover {
            background: #2d5a5e;
        }

        .sidebar .menu i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-info .avatar {
            width: 40px;
            height: 40px;
            background: #1a3c40;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1a3c40;
        }

        .recent-risks {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .recent-risks h2 {
            margin-bottom: 20px;
            color: #1a3c40;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            Risk Management
        </div>
        <div class="menu">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="#"><i class="fas fa-chart-bar"></i> Risk Assessment</a>
            <a href="#"><i class="fas fa-tasks"></i> Mitigation Plans</a>
            <a href="#"><i class="fas fa-file-alt"></i> Reports</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="#"><i class="fas fa-users"></i> Manage Users</a>
            <?php endif; ?>
            <a href="#"><i class="fas fa-cog"></i> Settings</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="user-info">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['email'], 0, 1)); ?>
                </div>
                <div>
                    <h3><?php echo htmlspecialchars($_SESSION['email']); ?></h3>
                    <small><?php echo $fakultasName ? $fakultasName : 'Administrator'; ?></small>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Risks</h3>
                <div class="value">12</div>
            </div>
            <div class="stat-card">
                <h3>High Priority</h3>
                <div class="value">3</div>
            </div>
            <div class="stat-card">
                <h3>In Progress</h3>
                <div class="value">5</div>
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <div class="value">4</div>
            </div>
        </div>

        <div class="recent-risks">
            <h2>Recent Risk Assessments</h2>
            <!-- You can add a table or list of recent risks here -->
            <p style="color: #666; text-align: center; padding: 20px;">
                No recent risk assessments found.
            </p>
        </div>
    </div>
</body>
</html>
