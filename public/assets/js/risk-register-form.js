class RiskForm {
    constructor() {
        this.form = document.querySelector('.risk-form');
        this.matrixPreview = document.querySelector('.risk-matrix-preview');
        this.init();
    }

    init() {
        if (!this.form) return;
        
        // Setup event listeners
        this.setupFormValidation();
        this.setupMatrixPreview();
        this.setupResetHandler();
        
        // Initial matrix update
        this.updateMatrixPreview();
    }

    setupFormValidation() {
        // Form submit
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        const requiredFields = this.form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.validateField(field));
        });
    }

    setupResetHandler() {
        this.form.addEventListener('reset', (e) => {
            setTimeout(() => {
                this.updateMatrixPreview();
                this.removeAllErrors();
            }, 0);
        });
    }

    validateForm() {
        let isValid = true;
        const requiredFields = this.form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const formGroup = field.closest('.form-group');
        const value = field.value.trim();
        const isValid = value !== '';
        
        if (!isValid) {
            this.showError(field, 'This field is required');
        } else {
            this.removeError(field);
        }

        return isValid;
    }

    showError(field, message) {
        const formGroup = field.closest('.form-group');
        let errorDiv = formGroup.querySelector('.error-message');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            formGroup.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        formGroup.classList.add('has-error');
        field.setAttribute('aria-invalid', 'true');
    }

    removeError(field) {
        const formGroup = field.closest('.form-group');
        const errorDiv = formGroup.querySelector('.error-message');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        formGroup.classList.remove('has-error');
        field.removeAttribute('aria-invalid');
    }

    removeAllErrors() {
        this.form.querySelectorAll('.error-message').forEach(el => el.remove());
        this.form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
        this.form.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
    }

    setupMatrixPreview() {
        const ratingInputs = this.form.querySelectorAll('input[name="likelihood"], input[name="impact"]');
        ratingInputs.forEach(input => {
            input.addEventListener('change', () => this.updateMatrixPreview());
        });
    }

    updateMatrixPreview() {
        const likelihood = parseInt(this.form.querySelector('input[name="likelihood"]:checked')?.value) || 1;
        const impact = parseInt(this.form.querySelector('input[name="impact"]:checked')?.value) || 1;
        this.matrixPreview.innerHTML = this.generateMatrixHtml(likelihood, impact);
    }

    calculateRiskLevel(likelihood, impact) {
        const score = likelihood * impact;

        // Kondisi Khusus: Impact=5 dan Likelihood=1 atau Impact=1 dan Likelihood=5
        if (
            (impact === 5 && likelihood === 1) ||
            (impact === 1 && likelihood === 5)
        ) {
            return {
                level: 'High',
                color: '#ea580c', 
                background: '#ffebee'
            };
        }

        if (score >= 16) {
            return {
                level: 'Very High',
                color: '#dc2626', 
                background: '#8345ef'
            };
        }

        if (score >= 10) { 
            return {
                level: 'High',
                color: '#ea580c',
                background: '#ffebee'
            };
        }

        if (score >= 6) { 
            return {
                level: 'Medium',
                color: '#ca8a04',
                background: '#f1f8e9'
            };
        }

        return {
            level: 'Low',
            color: '#16a34a',
            background: '#e8f5e9'
        };
    }

    generateMatrixHtml(selectedL, selectedI) {
        const { level, color } = this.calculateRiskLevel(selectedL, selectedI);
        const score = selectedL * selectedI;

        return `
            <div class="matrix-result">
                <h4>Risk Level Assessment</h4>
                <div class="result-details">
                    <div class="result-item">
                        <span class="result-label">Score:</span>
                        <span class="result-value">${score}</span>
                    </div>
                    <div class="result-item">
                        <span class="result-label">Level:</span>
                        <span class="result-value" style="color: ${color}">${level}</span>
                    </div>
                </div>
                <p class="result-note">
                    Based on Likelihood (${selectedL}) × Impact (${selectedI})
                </p>
            </div>
        `;
    }
}

// Initialize on document load
document.addEventListener('DOMContentLoaded', () => {
    new RiskForm();
});