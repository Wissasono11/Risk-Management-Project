<?php
global $detailRisks, $detailFakultasId, $detailYear;
global $detailTotalRisks, $detailHighRisks, $detailTotalTreatments, $detailCompleted;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/reports.css">

<div class="reports-container">
    <div class="reports-header">
        <div class="header-titles">
            <a href="<?= $_SESSION['base_uri'] ?>/reports" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <h1 class="page-title">Risk Detail Report - <?= $detailYear ?></h1>
        </div>
        <a href="<?= $_SESSION['base_uri'] ?>/reports/export?fakultas_id=<?= $detailFakultasId ?>&tahun=<?= $detailYear ?>" 
           class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Detail
        </a>
    </div>

    <!-- Detail Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--color-low)">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="stat-title">Total Risks</div>
            <div class="stat-value"><?= $detailTotalRisks ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--color-high)">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-title">High Risks</div>
            <div class="stat-value"><?= $detailHighRisks ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(124, 58, 237, 0.1); color: var(--color-extreme)">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-title">Progress</div>
            <div class="stat-value">
                <?php 
                $progress = $detailTotalTreatments 
                    ? round(($detailCompleted / $detailTotalTreatments)*100)
                    : 0;
                echo $progress . '%';
                ?>
            </div>
        </div>
    </div>

    <!-- Risks Table -->
    <div class="risk-table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Risk Event</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Owner</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($detailRisks as $risk): ?>
                <?php 
                $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
                $progress2 = $risk['total_treatments']
                    ? round(($risk['completed_treatments']/$risk['total_treatments'])*100)
                    : 0;
                ?>
                <tr>
                    <td>
                        <div class="risk-info">
                            <span class="risk-title"><?= htmlspecialchars($risk['risk_event']) ?></span>
                            <small class="risk-desc"><?= htmlspecialchars($risk['objective']) ?></small>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($risk['kategori_nama']) ?></td>
                    <td>
                        <span class="risk-badge <?= strtolower($level['label']) ?>">
                            <?= $level['label'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $progress2 ?>%">
                                <span class="progress-value"><?= $progress2 ?>%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="<?= $_SESSION['base_uri'] ?>/risk-register/view?id=<?= $risk['id'] ?>" 
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
$pageTitle = "Report Detail";
$module    = "reports";
require __DIR__ . '/../../layouts/app.blade.php';
