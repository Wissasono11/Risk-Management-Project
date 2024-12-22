<?php
global $treatmentList;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">

<div class="treatments-container">
    <div class="treatments-header">
        <div class="header-left">
            <h1 class="header-title">Risk Treatments</h1>
            <p class="header-subtitle">Manage and monitor risk mitigation plans</p>
        </div>
        <div class="header-actions">
        </div>
    </div>

    <!-- Treatment Stats -->
    <div class="treatment-stats">
        <div class="stat-card">
            <div class="stat-value"><?= count($treatmentList) ?></div>
            <div class="stat-label">Total Treatments</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?= count(array_filter($treatmentList, function($t) {
                    return isset($t['status']) && $t['status'] === 'completed';
                })) ?>
            </div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?= count(array_filter($treatmentList, function($t) {
                    return isset($t['status']) && $t['status'] === 'ongoing';
                })) ?>
            </div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?= count(array_filter($treatmentList, function($t) {
                    return isset($t['status']) && $t['status'] === 'overdue';
                })) ?>
            </div>
            <div class="stat-label">Overdue</div>
        </div>
    </div>

    <!-- Treatment Table -->
    <div class="treatment-table">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Risk Event</th>
                        <th width="25%">Mitigation Plan</th>
                        <th width="15%">PIC</th>
                        <th width="15%">Timeline</th>
                        <th width="10%">Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($treatmentList)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-tasks fa-3x text-gray-300"></i>
                                <p class="mt-2 text-gray-500">No treatments found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($treatmentList as $i => $treatment): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="risk-info">
                                    <span class="risk-title"><?= htmlspecialchars($treatment['risk_event']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></td>
                            <td>
                                <div class="pic-info">
                                    <span class="pic-name"><?= htmlspecialchars($treatment['pic']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="timeline">
                                    <?php 
                                    if (!empty($treatment['timelines'])):
                                        foreach($treatment['timelines'] as $timeline):
                                            $status = $timeline['realisasi'] ? 'completed' : 'planned';
                                    ?>
                                        <div class="quarter">
                                            <div class="quarter-box <?= $status ?>">
                                                Q<?= $timeline['triwulan'] ?>
                                            </div>
                                        </div>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status = calculateTreatmentStatus($treatment);
                                $statusClass = strtolower($status);
                                ?>
                                <span class="status-badge status-<?= $statusClass ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-secondary btn-sm" 
                                            onclick="editTreatment(<?= $treatment['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="deleteTreatment(<?= $treatment['id'] ?>)">
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
</div>
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/treatments.js"></script>>

<?php
$content = ob_get_clean();
$pageTitle = "Treatments";
$module    = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
