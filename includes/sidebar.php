<div class="sidebar">
    <div class="logo">Risk Management</div>
    <div class="menu">
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="modules/risk_register/list.php"><i class="fas fa-list"></i> Risk Register</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="modules/reports/index.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="modules/users/index.php"><i class="fas fa-users"></i> Users</a>
        <?php endif; ?>
    </div>
</div>