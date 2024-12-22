class TreatmentManager {
    constructor() {
        this.init();
    }

    init() {
        this.createModals();
        this.setupEventListeners();
    }

    createModals() {
        // Create modals HTML
        const modalTemplate = `
            <!-- Treatment Modal -->
            <div class="modal" id="treatmentModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Add Treatment</h3>
                        <button type="button" class="modal-close">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="treatmentForm" class="treatment-form">
                            <input type="hidden" name="risk_id" id="riskId">
                            
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
                                <label>Timeline (${new Date().getFullYear()})</label>
                                <div class="timeline-checkboxes">
                                    ${[1,2,3,4].map(q => `
                                        <div class="quarter-checkbox">
                                            <input type="checkbox" name="timeline[${q}]" id="q${q}" value="1">
                                            <label for="q${q}">Q${q}</label>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Treatment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal" id="confirmationModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Confirm Delete</h3>
                        <button type="button" class="modal-close">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="confirm-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p>Are you sure you want to delete this treatment?</p>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div class="toast" id="toast"></div>
        `;

        // Add modals to body
        document.body.insertAdjacentHTML('beforeend', modalTemplate);

        // Store modal references
        this.treatmentModal = document.getElementById('treatmentModal');
        this.confirmationModal = document.getElementById('confirmationModal');
        this.treatmentForm = document.getElementById('treatmentForm');
    }

    setupEventListeners() {
        // Add Treatment button
        const addBtn = document.querySelector('.btn-add-treatment');
        if (addBtn) {
            addBtn.addEventListener('click', () => this.openTreatmentModal());
        }

        // Close buttons
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                this.closeModal(modal);
            });
        });

        // Form submit
        this.treatmentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e.target);
        });

        // Delete confirmation
        document.getElementById('confirmDelete').addEventListener('click', () => {
            this.handleDelete();
        });
    }

    openTreatmentModal(id = null) {
        this.currentId = id;
        const modal = this.treatmentModal;
        const title = modal.querySelector('#modalTitle');
        
        title.textContent = id ? 'Edit Treatment' : 'Add Treatment';
        
        if (id) {
            this.loadTreatmentData(id);
        } else {
            this.treatmentForm.reset();
        }

        modal.classList.add('show');
    }

    closeModal(modal) {
        modal.classList.remove('show');
    }

    async loadTreatmentData(id) {
        try {
            const response = await fetch(`${baseUrl}/treatments/${id}`);
            const data = await response.json();
            
            if (data.success) {
                this.populateForm(data.treatment);
            }
        } catch (error) {
            this.showToast('Failed to load treatment data', 'error');
        }
    }

    populateForm(treatment) {
        const form = this.treatmentForm;
        form.querySelector('[name="rencana_mitigasi"]').value = treatment.rencana_mitigasi;
        form.querySelector('[name="pic"]').value = treatment.pic;
        form.querySelector('[name="evidence_type"]').value = treatment.evidence_type;

        // Handle timeline checkboxes
        if (treatment.timelines) {
            treatment.timelines.forEach(tl => {
                const checkbox = form.querySelector(`[name="timeline[${tl.triwulan}]"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }

    async handleSubmit(form) {
        try {
            const formData = new FormData(form);
            const url = this.currentId 
                ? `${baseUrl}/treatments/update/${this.currentId}`
                : `${baseUrl}/treatments/store`;

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.showToast('Treatment saved successfully', 'success');
                this.closeModal(this.treatmentModal);
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (error) {
            this.showToast('Failed to save treatment', 'error');
        }
    }

    openDeleteConfirmation(id) {
        this.currentId = id;
        this.confirmationModal.classList.add('show');
    }

    async handleDelete() {
        try {
            const response = await fetch(`${baseUrl}/treatments/delete/${this.currentId}`, {
                method: 'POST'
            });

            const result = await response.json();
            
            if (result.success) {
                this.showToast('Treatment deleted successfully', 'success');
                this.closeModal(this.confirmationModal);
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (error) {
            this.showToast('Failed to delete treatment', 'error');
        }
    }

    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.className = `toast toast-${type} show`;
        toast.textContent = message;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}

// Initialize on document load
document.addEventListener('DOMContentLoaded', () => {
    window.treatmentManager = new TreatmentManager();
});