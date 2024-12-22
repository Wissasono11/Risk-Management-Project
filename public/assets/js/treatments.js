// treatments.js

class TreatmentManager {
    constructor() {
        // Gunakan baseUrl yang didefinisikan di blade
        this.bindEvents();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Tombol Add Treatment
        const addBtn = document.querySelector('.btn-add-treatment');
        if (addBtn) {
            addBtn.addEventListener('click', () => this.openTreatmentModal());
        }

        // Event Delegation untuk tombol Edit dan Delete
        const treatmentTable = document.querySelector('.treatment-table');
        if (treatmentTable) {
            treatmentTable.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.btn-edit');
                const deleteBtn = e.target.closest('.btn-delete');

                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    this.openTreatmentModal(id);
                }

                if (deleteBtn) {
                    const id = deleteBtn.getAttribute('data-id');
                    this.openDeleteConfirmation(id);
                }
            });
        }

        // Submit Formulir Add/Edit
        const treatmentForm = document.getElementById('treatmentForm');
        if (treatmentForm) {
            treatmentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit(e.target);
            });
        }

        // Submit Formulir Delete
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleDelete();
            });
        }
    }

    async openTreatmentModal(id = null) {
        const modalTitle = document.getElementById('treatmentModalLabel');
        const treatmentForm = document.getElementById('treatmentForm');

        if (id) {
            // Edit Treatment
            modalTitle.textContent = 'Edit Treatment';
            treatmentForm.reset();
            try {
                const response = await fetch(`${this.baseUrl}/treatments/view?id=${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.populateForm(data.treatment);
                } else {
                    this.showToast(data.message || 'Failed to load treatment data', 'danger');
                    return;
                }
            } catch (error) {
                this.showToast('Failed to load treatment data', 'danger');
                return;
            }
        } else {
            // Add Treatment
            modalTitle.textContent = 'Add Treatment';
            treatmentForm.reset();
            document.getElementById('treatmentId').value = '';
            document.getElementById('riskId').value = '';
        }

        // Tampilkan modal menggunakan Bootstrap's Modal API
        const treatmentModalEl = document.getElementById('treatmentModal');
        const treatmentModal = new bootstrap.Modal(treatmentModalEl);
        treatmentModal.show();
    }

    populateForm(treatment) {
        document.getElementById('treatmentId').value = treatment.id;
        document.getElementById('riskId').value = treatment.risk_register_id;
        document.getElementById('rencana_mitigasi').value = treatment.rencana_mitigasi;
        document.getElementById('pic').value = treatment.pic;
        document.getElementById('evidence_type').value = treatment.evidence_type;

        // Reset semua checkbox
        document.querySelectorAll('.timeline-checkboxes input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Set checkbox sesuai timelines
        if (treatment.timelines) {
            treatment.timelines.forEach(tl => {
                const checkbox = document.querySelector(`[name="timeline[${tl.triwulan}]"]`);
                if (checkbox) {
                    checkbox.checked = !!tl.realisasi;
                }
            });
        }
    }

    async handleSubmit(form) {
        const id = document.getElementById('treatmentId').value;
        const isEdit = !!id;
        const url = isEdit ? `${this.baseUrl}/treatments/update` : `${this.baseUrl}/treatments/store`;
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (result.success) {
                this.showToast(result.message || (isEdit ? 'Treatment updated successfully' : 'Treatment created successfully'), 'success');
                // Tutup modal
                const treatmentModalEl = document.getElementById('treatmentModal');
                const treatmentModal = bootstrap.Modal.getInstance(treatmentModalEl);
                treatmentModal.hide();
                // Reload halaman setelah beberapa detik
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showToast(result.message || 'Failed to save treatment', 'danger');
            }
        } catch (error) {
            this.showToast('Failed to save treatment', 'danger');
        }
    }

    openDeleteConfirmation(id) {
        document.getElementById('deleteTreatmentId').value = id;
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        confirmationModal.show();
    }

    async handleDelete() {
        const id = document.getElementById('deleteTreatmentId').value;
        if (!id) {
            this.showToast('No treatment selected for deletion', 'danger');
            return;
        }

        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch(`${this.baseUrl}/treatments/delete`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (result.success) {
                this.showToast(result.message || 'Treatment deleted successfully', 'success');
                // Tutup modal konfirmasi
                const confirmationModalEl = document.getElementById('confirmationModal');
                const confirmationModal = bootstrap.Modal.getInstance(confirmationModalEl);
                confirmationModal.hide();
                // Reload halaman setelah beberapa detik
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showToast(result.message || 'Failed to delete treatment', 'danger');
            }
        } catch (error) {
            this.showToast('Failed to delete treatment', 'danger');
        }
    }

    showToast(message, type = 'success') {
        const toastEl = document.getElementById('toast');
        const toastBody = toastEl.querySelector('.toast-body');
        toastBody.textContent = message;
        toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();

        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.hide();
        }, 3000);
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.treatmentManager = new TreatmentManager();
});