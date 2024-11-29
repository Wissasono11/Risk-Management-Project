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

<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Risk Treatments</h1>
    </div>

    <div class="content">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Risk Event</th>
                    <th>Rencana Mitigasi</th>
                    <th>PIC</th>
                    <th>Evidence</th>
                    <th>Status</th>
                    <th>Action</th>
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
                            echo "<span class='badge badge-{$status['color']}'>{$status['label']}</span>";
                            ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $treatment['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $treatment['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>