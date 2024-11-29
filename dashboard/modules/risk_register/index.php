<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Get risks sesuai role
$risks = [];
if ($_SESSION['role'] === 'admin') {
    $risks = getRiskRegisters();
} else {
    $risks = getRiskRegisters($_SESSION['fakultas_id']);
}
?>

<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Risk Register</h1>
        <a href="create.php" class="btn btn-primary">Add New Risk</a>
    </div>

    <div class="content">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Objective</th>
                    <th>Risk Event</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>PIC</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($risks as $index => $risk): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($risk['objective']) ?></td>
                        <td><?= htmlspecialchars($risk['risk_event']) ?></td>
                        <td><?= htmlspecialchars($risk['kategori_nama']) ?></td>
                        <td>
                            <?php 
                            $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
                            ?>
                            <span class="badge badge-<?= $level['color'] ?>">
                                <?= $level['label'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
                        <td>
                            <a href="view.php?id=<?= $risk['id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="edit.php?id=<?= $risk['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $risk['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>