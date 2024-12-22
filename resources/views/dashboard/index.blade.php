<?php
// resources/views/dashboard/index.blade.php

// Start output buffering:
ob_start();
?>
<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<link rel="stylesheet" href="<?= $_SESSION['base_uri'] ?>/assets/css/dashboard.css">
<script src="<?= $_SESSION['base_uri'] ?>/assets/js/dashboard-chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const riskData = <?= json_encode($risk_distribution ?? []) ?>;
    console.log('Risk Distribution Data:', riskData); // Debugging
    initializeCharts(riskData);
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
            <span>Faculty</span>
        </div>
        <div>
            <span class="las la-building"></span>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>
                <?php 
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
                    
                    <!-- Row 5 -->
                    <div class="matrix-cell header">5</div>
                    <?php
                    $cells5 = [
                        ['level' => 3, 'text' => '5'],
                        ['level' => 3, 'text' => '10'],
                        ['level' => 3, 'text' => '15'],
                        ['level' => 4, 'text' => '20'],
                        ['level' => 4, 'text' => '25']
                    ];
                    foreach($cells5 as $cell): ?>
                        <div class="matrix-cell level-<?= $cell['level'] ?>"><?= $cell['text'] ?></div>
                    <?php endforeach; ?>

                    <!-- Row 4 -->
                    <div class="matrix-cell header">4</div>
                    <?php
                    $cells4 = [
                        ['level' => 1, 'text' => '4'],
                        ['level' => 2, 'text' => '8'],
                        ['level' => 3, 'text' => '12'],
                        ['level' => 4, 'text' => '16'],
                        ['level' => 4, 'text' => '20']
                    ];
                    foreach($cells4 as $cell): ?>
                        <div class="matrix-cell level-<?= $cell['level'] ?>"><?= $cell['text'] ?></div>
                    <?php endforeach; ?>

                    <!-- Row 3 -->
                    <div class="matrix-cell header">3</div>
                    <?php
                    $cells3 = [
                        ['level' => 1, 'text' => '3'],
                        ['level' => 2, 'text' => '6'],
                        ['level' => 2, 'text' => '9'],
                        ['level' => 3, 'text' => '12'],
                        ['level' => 3, 'text' => '15']
                    ];
                    foreach($cells3 as $cell): ?>
                        <div class="matrix-cell level-<?= $cell['level'] ?>"><?= $cell['text'] ?></div>
                    <?php endforeach; ?>

                    <!-- Row 2 -->
                    <div class="matrix-cell header">2</div>
                    <?php
                    $cells2 = [
                        ['level' => 1, 'text' => '2'],
                        ['level' => 1, 'text' => '4'],
                        ['level' => 2, 'text' => '6'],
                        ['level' => 2, 'text' => '8'],
                        ['level' => 3, 'text' => '10']
                    ];
                    foreach($cells2 as $cell): ?>
                        <div class="matrix-cell level-<?= $cell['level'] ?>"><?= $cell['text'] ?></div>
                    <?php endforeach; ?>

                    <!-- Row 1 -->
                    <div class="matrix-cell header">1</div>
                    <?php
                    $cells1 = [
                        ['level' => 1, 'text' => '1'],
                        ['level' => 1, 'text' => '2'],
                        ['level' => 1, 'text' => '3'],
                        ['level' => 1, 'text' => '4'],
                        ['level' => 3, 'text' => '5']
                    ];
                    foreach($cells1 as $cell): ?>
                        <div class="matrix-cell level-<?= $cell['level'] ?>"><?= $cell['text'] ?></div>
                    <?php endforeach; ?>
                    <div class="matrix-cell header footer-header">
                    </div>
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
?>
