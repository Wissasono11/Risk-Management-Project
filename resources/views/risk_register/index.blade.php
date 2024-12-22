<?php
global $riskRegisters;
ob_start();

$matrix = [];

foreach ($riskRegisters as $risk) {
    $likelihood = (int) $risk['likelihood_inherent'];
    $impact = (int) $risk['impact_inherent'];
    
    if (!isset($matrix[$likelihood])) {
        $matrix[$likelihood] = [];
    }
    if (!isset($matrix[$likelihood][$impact])) {
        $matrix[$likelihood][$impact] = [];
    }
    
    $matrix[$likelihood][$impact][] = $risk;
}


?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/risk-register.css">

<div class="risk-container container mt-5">
    <div class="risk-header d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="header-title">Risk Register</h1>
            <p class="header-subtitle">Manage and monitor your risk assessments</p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/risk-register/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Risk
            </a>
        </div>
    </div>

    <!-- Risk Statistics -->
    <div class="risk-stats">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--status-low)">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="stat-value"><?= count($riskRegisters) ?></div>
            <div class="stat-label">Total Risks</div>
        </div>
        
        <?php
        $highRisks = array_filter($riskRegisters, function($risk) {
            $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
            return in_array($level['label'], ['High', 'Very High']);
        });
        ?>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--status-high)">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-value"><?= count($highRisks) ?></div>
            <div class="stat-label">High Risks</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--status-medium)">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
            <div class="stat-value"><?= array_sum(array_column($riskRegisters, 'treatments_count') ?? []) ?></div>
            <div class="stat-label">Active Treatments</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: rgba(124, 58, 237, 0.1); color: var(--status-very-high)">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?= array_sum(array_column($riskRegisters, 'completed_treatments') ?? []) ?></div>
            <div class="stat-label">Completed Treatments</div>
        </div>
    </div>

    <!-- Risk Matrix -->
    <div class="matrix-container">
        <h3 class="matrix-title">Risk Distribution Matrix</h3>
        <div class="risk-matrix">
        </div>
    </div>

    <!-- Risk Table -->
    <div class="risk-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Risk Event</th>
                    <th>Category</th>
                    <th>Risk Owner</th>
                    <th>Level</th>
                    <th>Treatments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($riskRegisters)): ?>
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list fa-3x"></i>
                            <p>No risks have been added yet</p>
                            <a href="<?= $_SESSION['base_uri'] ?>/risk-register/create" class="btn btn-primary">
                                Add Your First Risk
                            </a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($riskRegisters as $risk): 
                    $level = calculateRiskLevel(
                        $risk['likelihood_inherent'] ?? 0,
                        $risk['impact_inherent'] ?? 0
                    );
                ?>
                <tr>
                    <td>
                        <div class="risk-info">
                            <div class="risk-event"><?= htmlspecialchars($risk['risk_event']) ?></div>
                            <div class="risk-objective"><?= htmlspecialchars($risk['objective']) ?></div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($risk['kategori_nama'] ?? '') ?></td>
                    <td>
                        <div class="owner-info">
                            <span class="owner-name"><?= htmlspecialchars($risk['risk_owner']) ?></span>
                            <span class="owner-dept"><?= htmlspecialchars($risk['fakultas_nama'] ?? '') ?></span>
                        </div>
                    </td>
                    <td style="text-align: center";>
                        <span class="status-badge status-<?= strtolower($level['level']) ?>">
                            <?= $level['label'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="progress-info">
                            <div class="progress">
                                <?php
                                $progress = isset($risk['treatments_count']) && $risk['treatments_count'] > 0
                                    ? ($risk['completed_treatments'] / $risk['treatments_count']) * 100
                                    : 0;
                                ?>
                                <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                            </div>
                            <span class="progress-text">
                                <?= $risk['completed_treatments'] ?? 0 ?>/<?= $risk['treatments_count'] ?? 0 ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="<?= $_SESSION['base_uri'] ?>/risk-register/view?id=<?= $risk['id'] ?>" 
                               class="btn-icon btn-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= $_SESSION['base_uri'] ?>/risk-register/edit?id=<?= $risk['id'] ?>" 
                               class="btn-icon btn-edit" title="Edit Risk">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn-icon btn-delete" data-id="<?= $risk['id'] ?>" title="Delete Risk">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
window.matrixData = <?= json_encode($matrix ?? []) ?>;
window.baseUrl = '<?= $_SESSION['base_uri'] ?>';
</script>

<!-- Include the RiskManager JavaScript file -->
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/risk-register.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "Risk Register";
$module = "risk_register";
require __DIR__ . '/../../layouts/app.blade.php';
?>