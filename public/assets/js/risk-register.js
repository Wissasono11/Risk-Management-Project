// assets/js/risk-register.js

class RiskManager {
    constructor() {
        this.deleteModal = this.createDeleteModal();
        this.matrixModal = this.createMatrixModal();
        this.currentRiskId = null; // Initialize the currentRiskId
        this.init();
    }

    init() {
        document.body.appendChild(this.deleteModal);
        document.body.appendChild(this.matrixModal);
        this.initMatrix();
        this.setupEventListeners();
    }

    createDeleteModal() {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.id = 'deleteModal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Confirm Delete</h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p>Are you sure you want to delete this risk? This action cannot be undone.</p>
                    <div class="modal-actions">
                        <button class="btn btn-secondary modal-close">Cancel</button>
                        <button class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        `;
        return modal;
    }

    createMatrixModal() {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.id = 'matrixModal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Risks in Selected Cell</h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body" id="matrixModalContent">
                </div>
            </div>
        `;
        return modal;
    }

    initMatrix() {
        const matrix = document.querySelector('.risk-matrix');
        if (!matrix) return;

        console.log('Initializing Risk Matrix with data:', window.matrixData); // Debugging

        // Create matrix header
        const header = document.createElement('div');
        header.className = 'matrix-header';
        header.innerHTML = `
            <div class="matrix-label">Likelihood</div>
            <div class="matrix-impacts">
                <div>Very Low</div>
                <div>Low</div>
                <div>Medium</div>
                <div>High</div>
                <div>Very High</div>
            </div>
        `;
        matrix.appendChild(header);

        // Create matrix body
        const body = document.createElement('div');
        body.className = 'matrix-body';

        for (let i = 5; i >= 1; i--) {
            const row = document.createElement('div');
            row.className = 'matrix-row';
            row.innerHTML = `<div class="matrix-likelihood">${i}</div>`;

            for (let j = 1; j <= 5; j++) {
                const cell = document.createElement('div');
                cell.className = 'matrix-cell';
                cell.dataset.likelihood = i;
                cell.dataset.impact = j;

                const level = this.calculateLevel(i, j);
                cell.classList.add(`level-${level}`);

                const count = window.matrixData?.[i]?.[j]?.length || 0;
                cell.textContent = count;

                if (count > 0) {
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', () => this.showMatrixDetails(i, j));
                }

                row.appendChild(cell);
            }
            body.appendChild(row);
        }

        matrix.appendChild(body);
    }

    calculateLevel(likelihood, impact) {
        const score = likelihood * impact;
        if (score >= 16) return 'extreme';
        if (score >= 10) return 'high';
        if (score >= 5) return 'medium';
        return 'low';
    }

    showMatrixDetails(likelihood, impact) {
        const risks = window.matrixData?.[likelihood]?.[impact] || [];
        const content = document.getElementById('matrixModalContent');

        content.innerHTML = risks.length ? `
            <div class="matrix-risks">
                ${risks.map(risk => `
                    <div class="matrix-risk-item">
                        <div class="risk-title">${risk.risk_event}</div>
                        <div class="risk-details">
                            <span class="risk-owner">${risk.risk_owner}</span>
                            <span class="risk-category">${risk.kategori_nama}</span>
                        </div>
                        <div class="risk-actions">
                            <a href="${window.baseUrl}/risk-register/view?id=${risk.id}" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                `).join('')}
            </div>
        ` : '<div class="empty-message">No risks in this cell</div>';

        this.openModal('matrixModal');
    }

    setupEventListeners() {
        // Delete confirmation
        document.addEventListener('click', (e) => {
            const deleteButton = e.target.closest('.btn-delete');
            if (deleteButton) {
                e.preventDefault();
                const riskId = deleteButton.getAttribute('data-id');
                this.openDeleteConfirmation(riskId);
            }
        });

        // Modal close buttons
        document.body.addEventListener('click', (e) => {
            if (e.target.matches('.modal-close')) {
                this.closeAllModals();
            }
        });

        // Confirm delete action
        document.addEventListener('click', (e) => {
            if (e.target.id === 'confirmDelete') {
                this.confirmDelete();
            }
        });

        // Close modal on outside click
        document.addEventListener('click', (e) => {
            if (e.target.matches('.modal')) {
                this.closeAllModals();
            }
        });
    }

    openDeleteConfirmation(riskId) {
        this.currentRiskId = riskId;
        this.openModal('deleteModal');
    }

    confirmDelete() {
        if (this.currentRiskId) {
            // Redirect untuk penghapusan
            window.location.href = `${window.baseUrl}/risk-register/delete?id=${this.currentRiskId}`;
        }
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
    }
}

// Inisialisasi saat dokumen siap
document.addEventListener('DOMContentLoaded', () => {
    window.riskManager = new RiskManager();
});
