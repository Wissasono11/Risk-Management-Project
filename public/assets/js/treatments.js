// treatments.js
class TreatmentManager {
    constructor() {
        this.baseUrl = window.baseUrl;
        this.bindEvents();
    }

    bindEvents() {
        // Event Delegation untuk tombol Edit dan Delete
        const treatmentTable = document.querySelector('.treatment-table');
        if (treatmentTable) {
            treatmentTable.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.btn-edit');
                const deleteBtn = e.target.closest('.btn-delete');

                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    window.location.href = `${this.baseUrl}/treatments/edit?id=${id}`;
                }

                if (deleteBtn) {
                    const id = deleteBtn.getAttribute('data-id');
                    if (confirm('Are you sure you want to delete this treatment?')) {
                        window.location.href = `${this.baseUrl}/treatments/delete?id=${id}`;
                    }
                }
            });
        }

        // Handle Form Submit di halaman Edit
        const editForm = document.querySelector('form[action*="/treatments/update"]');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleUpdate(editForm);
            });
        }
    }

    async handleUpdate(form) {
        try {
            const formData = new FormData(form);
            const response = await fetch(`${this.baseUrl}/treatments/update`, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                alert('Treatment updated successfully');
                window.location.href = `${this.baseUrl}/treatments`;
            } else {
                alert('Failed to update treatment');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating treatment');
        }
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.treatmentManager = new TreatmentManager();
});