<?php
global $riskId; // from controller
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">

<div class="treatments-container">
    <div class="treatments-header">
        <div class="header-left">
            <h2 class="header-title">Add Treatment</h2>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Treatments
            </a>
        </div>
    </div>

    <div class="form-section">
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/treatments/store">
            <input type="hidden" name="risk_id" value="<?= $riskId ?>">

            <div class="form-group">
                <label>Mitigation Plan *</label>
                <textarea name="rencana_mitigasi" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>PIC *</label>
                <input type="text" name="pic" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Evidence Type *</label>
                <input type="text" name="evidence_type" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Timeline (<?= date('Y') ?>)</label>
                <div class="timeline-checkboxes">
                    <?php for($i=1; $i<=4; $i++): ?>
                        <div class="quarter-checkbox">
                            <input type="checkbox" name="timeline[<?= $i ?>]" id="q<?= $i ?>" value="1">
                            <label for="q<?= $i ?>">Q<?= $i ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Treatment</button>
                <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "Create Treatment";
$module    = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
