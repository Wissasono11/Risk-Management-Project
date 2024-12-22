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

        // Bersihkan matrix yang ada
        matrix.innerHTML = '';

        // Tambahkan header utama (Impact & Likelihood)
        const mainHeader = document.createElement('div');
        mainHeader.className = 'matrix-cell header main-header';
        mainHeader.innerHTML = `
            <div class="impact-label">Impact ↑</div>
            <div class="likelihood-label">Likelihood →</div>
        `;
        matrix.appendChild(mainHeader);

        // Headers untuk Impact (1-5)
        for (let i = 1; i <= 5; i++) {
            const header = document.createElement('div');
            header.className = 'matrix-cell header';
            header.textContent = i;
            matrix.appendChild(header);
        }

        // Define risk scores dan warna
        const cells = {
            5: [
                { score: 5, level: 'high', text: 'HIGH-5' },
                { score: 10, level: 'high', text: 'HIGH-10' },
                { score: 15, level: 'high', text: 'HIGH-15' },
                { score: 20, level: 'very-high', text: 'VERY-HIGH-20' },
                { score: 25, level: 'very-high', text: 'VERY-HIGH-25' }
            ],
            4: [
                { score: 4, level: 'low', text: 'LOW-4' },
                { score: 8, level: 'medium', text: 'MEDIUM-8' },
                { score: 12, level: 'high', text: 'HIGH-12' },
                { score: 16, level: 'very-high', text: 'VERY HIGH-16' },
                { score: 20, level: 'very-high', text: 'VERY HIGH-20' }
            ],
            3: [
                { score: 3, level: 'low', text: 'LOW-3' },
                { score: 6, level: 'medium', text: 'MEDIUM-6' },
                { score: 9, level: 'medium', text: 'MEDIUM-9' },
                { score: 12, level: 'high', text: 'HIGH-12' },
                { score: 15, level: 'high', text: 'HIGH-15' }
            ],
            2: [
                { score: 2, level: 'low', text: 'LOW-2' },
                { score: 4, level: 'low', text: 'LOW-4' },
                { score: 6, level: 'medium', text: 'MEDIUM-6' },
                { score: 8, level: 'medium', text: 'MEDIUM-8' },
                { score: 10, level: 'high', text: 'HIGH-10' }
            ],
            1: [
                { score: 1, level: 'low', text: 'LOW-1' },
                { score: 2, level: 'low', text: 'LOW-2' },
                { score: 3, level: 'low', text: 'LOW-3' },
                { score: 4, level: 'low', text: 'LOW-4' },
                { score: 5, level: 'high', text: 'HIGH-5' }
            ]
        };

        // Buat rows matrix
        for (let i = 5; i >= 1; i--) {
            // Header likelihood
            const rowHeader = document.createElement('div');
            rowHeader.className = 'matrix-cell header';
            rowHeader.textContent = i;
            matrix.appendChild(rowHeader);

            // Cells untuk setiap baris
            for (let j = 0; j < 5; j++) {
                const cell = document.createElement('div');
                cell.className = 'matrix-cell';
                
                const cellData = cells[i][j];
                cell.classList.add(`level-${cellData.level}`);
                
                const count = window.matrixData?.[i]?.[j + 1]?.length || 0;
                
                cell.innerHTML = `
                    <div class="cell-content">
                        <div class="score">${cellData.text}</div>
                        ${count > 0 ? `<div class="count">(${count})</div>` : ''}
                    </div>
                `;

                if (count > 0) {
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', () => this.showMatrixDetails(i, j + 1));
                }

                // Tambahkan garis toleransi
                if ((i === 3 && j === 3) || (i === 4 && j === 2) || (i === 5 && j === 1)) {
                    cell.classList.add('tolerance-line');
                }

                matrix.appendChild(cell);
            }
        }
        
        const footerLabels = ['Rare', 'Unlikely', 'Moderate', 'Likely', 'Almost Certain'];
        
        // Header kosong untuk alignment
        const emptyHeader = document.createElement('div');
        emptyHeader.className = 'matrix-cell header footer-header invisible';
        matrix.appendChild(emptyHeader);

        // Labels
        footerLabels.forEach(label => {
            const labelCell = document.createElement('div');
            labelCell.className = 'matrix-cell header footer-label';
            labelCell.textContent = label;
            matrix.appendChild(labelCell);
        });
    }

    updateRiskLevelLabels() {
        const badges = document.querySelectorAll('.status-badge');
        const levelMapping = {
            'low': 'Low',
            'medium': 'Medium',
            'high': 'High',
            'very-high': 'Very High'
        };

        badges.forEach(badge => {
            // Cari kelas yang dimulai dengan 'status-'
            const statusClass = Array.from(badge.classList).find(cls => cls.startsWith('status-'));
            if (statusClass) {
                const levelKey = statusClass.replace('status-', '');
                const label = levelMapping[levelKey] || 'Unknown';
                badge.textContent = label;

                // Sesuaikan kelas CSS jika diperlukan
                // Misalnya, menambahkan kelas 'level-st' untuk 'Very High'
                const levelClassMapping = {
                    'low': 'level-r',
                    'medium': 'level-s',
                    'high': 'level-t',
                    'very-high': 'level-st'
                };

                // Hapus kelas level sebelumnya
                badge.classList.forEach(cls => {
                    if (cls.startsWith('level-')) {
                        badge.classList.remove(cls);
                    }
                });

                // Tambahkan kelas level yang sesuai
                const newLevelClass = levelClassMapping[levelKey];
                if (newLevelClass) {
                    badge.classList.add(newLevelClass);
                }
            }
        });
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
