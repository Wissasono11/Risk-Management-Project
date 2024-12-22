<?php 
use App\Services\RiskRegisterService;

global $riskEdit;
ob_start(); 
?>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/risk-register-form.css">

<div class="risk-container">
    <div class="risk-header">
        <div class="header-left">
            <h2 class="header-title">Edit Risk</h2>
            <p class="header-subtitle">Update risk assessment details</p>
        </div>
        <div class="header-actions">
            <a href="<?= $_SESSION['base_uri'] ?>/risk-register" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Risks
            </a>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" action="<?= $_SESSION['base_uri'] ?>/risk-register/update" class="risk-form">
            <input type="hidden" name="id" value="<?= $riskEdit['id'] ?>">
            
        <!-- Basic Information -->
        <div class="form-section">
            <h3 class="section-title">Basic Information</h3>
            
            <div class="form-group">
                <label class="required">Objective</label>
                <textarea name="objective" class="form-control" required rows="3" 
                          placeholder="Enter the objective of this risk assessment"></textarea>
                <span class="form-hint">Describe the main objective or goal related to this risk</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="required">Business Process</label>
                    <input type="text" name="proses_bisnis" class="form-control" required 
                           placeholder="Enter related business process">
                </div>
                
                <div class="form-group">
                    <label class="required">Risk Category</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="1">Strategic Risk</option>
                        <option value="2">Operational Risk</option>
                        <option value="3">Financial Risk</option>
                        <option value="4">Compliance Risk</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Risk Details -->
        <div class="form-section">
            <h3 class="section-title">Risk Details</h3>
            
            <div class="form-group">
                <label class="required">Risk Event</label>
                <textarea name="risk_event" class="form-control" required rows="3" 
                          placeholder="Describe the potential risk event"></textarea>
                <span class="form-hint">What could happen? Be specific about the risk event</span>
            </div>

            <div class="form-group">
                <label class="required">Risk Cause</label>
                <textarea name="risk_cause" class="form-control" required rows="3" 
                          placeholder="Describe what could cause this risk"></textarea>
                <span class="form-hint">What are the root causes of this potential risk?</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="required">Risk Source</label>
                    <select name="risk_source" class="form-control" required>
                        <option value="internal">Internal</option>
                        <option value="external">External</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="required">Risk Owner (PIC)</label>
                    <input type="text" name="risk_owner" class="form-control" required 
                           placeholder="Person in charge">
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="form-section">
            <h3 class="section-title">Risk Assessment</h3>
            <p class="section-desc">Assess the likelihood and impact of the risk occurring</p>

            <div class="form-row">
                <div class="form-group">
                    <label class="required">Likelihood</label>
                    <div class="rating-select">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" name="likelihood" value="<?= $i ?>" 
                                   id="likelihood_<?= $i ?>" required 
                                   <?= ($i === 1 ? 'checked' : '') ?>>
                            <label for="likelihood_<?= $i ?>" class="rating-label">
                                <span class="rating-value"><?= $i ?></span>
                                <span class="rating-text">
                                    <?= ['Very Low', 'Low', 'Medium', 'High', 'Very High'][$i-1] ?>
                                </span>
                            </label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="required">Impact</label>
                    <div class="rating-select">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" name="impact" value="<?= $i ?>" 
                                   id="impact_<?= $i ?>" required 
                                   <?= ($i === 1 ? 'checked' : '') ?>>
                            <label for="impact_<?= $i ?>" class="rating-label">
                                <span class="rating-value"><?= $i ?></span>
                                <span class="rating-text">
                                    <?= ['Very Low', 'Low', 'Medium', 'High', 'Very High'][$i-1] ?>
                                </span>
                            </label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

                <div class="risk-matrix-preview">
                </div>
            </div>
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                    Reset Form
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= isset($riskEdit) ? 'Update Risk' : 'Save Risk' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= $_SESSION['base_uri'] ?>/assets/js/risk-register-form.js"></script>

<?php
$content = ob_get_clean();
$pageTitle = "Edit Risk";
$module = "risk_register";
require __DIR__ . '/../../layouts/app.blade.php';
?>