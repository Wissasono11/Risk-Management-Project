<?php
// resources/views/dashboard/index.blade.php
// Kita asumsikan variable $stats, $risk_distribution, $recent_activities, dsb.
// disiapkan di DashboardController@index()

// Start output buffering:
ob_start();
?>
<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/dashboard.css">
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/dashboard-chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi chart
    initializeCharts(<?= json_encode($risk_distribution ?? []) ?>);
});
</script>

<!-- Risk Summary Cards -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<div class="cards">
    <div class="card-single">
        <div>
            <h1><?= $stats['total_risks'] ?? 0 ?></h1>
            <span>Total Risks</span>
        </div>
        <div>
            <span class="las la-exclamation-triangle"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1><?= $stats['high_risks'] ?? 0 ?></h1>
            <span>High Risks</span>
        </div>
        <div>
            <span class="las la-exclamation-circle"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1><?= $stats['total_fakultas'] ?? 0 ?></h1>
            <span>Departments</span>
        </div>
        <div>
            <span class="las la-building"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>
                <?php 
                // array_sum(array_values($risk_distribution)) 
                // boleh dicek total
                $total = is_array($risk_distribution ?? null)
                         ? array_sum($risk_distribution)
                         : 0;
                echo $total;
                ?>
            </h1>
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
    
    <!-- Likelihood vs Impact Matrix -->
    <div class="chart-card">
        <div class="card">
            <div class="card-header">
                <h3>Risk Matrix</h3>
            </div>
            <div class="card-body">
                <div class="risk-matrix">
                    <div class="matrix-cell header">Impact →</div>
                    <div class="matrix-cell header">1</div>
                    <div class="matrix-cell header">2</div>
                    <div class="matrix-cell header">3</div>
                    <div class="matrix-cell header">4</div>
                    <div class="matrix-cell header">5</div>
                    
                    <div class="matrix-cell header">5 ↑</div>
                    <div class="matrix-cell medium">5</div>
                    <div class="matrix-cell high">10</div>
                    <div class="matrix-cell high">15</div>
                    <div class="matrix-cell extreme">20</div>
                    <div class="matrix-cell extreme">25</div>
                    
                    <div class="matrix-cell header">4</div>
                    <div class="matrix-cell low">4</div>
                    <div class="matrix-cell medium">8</div>
                    <div class="matrix-cell high">12</div>
                    <div class="matrix-cell high">16</div>
                    <div class="matrix-cell extreme">20</div>
                    
                    <div class="matrix-cell header">3</div>
                    <div class="matrix-cell low">3</div>
                    <div class="matrix-cell medium">6</div>
                    <div class="matrix-cell medium">9</div>
                    <div class="matrix-cell high">12</div>
                    <div class="matrix-cell high">15</div>
                    
                    <div class="matrix-cell header">2</div>
                    <div class="matrix-cell low">2</div>
                    <div class="matrix-cell low">4</div>
                    <div class="matrix-cell medium">6</div>
                    <div class="matrix-cell medium">8</div>
                    <div class="matrix-cell high">10</div>
                    
                    <div class="matrix-cell header">1</div>
                    <div class="matrix-cell low">1</div>
                    <div class="matrix-cell low">2</div>
                    <div class="matrix-cell low">3</div>
                    <div class="matrix-cell low">4</div>
                    <div class="matrix-cell medium">5</div>
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
                                <td>Risk Level</td>
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
                                $risk_level = calculateRiskLevel(
                                    $activity['likelihood_inherent'] ?? 0,
                                    $activity['impact_inherent'] ?? 0
                                );
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['risk_event']) ?></td>
                                <td><?= htmlspecialchars($activity['rencana_mitigasi'] ?? '-') ?></td>
                                <td>
                                    <span class="status <?= strtolower($risk_level['level']) ?>">
                                        <?= $risk_level['label'] ?>
                                    </span>
                                </td>
                                <td>Triwulan <?= $activity['triwulan'] ?? '-' ?> <?= $activity['tahun'] ?? '' ?></td>
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
$module    = "dashboard"; // Agar menu 'Dashboard' di layout jadi active
require __DIR__ . '/../../layouts/app.blade.php';
