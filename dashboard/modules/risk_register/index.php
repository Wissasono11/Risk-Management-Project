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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .table-hover tbody tr:hover {
    background-color: #f5f5f5;
  }
  .badge {
    font-size: 0.8rem;
    font-weight: 500;
  }
</style>
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Risk Register</h1>
    <a href="index.php?module=risk_register&action=create" class="btn btn-primary">Add New Risk</a>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
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
                <span class="badge rounded-pill bg-<?= $level['color'] ?>">
                  <?= $level['label'] ?>
                </span>
              </td>
              <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="index.php?module=risk_register&action=view&id=<?= $risk['id'] ?>" 
                     class="btn btn-info btn-sm">View</a>
                  <a href="index.php?module=risk_register&action=edit&id=<?= $risk['id'] ?>" 
                     class="btn btn-warning btn-sm">Edit</a>
                  <a href="index.php?module=risk_register&action=delete&id=<?= $risk['id'] ?>" 
                     class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
