<?php
global $treatmentEdit;
ob_start();
?>
<div class="treatments-container">
    <div class="treatments-header">
        <div class="header-left">
            <h2 class="header-title">Edit Treatment</h2>
            <p class="risk-event">For Risk: <?= htmlspecialchars($treatmentEdit['risk_event']) ?></p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Treatments
            </a>
        </div>
    </div>

    <div class="form-section">
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/treatments/update">
            <input type="hidden" name="id" value="<?= $treatmentEdit['id'] ?>">

            <div class="form-group">
                <label>Mitigation Plan *</label>
                <textarea name="rencana_mitigasi" class="form-control" rows="3" required>
                    <?= htmlspecialchars($treatmentEdit['rencana_mitigasi']) ?>
                </textarea>
            </div>

            <div class="form-group">
                <label>PIC *</label>
                <input type="text" name="pic" class="form-control" required
                       value="<?= htmlspecialchars($treatmentEdit['pic']) ?>">
            </div>

            <div class="form-group">
                <label>Evidence Type *</label>
                <input type="text" name="evidence_type" class="form-control" required
                       value="<?= htmlspecialchars($treatmentEdit['evidence_type']) ?>">
            </div>

            <div class="form-group">
                <label>Timeline & Realization</label>
                <div class="timeline-grid">
                    <?php if (!empty($treatmentEdit['timelines'])): ?>
                        <?php foreach($treatmentEdit['timelines'] as $tl): ?>
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    Q<?= $tl['triwulan'] ?> - <?= $tl['tahun'] ?>
                                </div>
                                <div class="timeline-status">
                                    <input type="checkbox" 
                                           name="timeline[<?= $tl['id'] ?>][realisasi]" 
                                           id="tl<?= $tl['id'] ?>"
                                           value="1" 
                                           <?= ($tl['realisasi'] ? 'checked' : '') ?>>
                                    <label for="tl<?= $tl['id'] ?>">Realized</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Treatment</button>
                <a href="<?= $_SESSION['base_uri'] ?>/treatments" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "Edit Treatment";
$module    = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
