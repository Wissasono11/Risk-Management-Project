<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Get risk registers
$sql = "SELECT r.*, rc.nama as kategori_nama 
        FROM risk_registers r
        LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
        WHERE 1=1";

if ($_SESSION['role'] !== 'admin') {
    $sql .= " AND r.fakultas_id = " . $_SESSION['fakultas_id'];
}

$sql .= " ORDER BY r.created_at DESC";
$risks = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Register</title>
    <link rel="stylesheet" href="../../../assets/css/risk-register.css">
</head>
<body>
    <div class="container my-5">
        <div class="header-section">
            <h1 class="title">Risk Register</h1>
            <a href="index.php?module=risk_register&action=create" class="btn-add">Add New Risk</a>
        </div>

        <div class="table-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Objective</th>
                        <th>Risk Event</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>PIC</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($risks)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($risks as $i => $risk): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
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
                                    <div class="action-buttons">
                                        <a href="index.php?module=risk_register&action=view&id=<?= $risk['id'] ?>" class="btn-view">View</a>
                                        <a href="index.php?module=risk_register&action=edit&id=<?= $risk['id'] ?>" class="btn-edit">Edit</a>
                                        <a href="index.php?module=risk_register&action=delete&id=<?= $risk['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>