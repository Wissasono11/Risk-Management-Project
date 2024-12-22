<?php
global $riskDetail;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/risk-register.css">

<div class="risk-container">
    <!-- Enhanced Header -->
    <div class="risk-header">
        <div class="header-left">
            <div class="header-title-group">
                <h1 class="header-title">Risk Detail</h1>
                <p class="header-subtitle">Viewing detailed risk information</p>
            </div>
            <div class="risk-level-indicator">
                <?php 
                $level = calculateRiskLevel(
                    $riskDetail['likelihood_inherent'] ?? 0, 
                    $riskDetail['impact_inherent'] ?? 0
                );
                ?>
                <span class="status-badge status-<?= strtolower($level['level']) ?>">
                    <i class="fas fa-shield-alt"></i>
                    <?= $level['label'] ?> Risk Level
                </span>
            </div>
        </div>
        <div class="header-actions">
            <button onclick="window.history.back()" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back
            </button>
            <div class="btn-group">
                <a href="<?= $_SESSION['base_uri'] ?>/risk-register/edit?id=<?= $riskDetail['id'] ?>" 
                   class="btn btn-outline">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="<?= $_SESSION['base_uri'] ?>/treatments/create?risk_id=<?= $riskDetail['id'] ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Treatment
                </a>
            </div>
        </div>
    </div>

<!-- Risk Information -->
<div class="info-card">
    <div class="card-header">
        <h3>
            <i class="fas fa-info-circle"></i>
            Risk Information
        </h3>
    </div>
    <div class="card-body">
        <!-- Main Info Grid -->
        <div class="info-sections">
            <!-- Left Section -->
            <div class="info-section">
                <h4 class="section-title">Basic Information</h4>
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-building text-gray-500"></i>
                            Faculty
                        </div>
                        <div class="info-value"><?= htmlspecialchars($riskDetail['fakultas_nama'] ?? '-') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-bullseye text-gray-500"></i>
                            Objective
                        </div>
                        <div class="info-value highlighted-text"><?= htmlspecialchars($riskDetail['objective'] ?? '') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-cogs text-gray-500"></i>
                            Business Process
                        </div>
                        <div class="info-value"><?= htmlspecialchars($riskDetail['proses_bisnis'] ?? '') ?></div>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="info-section">
                <h4 class="section-title">Risk Details</h4>
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-exclamation-triangle text-gray-500"></i>
                            Risk Event
                        </div>
                        <div class="info-value highlighted-text"><?= htmlspecialchars($riskDetail['risk_event'] ?? '') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-search text-gray-500"></i>
                            Risk Cause
                        </div>
                        <div class="info-value"><?= htmlspecialchars($riskDetail['risk_cause'] ?? '') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-vector-square text-gray-500"></i>
                            Risk Source
                        </div>
                        <div class="info-value">
                            <span class="tag tag-<?= strtolower($riskDetail['risk_source'] ?? '') ?>">
                                <?= ucfirst(htmlspecialchars($riskDetail['risk_source'] ?? '')) ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user text-gray-500"></i>
                            Risk Owner
                        </div>
                        <div class="info-value"><?= htmlspecialchars($riskDetail['risk_owner'] ?? '') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Assessment Box -->
        <div class="risk-assessment-box">
            <div class="assessment-header">
                <i class="fas fa-chart-line"></i>
                Risk Assessment
            </div>
            <div class="assessment-content">
                <div class="assessment-metrics">
                    <div class="metric-box">
                        <div class="metric-label">Likelihood</div>
                        <div class="metric-value"><?= $riskDetail['likelihood_inherent'] ?></div>
                    </div>
                    <div class="metric-separator">×</div>
                    <div class="metric-box">
                        <div class="metric-label">Impact</div>
                        <div class="metric-value"><?= $riskDetail['impact_inherent'] ?></div>
                    </div>
                    <div class="metric-separator">=</div>
                    <div class="metric-box result">
                        <div class="metric-label">Risk Score</div>
                        <div class="metric-value">
                            <?= $riskDetail['likelihood_inherent'] * $riskDetail['impact_inherent'] ?>
                        </div>
                    </div>
                </div>
                <div class="assessment-level">
                    <?php 
                    $level = calculateRiskLevel(
                        $riskDetail['likelihood_inherent'] ?? 0,
                        $riskDetail['impact_inherent'] ?? 0
                    );
                    ?>
                    <div class="level-label">Risk Level:</div>
                    <div class="level-badge level-<?= strtolower($level['level']) ?>">
                        <i class="fas fa-shield-alt"></i>
                        <?= $level['label'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Treatments Section (if needed) -->
    <?php if (!empty($riskDetail['treatments'])): ?>
    <div class="info-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-tasks"></i>
                Treatment Plan
            </h3>
            <a href="<?= $_SESSION['base_uri'] ?>/treatments/create?risk_id=<?= $riskDetail['id'] ?>" 
               class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i>
                Add Treatment
            </a>
        </div>
        <div class="card-body">
            <div class="treatment-timeline">
                <?php foreach($riskDetail['treatments'] as $treatment): ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h4 class="treatment-title"><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></h4>
                        <div class="treatment-meta">
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($treatment['pic']) ?>
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-file-alt"></i>
                                <?= htmlspecialchars($treatment['evidence_type']) ?>
                            </span>
                        </div>
                        <div class="treatment-actions">
                            <a href="<?= $_SESSION['base_uri'] ?>/treatments/edit?id=<?= $treatment['id'] ?>" 
                               class="btn btn-sm btn-outline">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<?php
$content = ob_get_clean();
$pageTitle = "Risk Detail";
$module = "risk_register";
require __DIR__ . '/../../layouts/app.blade.php';
?>