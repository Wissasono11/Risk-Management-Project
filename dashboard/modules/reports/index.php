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
$sql = "
    SELECT 
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table-hover tbody tr:hover {
      background-color: #f5f5f5;
    }
  </style>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Risk Management Reports</h1>
      <a href="export.php" class="btn btn-success">Export Excel</a>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-4">
            <label for="tahun" class="form-label">Year</label>
            <select name="tahun" id="tahun" class="form-select">
              <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>>
                  <?= $y ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="fakultas_id" class="form-label">Faculty</label>
            <select name="fakultas_id" id="fakultas_id" class="form-select">
              <option value="">All Faculties</option>
              <?php foreach ($fakultas_list as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $f['id'] == $fakultas_id ? 'selected' : '' ?>>
                  <?= htmlspecialchars($f['nama']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
        </form>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary">
          <div class="card-body">
            <h5 class="card-title">Total Risks</h5>
            <p class="card-text display-6"><?= array_sum(array_column($summary, 'total_risks')) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-danger">
          <div class="card-body">
            <h5 class="card-title">High Risks</h5>
            <p class="card-text display-6"><?= array_sum(array_column($summary, 'high_risks')) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-info">
          <div class="card-body">
            <h5 class="card-title">Treatments</h5>
            <p class="card-text display-6"><?= array_sum(array_column($summary, 'total_treatments')) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success">
          <div class="card-body">
            <h5 class="card-title">Completed</h5>
            <p class="card-text display-6"><?= array_sum(array_column($summary, 'completed_treatments')) ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Risk Report by Faculty</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
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
                    <?php 
                    $progress = $row['total_treatments'] 
                        ? round(($row['completed_treatments'] / $row['total_treatments']) * 100) 
                        : 0; 
                    ?>
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" 
                           style="width: <?= $progress ?>%;" 
                           aria-valuenow="<?= $progress ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                        <?= $progress ?>%
                      </div>
                    </div>
                  </td>
                  <td>
                    <a href="detail.php?fakultas_id=<?= $row['fakultas_id'] ?>&tahun=<?= $tahun ?>" 
                       class="btn btn-info btn-sm">Detail</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($summary)): ?>
                <tr>
                  <td colspan="7" class="text-center">No data available</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>