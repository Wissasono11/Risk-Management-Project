// users.js
class UserForm {
    constructor() {
        this.modal = null;
        this.form = null;
        this.toastTimeout = null;
        this.init();
    }

    init() {
        this.createModal();
        this.createToast();
        this.setupEventListeners();
        this.setupFormValidation();
    }

    createModal() {
        const modalHtml = `
            <div class="modal" id="userModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Add New User</h2>
                        <button class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form content will be moved here -->
                    </div>
                </div>
            </div>

            <div class="modal" id="deleteModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Confirm Delete</h2>
                        <button class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="delete-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p>Are you sure you want to delete this user?</p>
                        <div class="modal-actions">
                            <button class="btn btn-secondary modal-close">Cancel</button>
                            <button class="btn btn-danger" id="confirmDelete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.modal = document.getElementById('userModal');
    }

    createToast() {
        const toastHtml = `
            <div class="toast-container">
                <div class="toast" id="toast">
                    <div class="toast-content">
                        <i class="toast-icon"></i>
                        <div class="toast-message"></div>
                    </div>
                    <div class="toast-progress"></div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
    }

    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon = toast.querySelector('.toast-icon');
        const msg = toast.querySelector('.toast-message');
        const progress = toast.querySelector('.toast-progress');

        // Clear previous timeout
        if (this.toastTimeout) {
            clearTimeout(this.toastTimeout);
            toast.classList.remove('show');
        }

        // Set icon and color based on type
        if (type === 'success') {
            icon.className = 'toast-icon fas fa-check-circle';
            toast.className = 'toast toast-success show';
        } else {
            icon.className = 'toast-icon fas fa-exclamation-circle';
            toast.className = 'toast toast-error show';
        }

        msg.textContent = message;
        progress.style.width = '100%';

        // Auto hide after 3 seconds
        this.toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    setupEventListeners() {
        // Open modal button
        const addButton = document.querySelector('.btn-add-user');
        if (addButton) {
            addButton.addEventListener('click', () => this.openModal());
        }

        // Close modal button
        const closeButton = this.modal.querySelector('.modal-close');
        closeButton.addEventListener('click', () => this.closeModal());

        // Close on outside click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.closeModal();
        });

        // Role select change
        const roleSelect = document.querySelector('select[name="role"]');
        if (roleSelect) {
            roleSelect.addEventListener('change', (e) => this.toggleFakultas(e.target.value));
        }
    }

    setupFormValidation() {
        this.form = document.querySelector('form');
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validateForm()) {
                this.submitForm();
            }
        });
    }

    validateForm() {
        let isValid = true;
        const email = this.form.querySelector('input[name="email"]');
        const password = this.form.querySelector('input[name="password"]');

        // Email validation
        if (!this.validateEmail(email.value)) {
            this.showError(email, 'Please enter a valid email address');
            isValid = false;
        } else {
            this.removeError(email);
        }

        // Password validation for new user
        if (!this.form.querySelector('input[name="id"]') && password.value.length < 6) {
            this.showError(password, 'Password must be at least 6 characters long');
            isValid = false;
        } else {
            this.removeError(password);
        }

        return isValid;
    }

    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    showError(input, message) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.add('error');
        const error = formGroup.querySelector('.error-message') || document.createElement('div');
        error.className = 'error-message';
        error.textContent = message;
        if (!formGroup.querySelector('.error-message')) {
            formGroup.appendChild(error);
        }
    }

    removeError(input) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.remove('error');
        const error = formGroup.querySelector('.error-message');
        if (error) error.remove();
    }

    toggleFakultas(role) {
        const fakultasGroup = document.getElementById('fakultas-group');
        fakultasGroup.style.display = role === 'fakultas' ? 'block' : 'none';
        
        if (role === 'fakultas') {
            const fakultasSelect = fakultasGroup.querySelector('select');
            fakultasSelect.required = true;
        }
    }

    openModal() {
        this.modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    async submitForm() {
        try {
            const formData = new FormData(this.form);
            const response = await fetch(this.form.action, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                this.closeModal();
                this.showToast('User successfully saved!');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error('Something went wrong');
            }
        } catch (error) {
            this.showToast('Error saving user', 'error');
            console.error('Error:', error);
        }
    }

    async deleteUser(userId) {
        try {
            const response = await fetch(`${this.baseUrl}/users/delete?id=${userId}`, {
                method: 'GET'
            });

            if (response.ok) {
                this.showToast('User successfully deleted!');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error('Failed to delete user');
            }
        } catch (error) {
            this.showToast('Error deleting user', 'error');
            console.error('Error:', error);
        }
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', () => {
    new UserForm();
});
