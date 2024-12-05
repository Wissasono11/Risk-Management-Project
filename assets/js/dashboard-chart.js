function initializeCharts(riskDistribution) {
    // Risk Distribution Chart
    const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
    const riskChart = new Chart(riskCtx, {
        type: 'bar',
        data: {
            labels: ['Very Low', 'Low', 'Medium', 'High', 'Very High'],
            datasets: [{
                label: 'Number of Risks',
                data: [
                    riskDistribution.sr_count,
                    riskDistribution.r_count,
                    riskDistribution.s_count,
                    riskDistribution.t_count,
                    riskDistribution.st_count
                ],
                backgroundColor: [
                    '#4BC0C0',
                    '#36A2EB',
                    '#FFCE56',
                    '#FF9F40',
                    '#FF6384'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Initialize Risk Matrix interactions
function initializeRiskMatrix() {
    const cells = document.querySelectorAll('.matrix-cell:not(.header)');
    cells.forEach(cell => {
        cell.addEventListener('mouseover', function() {
            const value = this.textContent;
            const level = this.classList.contains('extreme') ? 'Extreme Risk' :
                         this.classList.contains('high') ? 'High Risk' :
                         this.classList.contains('medium') ? 'Medium Risk' : 'Low Risk';
                         
            this.setAttribute('title', `Risk Score: ${value}\nRisk Level: ${level}`);
        });
    });
}

// Add loading animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-single');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    initializeRiskMatrix();
});
