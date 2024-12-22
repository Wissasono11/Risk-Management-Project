<?php
global $reportsFaculties, $reportsSummary, $filterYear, $filterFaculty;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/reports.css">

<div class="reports-container">
    <div class="reports-header">
        <h1 class="page-title">Risk Management Reports</h1>
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

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--color-low)">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="stat-title">Total Risks</div>
            <div class="stat-value"><?= array_sum(array_column($reportsSummary,'total_risks')) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--color-high)">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-title">High Risks</div>
            <div class="stat-value"><?= array_sum(array_column($reportsSummary,'high_risks')) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(234, 179, 8, 0.1); color: var(--color-medium)">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
            <div class="stat-title">Treatments</div>
            <div class="stat-value"><?= array_sum(array_column($reportsSummary,'total_treatments')) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(124, 58, 237, 0.1); color: var(--color-extreme)">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-title">Completed</div>
            <div class="stat-value"><?= array_sum(array_column($reportsSummary,'completed_treatments')) ?></div>
        </div>
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
                    <td><?= $row['total_risks'] ?></td>
                    <td>
                        <?php if ($row['high_risks'] > 0): ?>
                            <span class="risk-badge high"><?= $row['high_risks'] ?></span>
                        <?php else: ?>
                            <span class="risk-badge low">0</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['total_treatments'] ?></td>
                    <td><?= $row['completed_treatments'] ?></td>
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
