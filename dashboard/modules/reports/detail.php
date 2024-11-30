<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

$fakultas_id = (int)$_GET['fakultas_id'];
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Get fakultas info
$sql = "SELECT * FROM fakultas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $fakultas_id);
$stmt->execute();
$fakultas = $stmt->get_result()->fetch_assoc();

if (!$fakultas) {
    setAlert('error', 'Fakultas tidak ditemukan');
    header('Location: index.php');
    exit();
}

// Get detailed risk data
$sql = "SELECT 
            r.*,
            rc.nama as kategori_nama,
            COUNT(t.id) as total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
        FROM risk_registers r
        LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE r.fakultas_id = ?
        GROUP BY r.id
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tahun, $fakultas_id);
$stmt->execute();
$risks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate summary
$total_risks = count($risks);
$high_risks = 0;
$total_treatments = 0;
$completed_treatments = 0;

foreach($risks as $risk) {
    if (in_array($risk['risk_level_inherent'], ['T', 'ST'])) {
        $high_risks++;
    }
    $total_treatments += $risk['total_treatments'];
    $completed_treatments += $risk['completed_treatments'];
}
?>

<div class="main-content">
    <div class="header">
        <h1>Risk Detail Report - <?= htmlspecialchars($fakultas['nama']) ?></h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Back</a>
            <a href="export.php?fakultas_id=<?= $fakultas_id ?>&tahun=<?= $tahun ?>" class="btn btn-success">Export</a>
        </div>
    </div>

    <div class="content">
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card bg-primary text-white">
                    <div class="stats-info">
                        <h4>Total Risks</h4>
                        <h3><?= $total_risks ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-danger text-white">
                    <div class="stats-info">
                        <h4>High Risks</h4>
                        <h3><?= $high_risks ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-info text-white">
                    <div class="stats-info">
                        <h4>Mitigation Progress</h4>
                        <h3><?= $total_treatments ? round(($completed_treatments / $total_treatments) * 100) : 0 ?>%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Details -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Risk Event</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Owner</th>
                                <th>Treatment Progress</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($risks as $risk): ?>
                                <tr>
                                    <td><?= htmlspecialchars($risk['risk_event']) ?></td>
                                    <td><?= htmlspecialchars($risk['kategori_nama']) ?></td>
                                    <td>
                                        <?php 
                                        $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
                                        ?>
                                        <span class="badge badge-<?= $level['color'] ?>"><?= $level['label'] ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
                                    <td>
                                        <?php
                                        $progress = $risk['total_treatments'] ? 
                                            round(($risk['completed_treatments'] / $risk['total_treatments']) * 100) : 0;
                                        ?>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $progress ?>%">
                                                <?= $progress ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="../risk_register/view.php?id=<?= $risk['id'] ?>" 
                                           class="btn btn-info btn-sm">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>