<?php
global $treatmentList;
ob_start();
require_once __DIR__ . '/../../../app/Helpers/calculateTreatmentStatus.php'; 
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">
<div class="treatments-container container mt-5">
    <div class="treatments-header d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="header-title">Risk Treatments</h1>
            <p class="header-subtitle">Manage and monitor risk mitigation plans</p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/treatments/create?risk_id=<?= htmlspecialchars($riskId) ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Treatment
            </a>
        </div>
    </div>

    <!-- Treatment Stats -->
    <div class="treatment-stats row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title"><?= count($treatmentList) ?></h5>
                    <p class="card-text">Total Treatments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= count(array_filter($treatmentList, function($t) {
                            return isset($t['status']) && $t['status'] === 'completed';
                        })) ?>
                    </h5>
                    <p class="card-text">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= count(array_filter($treatmentList, function($t) {
                            return isset($t['status']) && $t['status'] === 'ongoing';
                        })) ?>
                    </h5>
                    <p class="card-text">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= count(array_filter($treatmentList, function($t) {
                            return isset($t['status']) && $t['status'] === 'overdue';
                        })) ?>
                    </h5>
                    <p class="card-text">Overdue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatment Table -->
    <div class="treatment-table">
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Risk Event</th>
                        <th>Mitigation Plan</th>
                        <th>PIC</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                            <td><?= htmlspecialchars($treatment['risk_event']) ?></td>
                            <td><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></td>
                            <td><?= htmlspecialchars($treatment['pic']) ?></td>
                            <td>
                                <?php 
                                if (!empty($treatment['timelines'])):
                                    foreach($treatment['timelines'] as $timeline):
                                        $status = $timeline['realisasi'] ? 'Completed' : 'Planned';
                                ?>
                                    <span class="badge <?= $timeline['realisasi'] ? 'bg-success' : 'bg-secondary' ?>">
                                        Q<?= $timeline['triwulan'] ?> - <?= $timeline['tahun'] ?> (<?= $status ?>)
                                    </span>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </td>
                            <td>
                                <?php
                                $status = calculateTreatmentStatus($treatment);
                                $statusClass = strtolower($status);
                                ?>
                                <span class="badge bg-<?= 
                                    $statusClass === 'completed' ? 'success' : 
                                    ($statusClass === 'ongoing' ? 'warning text-dark' : 
                                    ($statusClass === 'overdue' ? 'danger' : 'secondary')) ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= $_SESSION['base_uri'] ?>/treatments/edit?id=<?= $treatment['id'] ?>" class="btn btn-secondary btn-sm me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= $_SESSION['base_uri'] ?>/treatments/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this treatment?');">
                                    <input type="hidden" name="id" value="<?= $treatment['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Notification (Optional) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <!-- Message akan diisi oleh JavaScript (Optional) -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    // Define baseUrl globally for JavaScript (if needed)
    window.baseUrl = '<?= $_SESSION['base_uri'] ?>';
</script>
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/treatments.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "Treatments";
$module    = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
?>
