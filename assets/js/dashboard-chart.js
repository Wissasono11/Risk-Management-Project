function getChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        },
        plugins: {
            legend: {
                display: window.innerWidth > 768, // Hide legend on mobile
                position: 'top',
                labels: {
                    boxWidth: window.innerWidth < 768 ? 8 : 12,
                    padding: window.innerWidth < 768 ? 8 : 10,
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    }
                }
            },
            tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: window.innerWidth < 768 ? 8 : 12,
                titleFont: {
                    size: window.innerWidth < 768 ? 12 : 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: window.innerWidth < 768 ? 11 : 13
                },
                cornerRadius: 8,
                displayColors: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    }
                },
                grid: {
                    display: true,
                    drawBorder: false,
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    },
                    maxRotation: window.innerWidth < 768 ? 45 : 0
                },
                grid: {
                    display: false
                }
            }
        }
    };
}

function initializeCharts(riskDistribution) {
    if (!riskDistribution) return;

    const riskCtx = document.getElementById('riskDistributionChart');
    if (!riskCtx) return;

    // Destroy existing chart if it exists
    if (window.riskChart instanceof Chart) {
        window.riskChart.destroy();
    }

    window.riskChart = new Chart(riskCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Very Low', 'Low', 'Medium', 'High', 'Very High'],
            datasets: [{
                label: 'Number of Risks',
                data: [
                    riskDistribution.sr_count || 0,
                    riskDistribution.r_count || 0,
                    riskDistribution.s_count || 0,
                    riskDistribution.t_count || 0,
                    riskDistribution.st_count || 0
                ],
                backgroundColor: [
                    'var(--glacier)',    // Very Low
                    'var(--blue-chill)', // Low
                    'var(--atol)',       // Medium
                    'var(--tiber)',      // High
                    'var(--black-pearl)' // Very High
                ],
                borderWidth: 0,
                borderRadius: window.innerWidth < 768 ? 3 : 5,
                barThickness: window.innerWidth < 768 ? 15 : 20
            }]
        },
        options: getChartOptions()
    });
}

function initializeRiskMatrix() {
    const cells = document.querySelectorAll('.matrix-cell:not(.header)');
    cells.forEach(cell => {
        // Touch and mouse events
        ['mouseover', 'touchstart'].forEach(eventType => {
            cell.addEventListener(eventType, function(e) {
                if (eventType === 'touchstart') e.preventDefault();
                
                const value = this.textContent.trim();
                const level = this.classList.contains('extreme') ? 'Extreme Risk' :
                             this.classList.contains('high') ? 'High Risk' :
                             this.classList.contains('medium') ? 'Medium Risk' : 'Low Risk';

                // Create or update tooltip
                this.setAttribute('title', `Risk Score: ${value}\nRisk Level: ${level}`);
                
                // Visual feedback
                this.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Card animations
    const cards = document.querySelectorAll('.card-single');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Initialize matrix
    initializeRiskMatrix();

    // Handle resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.riskChart instanceof Chart) {
                window.riskChart.options = getChartOptions();
                window.riskChart.update();
            }
        }, 250);
    });
});
