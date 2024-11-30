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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Treatments</title>
    <link rel="stylesheet" href="../../../assets/css/treatments.css">
</head>
<body>
    <div class="container my-5">
        <div class="header-section">
            <h1 class="title">Risk Treatments</h1>
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
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $treatment['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $treatment['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>