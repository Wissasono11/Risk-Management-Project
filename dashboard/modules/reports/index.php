<?php
require_once '../../config/database.php';
require_once '../../middleware/auth.php';
require_once '../../includes/functions.php';
checkAuth();

// Filter parameters
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$fakultas_id = isset($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : null;

// Get data fakultas untuk filter
$sql = "SELECT * FROM fakultas ORDER BY nama";
$fakultas_list = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Get summary data
$sql = "SELECT 
            f.nama as fakultas_nama,
            COUNT(r.id) as total_risks,
            SUM(CASE WHEN r.risk_level_inherent IN ('T', 'ST') THEN 1 ELSE 0 END) as high_risks,
            COUNT(t.id) as total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
        FROM fakultas f
        LEFT JOIN risk_registers r ON f.id = r.fakultas_id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE 1=1";

if ($fakultas_id) {
    $sql .= " AND f.id = ?";
}

$sql .= " GROUP BY f.id, f.nama";

$stmt = $conn->prepare($sql);
if ($fakultas_id) {
    $stmt->bind_param("ii", $tahun, $fakultas_id);
} else {
    $stmt->bind_param("i", $tahun);
}

$stmt->execute();
$summary = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Risk Management Reports</h1>
        <div>
            <a href="export.php" class="btn btn-success">Export Excel</a>
        </div>
    </div>

    <div class="content">
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            <?php for($y = 2020; $y <= date('Y'); $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Fakultas</label>
                        <select name="fakultas_id" class="form-control">
                            <option value="">Semua Fakultas</option>
                            <?php foreach($fakultas_list as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= $f['id'] == $fakultas_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card bg-primary text-white">
                    <div class="stats-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-info">
                        <h4>Total Risks</h4>
                        <h3><?= array_sum(array_column($summary, 'total_risks')) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-danger text-white">
                    <div class="stats-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stats-info">
                        <h4>High Risks</h4>
                        <h3><?= array_sum(array_column($summary, 'high_risks')) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-info text-white">
                    <div class="stats-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stats-info">
                        <h4>Treatments</h4>
                        <h3><?= array_sum(array_column($summary, 'total_treatments')) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-success text-white">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-info">
                        <h4>Completed</h4>
                        <h3><?= array_sum(array_column($summary, 'completed_treatments')) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Report -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Risk Report by Faculty</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fakultas</th>
                                <th>Total Risks</th>
                                <th>High Risks</th>
                                <th>Treatments</th>
                                <th>Completed</th>
                                <th>Progress</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($summary as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['fakultas_nama']) ?></td>
                                    <td><?= $row['total_risks'] ?></td>
                                    <td><?= $row['high_risks'] ?></td>
                                    <td><?= $row['total_treatments'] ?></td>
                                    <td><?= $row['completed_treatments'] ?></td>
                                    <td>
                                        <?php
                                        $progress = $row['total_treatments'] ? 
                                            round(($row['completed_treatments'] / $row['total_treatments']) * 100) : 0;
                                        ?>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $progress ?>%">
                                                <?= $progress ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="detail.php?fakultas_id=<?= $row['id'] ?>&tahun=<?= $tahun ?>" 
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

<?php include '../../includes/footer.php'; ?>