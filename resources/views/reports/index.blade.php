<?php
global $reportsFaculties, $reportsSummary, $filterYear, $filterFaculty;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/reports.css">

<div class="reports-container">
    <div class="reports-header">
    <div class="header-left">
        <h1 class="page-title">Risk Management Reports</h1>
        <p class="header-subtitle">View your Detailed Reports</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="<?= $_SESSION['base_uri'] ?>/reports/export" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" class="filter-form" action="<?= $_SESSION['base_uri'] ?>/reports">
            <div class="form-group">
                <label>Year</label>
                <select name="tahun" class="form-control">
                    <?php for($y=2020; $y<=date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= ($y==$filterYear?'selected':'') ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Faculty</label>
                <select name="fakultas_id" class="form-control">
                    <option value="">All Faculties</option>
                    <?php foreach($reportsFaculties as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ($f['id']==$filterFaculty?'selected':'') ?>>
                            <?= htmlspecialchars($f['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Apply Filter
            </button>
        </form>
    </div>



    <!-- Risk Table -->
    <div class="risk-table-container">
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
            <?php foreach($reportsSummary as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fakultas_nama']) ?></td>
                    <td style="text-align: center";><?= $row['total_risks'] ?></td>
                    <td style="text-align: center";>
                        <?php if ($row['high_risks'] > 0): ?>
                            <span class="risk-badge high"><?= $row['high_risks'] ?></span>
                        <?php else: ?>
                            <span class="risk-badge low">0</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center";><?= $row['total_treatments'] ?></td>
                    <td style="text-align: center";><?= $row['completed_treatments'] ?></td>
                    <td>
                        <div class="progress">
                            <?php 
                            $progress = $row['total_treatments']
                                ? round(($row['completed_treatments']/$row['total_treatments'])*100)
                                : 0;
                            ?>
                            <div class="progress-bar" style="width: <?= $progress ?>%">
                                <span class="progress-value"><?= $progress ?>%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="<?= $_SESSION['base_uri'] ?>/reports/detail?fakultas_id=<?= $row['fakultas_id'] ?>&tahun=<?= $filterYear ?>" 
                           class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "Reports";
$module    = "reports";
require __DIR__ . '/../../layouts/app.blade.php';
