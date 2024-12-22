<?php
// resources/views/dashboard/index.blade.php

// Start output buffering:
ob_start();

// Ekstrak data
$stats = $data['stats'] ?? [];
$risk_distribution = $data['risk_distribution'] ?? [];
$recent_activities = $data['recent_activities'] ?? [];
$fakultasName = $data['fakultasName'] ?? null;

// Debug: Periksa data
error_log("Stats: " . print_r($stats, true));
error_log("Risk Distribution: " . print_r($risk_distribution, true));
error_log("Recent Activities: " . print_r($recent_activities, true));
?>
<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/dashboard.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const riskData = <?= json_encode($risk_distribution ?? []) ?>;
    console.log("Risk Distribution Data:", riskData); // Debug

    // Risk Distribution Chart
    const ctx = document.getElementById('riskDistributionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High', 'Very High'],
            datasets: [{
                label: 'Number of Risks',
                data: [
                    riskData.low_count ?? 0,
                    riskData.medium_count ?? 0,
                    riskData.high_count ?? 0,
                    riskData.very_high_count ?? 0
                ],
                backgroundColor: [
                    '#22c55e', // Low - Green
                    '#f59e0b', // Medium - Yellow
                    '#ef4444', // High - Red
                    '#7c3aed'  // Very High - Purple
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
<!-- Risk Summary Cards -->
<div class="cards">
    <div class="card-single">
        <div>
            <h1><?= htmlspecialchars($stats['total_risks'] ?? 0) ?></h1>
            <span>Total Risks</span>
        </div>
        <div>
            <span class="las la-exclamation-triangle"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1><?= htmlspecialchars($stats['high_risks'] ?? 0) ?></h1>
            <span>High Risks</span>
        </div>
        <div>
            <span class="las la-exclamation-circle"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1><?= htmlspecialchars($stats['total_fakultas'] ?? 0) ?></h1>
            <span>Faculty</span>
        </div>
        <div>
            <span class="las la-building"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1><?= htmlspecialchars($stats['active_risks'] ?? 0) ?></h1>
            <span>Active Risks</span>
        </div>
        <div>
            <span class="las la-shield-alt"></span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Risk Level Distribution Chart -->
    <div class="chart-card">
        <div class="card">
            <div class="card-header">
                <h3>Risk Level Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="riskDistributionChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Risk Matrix -->
    <div class="chart-card">
        <div class="card">
            <div class="card-header">
                <h3>Risk Matrix</h3>
            </div>
            <div class="card-body">
                <div class="risk-matrix">
                    <!-- Headers -->
                    <div class="matrix-cell header main-header">
                        <div class="impact-label">Impact ↑</div>
                        <div class="likelihood-label">Likelihood →</div>
                    </div>
                    <div class="matrix-cell header">Very Low</div>
                    <div class="matrix-cell header">Low</div>
                    <div class="matrix-cell header">Medium</div>
                    <div class="matrix-cell header">High</div>
                    <div class="matrix-cell header">Very High</div>
                    
                    <!-- Risk Matrix Cells -->
                    <?php
                    // Impact rows (5 to 1)
                    for ($i = 5; $i >= 1; $i--) {
                        // Row header
                        echo '<div class="matrix-cell header">' . $i . '</div>';
                        
                        // Likelihood columns (1 to 5)
                        for ($j = 1; $j <= 5; $j++) {
                            $score = $i * $j;
                            $level = '';
                            
                            $riskLevel = calculateRiskLevel($j, $i);
                            switch($riskLevel['level']) {
                                case 'Very-High':
                                    $level = '4';
                                    break;
                                case 'High':
                                    $level = '3';
                                    break;
                                case 'Medium':
                                    $level = '2';
                                    break;
                                case 'Low':
                                default:
                                    $level = '1';
                                    break;
                            }
                            
                            echo '<div class="matrix-cell level-' . $level . '">' . $score . '</div>';
                        }
                    }
                    ?>
                    
                    <!-- Footer row with likelihood values -->
                    <div class="matrix-cell header footer-header"></div>
                    <div class="matrix-cell header likelihood-label">Rare</div>
                    <div class="matrix-cell header likelihood-label">Unlikely</div>
                    <div class="matrix-cell header likelihood-label">Moderate</div>
                    <div class="matrix-cell header likelihood-label">Likely</div>
                    <div class="matrix-cell header likelihood-label">Almost Certain</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Table -->
<div class="recent-grid">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>Recent Risk Updates</h3>
                <button onclick="window.location='<?= $_SESSION['base_uri'] ?>/risk-register'">
                    View All <span class="las la-arrow-right"></span>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>Risk Event</td>
                                <td>Treatment Plan</td>
                                <td style="text-align: center";>Risk Level</td>
                                <td>Planned Date</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recent_activities)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($recent_activities as $activity): 
                                // Gunakan nilai asli tanpa mengubahnya
                                $riskLevel = $activity['risk_level_inherent'];
                                
                                // Tentukan label dan class berdasarkan risk_level_inherent
                                switch($riskLevel) {
                                    case 'Very-High':
                                        $level_class = 'very_high';
                                        $label = 'Very High';
                                        break;
                                    case 'High':
                                        $level_class = 'high';
                                        $label = 'High';
                                        break;
                                    case 'Medium':
                                        $level_class = 'medium';
                                        $label = 'Medium';
                                        break;
                                    case 'Low':
                                        $level_class = 'low';
                                        $label = 'Low';
                                        break;
                                    default:
                                        $level_class = 'unknown';
                                        $label = 'Unknown';
                                        break;
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['risk_event']) ?></td>
                                <td><?= htmlspecialchars($activity['rencana_mitigasi'] ?? '-') ?></td>
                                <td style="text-align: center;">
                                    <span class="status <?= strtolower($level_class) ?>">
                                        <?= htmlspecialchars($label) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        if (!empty($activity['triwulan']) && !empty($activity['tahun'])) {
                                            echo 'Triwulan ' . htmlspecialchars($activity['triwulan']) . ' ' . htmlspecialchars($activity['tahun']);
                                        } else {
                                            echo 'Not Planned';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "Dashboard";
$module = "dashboard";
require __DIR__ . '/../../layouts/app.blade.php';
?>
