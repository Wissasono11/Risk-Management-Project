<?php
global $treatmentEdit;
ob_start();
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">

<div class="treatments-container">
    <!-- Header Section -->
    <div class="treatments-header d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="header-title">Edit Treatment</h1>
            <p class="header-subtitle">For Risk: <?= htmlspecialchars($treatmentEdit['risk_event'] ?? '') ?></p>
        </div>
        <div class="header-actions">
            <button type="button" 
                    onclick="window.location.href='<?= $_SESSION['base_uri'] ?>/treatments'" 
                    class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Treatments
            </button>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/treatments/update" class="treatment-form">
            <!-- Hidden ID -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($treatmentEdit['id'] ?? '') ?>">

            <!-- Treatment Plan -->
            <div class="form-group">
                <label for="rencana_mitigasi" class="form-label required">Treatment Plan</label>
                <textarea name="rencana_mitigasi" 
                          id="rencana_mitigasi" 
                          class="form-control" 
                          rows="4" 
                          required><?= htmlspecialchars($treatmentEdit['rencana_mitigasi'] ?? '') ?></textarea>
                <small class="form-text text-muted">Describe how this risk will be treated</small>
            </div>

            <!-- PIC -->
            <div class="form-group">
                <label for="pic" class="form-label required">Person In Charge (PIC)</label>
                <input type="text" 
                       name="pic" 
                       id="pic" 
                       class="form-control" 
                       required 
                       value="<?= htmlspecialchars($treatmentEdit['pic'] ?? '') ?>">
                <small class="form-text text-muted">Person responsible for implementing this treatment</small>
            </div>

            <!-- Evidence Type -->
            <div class="form-group">
                <label for="evidence_type" class="form-label required">Evidence Type</label>
                <select name="evidence_type" id="evidence_type" class="form-control" required>
                    <option value="Dokumen Administratif" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Dokumen Administratif' ? 'selected' : '' ?>>
                        Dokumen Administratif
                    </option>
                    <option value="Pelatihan" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Pelatihan' ? 'selected' : '' ?>>
                        Pelatihan
                    </option>
                    <option value="Observasi Lapangan" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Observasi Lapangan' ? 'selected' : '' ?>>
                        Observasi Lapangan
                    </option>
                    <option value="Survei Mahasiswa/Dosen" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Survei Mahasiswa/Dosen' ? 'selected' : '' ?>>
                        Survei Mahasiswa/Dosen
                    </option>
                    <option value="Laporan Audit" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Laporan Audit' ? 'selected' : '' ?>>
                        Laporan Audit
                    </option>
                    <option value="Hasil Diskusi/Forum" <?= ($treatmentEdit['evidence_type'] ?? '') === 'Hasil Diskusi/Forum' ? 'selected' : '' ?>>
                        Hasil Diskusi/Forum
                    </option>
                </select>
                <small class="form-text text-muted">Type of evidence required to verify completion</small>
            </div>

            <!-- Timeline -->
            <div class="form-group">
                <label class="form-label required">Timeline</label>
                <div class="timeline-grid">
                    <?php
                    $currentYear = date('Y');
                    $checkedQuarters = [];
                    if (!empty($treatmentEdit['timelines'])) {
                        foreach ($treatmentEdit['timelines'] as $timeline) {
                            $checkedQuarters[$timeline['triwulan']] = !empty($timeline['realisasi']);
                        }
                    }
                    ?>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="timeline[<?= $i ?>]" 
                                   id="q<?= $i ?>" 
                                   value="1" 
                                   class="form-check-input"
                                   <?= isset($checkedQuarters[$i]) ? 'checked' : '' ?>>
                            <label for="q<?= $i ?>" class="form-check-label">Q<?= $i ?> - <?= $currentYear ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
                <small class="form-text text-muted">Check the quarters where this treatment is planned or completed</small>
            </div>

            <!-- Form Actions -->
            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Treatment
                </button>
                <button type="button" 
                        onclick="window.location.href='<?= $_SESSION['base_uri'] ?>/treatments'" 
                        class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "Edit Treatment";
$module = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
?>
