<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Get all treatments
$treatments = [];
$sql = "SELECT t.*, r.risk_event, r.fakultas_id
        FROM risk_treatments t
        JOIN risk_registers r ON t.risk_register_id = r.id";

if ($_SESSION['role'] !== 'admin') {
    $sql .= " WHERE r.fakultas_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['fakultas_id']);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$treatments = $result->fetch_all(MYSQLI_ASSOC);
?>
<link rel="stylesheet" href="../../../assets/css/treatments.css">

<div class="content">
    <div class="header-section">
        <h2>Risk Treatments</h2>
    </div>

    <div class="table-section">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Risk Event</th>
                    <th>Mitigation Plan</th>
                    <th>PIC</th>
                    <th>Evidence</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($treatments as $i => $treatment): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($treatment['risk_event']) ?></td>
                        <td><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></td>
                        <td><?= htmlspecialchars($treatment['pic']) ?></td>
                        <td><?= htmlspecialchars($treatment['evidence_type']) ?></td>
                        <td>
                            <?php
                            $status = getRealisasiStatus($treatment);
                            echo "<span class='badge {$status['color']}'>{$status['label']}</span>";
                            ?>
                        </td>
                        <td class="action-buttons">
                            <button class="btn-edit" onclick="window.location='index.php?module=treatments&action=edit&id=<?= $treatment['id'] ?>'">
                                Edit
                            </button>
                            <button class="btn-delete" onclick="confirmDelete(<?= $treatment['id'] ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
