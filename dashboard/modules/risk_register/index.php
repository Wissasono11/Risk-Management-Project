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
<link rel="stylesheet" href="../../../assets/css/risk-register.css">

<div class="content">
    <div class="header-section">
        <h2>Risk Register</h2>
        <button class="btn-add" onclick="window.location='index.php?module=risk_register&action=create'">
            <span class="las la-plus"></span>
            Add New Risk
        </button>
    </div>

    <div class="table-section">
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Objective</th>
                            <th>Risk Event</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>PIC</th>
                            <th>Actions</th>
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
                                        <span class="status <?= strtolower($level['level']) ?>">
                                            <?= $level['label'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-view">
                                            <span class="las la-eye"></span>
                                        </button>
                                        <button class="btn-edit">
                                            <span class="las la-pen"></span>
                                        </button>
                                        <button class="btn-delete">
                                            <span class="las la-trash"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
