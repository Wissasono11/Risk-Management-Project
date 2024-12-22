<?php
global $riskList;
ob_start();
require_once __DIR__ . '/../../../app/Helpers/calculateTreatmentStatus.php'; 
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/treatments.css">

<div class="treatments-container">
    <!-- Header Section -->
    <div class="treatments-header d-flex justify-content-between align-items-center mb-4">
        <div class="header-left">
            <h1 class="header-title">Add New Treatment</h1>
            <p class="header-subtitle">Create treatment plan for risk mitigation</p>
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
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/treatments/store" class="treatment-form">
            <input type="hidden" name="risk_id" value="<?= htmlspecialchars($_GET['risk_id'] ?? '') ?>">
            
            <!-- Risk Selection Section (if risk_id not provided) -->
            <?php if (empty($_GET['risk_id'])): ?>
            <div class="form-group">
                <label for="risk_id" class="form-label required">Select Risk</label>
                <select name="risk_id" id="risk_id" class="form-select" required>
                    <option value="">Choose a risk event...</option>
                    <?php foreach($riskList as $risk): ?>
                        <option value="<?= htmlspecialchars($risk['id']) ?>">
                            <?= htmlspecialchars($risk['risk_event']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Choose the risk event that this treatment will address</small>
            </div>
            <?php endif; ?>

            <!-- Treatment Details -->
            <div class="form-group">
                <label for="rencana_mitigasi" class="form-label required">Treatment Plan</label>
                <textarea name="rencana_mitigasi" 
                         id="rencana_mitigasi" 
                         class="form-control" 
                         rows="4" 
                         required
                         placeholder="Describe the treatment plan in detail..."></textarea>
                <small class="form-text text-muted">Provide a clear and detailed description of how the risk will be treated</small>
            </div>

            <!-- Person In Charge -->
            <div class="form-group">
                <label for="pic" class="form-label required">Person In Charge (PIC)</label>
                <input type="text" 
                       name="pic" 
                       id="pic" 
                       class="form-control" 
                       required
                       placeholder="Enter the name of responsible person">
                <small class="form-text text-muted">Specify who will be responsible for implementing this treatment</small>
            </div>

            <!-- Evidence Type -->
            <div class="form-group">
                <label for="evidence_type" class="form-label required">Evidence Type</label>
                <select name="evidence_type" id="evidence_type" class="form-select" required>
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
                <small class="form-text text-muted">Specify what kind of evidence will be required to verify completion</small>
            </div>
            <!-- Timeline Section -->
            <div class="form-group">
                <label class="form-label required">Implementation Timeline</label>
                <div class="timeline-grid">
                    <?php for($i = 1; $i <= 4; $i++): ?>
                        <div class="form-check">
                            <input type="radio" 
                                   class="form-check-input" 
                                   name="timeline" 
                                   id="q<?= $i ?>" 
                                   value="<?= $i ?>">
                            <label class="form-check-label" for="q<?= $i ?>">
                                Q<?= $i ?> - <?= date('Y') ?>
                            </label>
                        </div>
                    <?php endfor; ?>
                </div>
                <small class="form-text text-muted">Select only one quarter for implementation</small>
            </div>

            <!-- Form Actions -->
            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Treatment
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
$pageTitle = "Add Treatment";
$module = "treatments";
require __DIR__ . '/../../layouts/app.blade.php';
?>
