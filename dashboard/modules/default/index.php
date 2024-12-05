<?php
// Get statistics
$stats = getDashboardStats($_SESSION['fakultas_id']);

// Get risk level distribution based on likelihood and impact
$sql = "SELECT 
    SUM(CASE 
        WHEN likelihood_inherent * impact_inherent <= 4 THEN 1 
        ELSE 0 END) as sr_count,
    SUM(CASE 
        WHEN likelihood_inherent * impact_inherent > 4 AND likelihood_inherent * impact_inherent <= 8 THEN 1 
        ELSE 0 END) as r_count,
    SUM(CASE 
        WHEN likelihood_inherent * impact_inherent > 8 AND likelihood_inherent * impact_inherent <= 12 THEN 1 
        ELSE 0 END) as s_count,
    SUM(CASE 
        WHEN likelihood_inherent * impact_inherent > 12 AND likelihood_inherent * impact_inherent <= 16 THEN 1 
        ELSE 0 END) as t_count,
    SUM(CASE 
        WHEN likelihood_inherent * impact_inherent > 16 THEN 1 
        ELSE 0 END) as st_count
FROM risk_registers";

if ($_SESSION['fakultas_id']) {
    $sql .= " WHERE fakultas_id = " . $_SESSION['fakultas_id'];
}
$risk_distribution = $conn->query($sql)->fetch_assoc();

// Get recent risks
$sql = "SELECT r.risk_event, r.likelihood_inherent, r.impact_inherent, 
        t.rencana_mitigasi, mt.triwulan, mt.tahun 
        FROM risk_registers r
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id
        WHERE 1=1 ";

if ($_SESSION['fakultas_id']) {
    $sql .= " AND r.fakultas_id = " . $_SESSION['fakultas_id'];
}

$sql .= " ORDER BY r.created_at DESC LIMIT 5";

// Debug query
echo "<!-- Query: $sql -->"; // Untuk melihat query yang dijalankan

// Execute query with error handling
$result = $conn->query($sql);
if ($result === false) {
    // Log error
    error_log("Query error: " . $conn->error);
    $recent_activities = [];
} else {
    $recent_activities = $result->fetch_all(MYSQLI_ASSOC);
}

// Debug output
echo "<!-- Result count: " . count($recent_activities) . " -->"; // Untuk melihat jumlah hasil
?>

<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<link rel="stylesheet" href="../../../assets/css/default.css">
<script src="../../../assets/js/dashboard-chart.js"></script>

<!-- Risk Summary Cards -->
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
            <h1><?= array_sum(array_values($risk_distribution)) ?></h1>
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
                <button onclick="window.location='index.php?module=risk_register'">
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
            $risk_level = calculateRiskLevel($activity['likelihood_inherent'], $activity['impact_inherent']);
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