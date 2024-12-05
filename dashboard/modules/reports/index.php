<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Filter parameters
$tahun = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$fakultas_id = isset($_GET['fakultas_id']) && is_numeric($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : null;

// Get data fakultas untuk filter
$sql = "SELECT id, nama FROM fakultas ORDER BY nama";
$fakultas_list = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Query untuk mengambil data summary
$sql = "SELECT 
    f.id as fakultas_id,
    f.nama as fakultas_nama,
    COUNT(r.id) as total_risks,
    SUM(CASE WHEN r.risk_level_inherent IN ('T', 'ST') THEN 1 ELSE 0 END) as high_risks,
    COUNT(t.id) as total_treatments,
    SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
FROM fakultas f
LEFT JOIN risk_registers r ON f.id = r.fakultas_id
LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
WHERE 1 = 1";

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
<link rel="stylesheet" href="../../../assets/css/reports.css">

<div class="content">
    <div class="header-section">
        <h2>Risk Management Reports</h2>
        <a href="index.php?module=reports&action=export" class="btn-add">Export Excel</a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <input type="hidden" name="module" value="reports">
            <div class="form-group">
                <label>Year</label>
                <select name="tahun" class="form-control">
                    <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Faculty</label>
                <select name="fakultas_id" class="form-control">
                    <option value="">All Faculties</option>
                    <?php foreach ($fakultas_list as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= $f['id'] == $fakultas_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </form>
    </div>

    <div class="cards">
        <div class="card-single">
            <div>
                <h3>Total Risks</h3>
                <span><?= array_sum(array_column($summary, 'total_risks')) ?></span>
            </div>
            <div>
                <span class="las la-clipboard-list"></span>
            </div>
        </div>
        <div class="card-single">
            <div>
                <h3>High Risks</h3>
                <span><?= array_sum(array_column($summary, 'high_risks')) ?></span>
            </div>
            <div>
                <span class="las la-exclamation-triangle"></span>
            </div>
        </div>
        <div class="card-single">
            <div>
                <h3>Treatments</h3>
                <span><?= array_sum(array_column($summary, 'total_treatments')) ?></span>
            </div>
            <div>
                <span class="las la-tasks"></span>
            </div>
        </div>
        <div class="card-single">
            <div>
                <h3>Completed</h3>
                <span><?= array_sum(array_column($summary, 'completed_treatments')) ?></span>
            </div>
            <div>
                <span class="las la-check-circle"></span>
            </div>
        </div>
    </div>

    <div class="table-section">
        <table class="table">
            <thead>
                <tr>
                    <th>Faculty</th>
                    <th>Total Risks</th>
                    <th>High Risks</th>
                    <th>Treatments</th>
                    <th>Completed</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fakultas_nama']) ?></td>
                        <td><?= $row['total_risks'] ?></td>
                        <td><?= $row['high_risks'] ?></td>
                        <td><?= $row['total_treatments'] ?></td>
                        <td><?= $row['completed_treatments'] ?></td>
                        <td>
                            <div class="progress">
                                <?php 
                                $progress = $row['total_treatments'] 
                                    ? round(($row['completed_treatments'] / $row['total_treatments']) * 100) 
                                    : 0; 
                                ?>
                                <div class="progress-bar" style="width: <?= $progress ?>%">
                                    <?= $progress ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="index.php?module=reports&action=detail&fakultas_id=<?= $row['fakultas_id'] ?>&tahun=<?= $tahun ?>" 
                               class="btn-view">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
