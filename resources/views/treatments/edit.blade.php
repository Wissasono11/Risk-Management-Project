<?php
global $treatmentEdit;
ob_start();
require_once __DIR__ . '/../../../app/Helpers/calculateTreatmentStatus.php'; 
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">

<div class="treatments-container container mt-5">
    <div class="treatments-header d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="header-title">Edit Treatment</h1>
            <p class="header-subtitle">For Risk: <?= htmlspecialchars($treatmentEdit['risk_event']) ?></p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Treatments
            </a>
        </div>
    </div>

    <!-- Form Edit Treatment -->
    <div class="form-section">
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/treatments/update">
            <input type="hidden" name="id" value="<?= htmlspecialchars($treatmentEdit['id']) ?>">

            <div class="form-group">
                <label for="rencana_mitigasi" class="form-label">Mitigation Plan *</label>
                <textarea name="rencana_mitigasi" class="form-control" id="rencana_mitigasi" rows="3" required><?= htmlspecialchars($treatmentEdit['rencana_mitigasi']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="pic" class="form-label">PIC *</label>
                <input type="text" name="pic" class="form-control" id="pic" required value="<?= htmlspecialchars($treatmentEdit['pic']) ?>">
            </div>

            <div class="form-group">
                <label for="evidence_type" class="form-label">Evidence Type *</label>
                <input type="text" name="evidence_type" class="form-control" id="evidence_type" required value="<?= htmlspecialchars($treatmentEdit['evidence_type']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Timeline (<?= date('Y') ?>)</label>
                <div class="timeline-checkboxes d-flex justify-content-between">
                    <?php for($i=1; $i<=4; $i++): 
                        $isChecked = isset($treatmentEdit['timelines'][$i]) && $treatmentEdit['timelines'][$i]['realisasi'] ? 'checked' : '';
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="q<?= $i ?>" name="timeline[<?= $i ?>]" <?= $isChecked ?>>
                            <label class="form-check-label" for="q<?= $i ?>">
                                Q<?= $i ?>
                            </label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">Update Treatment</button>
                <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Define baseUrl globally if needed
    window.baseUrl = '<?= $_SESSION['base_uri'] ?>';
</script>
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/treatments.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "Edit Treatment";
$module    = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
?>
