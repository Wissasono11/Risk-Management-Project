<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Get risk data
$id = (int)$_GET['id'];
$risk = getRiskById($id);

// Check permission
if (!$risk || !canAccessRisk($id)) {
    setAlert('error', 'Access denied or risk not found');
    header('Location: index.php');
    exit();
}

// Get treatments/mitigasi
$treatments = getRiskTreatments($id);
?>

<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Risk Detail</h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?id=<?= $id ?>" class="btn btn-warning">Edit Risk</a>
        </div>
    </div>

    <div class="content">
        <!-- Risk Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Risk Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr>
                        <th width="200">Fakultas</th>
                        <td><?= htmlspecialchars($risk['fakultas_nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Objective/Tujuan</th>
                        <td><?= htmlspecialchars($risk['objective']) ?></td>
                    </tr>
                    <tr>
                        <th>Proses Bisnis</th>
                        <td><?= htmlspecialchars($risk['proses_bisnis']) ?></td>
                    </tr>
                    <tr>
                        <th>Kategori Risiko</th>
                        <td><?= htmlspecialchars($risk['kategori_nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Risk Event</th>
                        <td><?= htmlspecialchars($risk['risk_event']) ?></td>
                    </tr>
                    <tr>
                        <th>Risk Cause</th>
                        <td><?= htmlspecialchars($risk['risk_cause']) ?></td>
                    </tr>
                    <tr>
                        <th>Risk Source</th>
                        <td><?= ucfirst($risk['risk_source']) ?></td>
                    </tr>
                    <tr>
                        <th>Risk Owner</th>
                        <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Risk Assessment</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Inherent Risk -->
                    <div class="col-md-6">
                        <h4>Inherent Risk</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Likelihood</th>
                                <td><?= $risk['likelihood_inherent'] ?> - <?= getLikelihoodLabel($risk['likelihood_inherent']) ?></td>
                            </tr>
                            <tr>
                                <th>Impact</th>
                                <td><?= $risk['impact_inherent'] ?> - <?= getImpactLabel($risk['impact_inherent']) ?></td>
                            </tr>
                            <tr>
                                <th>Risk Level</th>
                                <td>
                                    <?php 
                                    $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
                                    ?>
                                    <span class="badge badge-<?= $level['color'] ?>"><?= $level['label'] ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Residual Risk -->
                    <div class="col-md-6">
                        <h4>Residual Risk</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Likelihood</th>
                                <td><?= $risk['likelihood_residual'] ?> - <?= getLikelihoodLabel($risk['likelihood_residual']) ?></td>
                            </tr>
                            <tr>
                                <th>Impact</th>
                                <td><?= $risk['impact_residual'] ?> - <?= getImpactLabel($risk['impact_residual']) ?></td>
                            </tr>
                            <tr>
                                <th>Risk Level</th>
                                <td>
                                    <?php 
                                    $level = calculateRiskLevel($risk['likelihood_residual'], $risk['impact_residual']);
                                    ?>
                                    <span class="badge badge-<?= $level['color'] ?>"><?= $level['label'] ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mitigasi/Treatments -->
        <div class="card">
            <div class="card-header">
                <h3>Risk Treatments</h3>
                <a href="../treatments/create.php?risk_id=<?= $id ?>" class="btn btn-primary btn-sm">Add Treatment</a>
            </div>
            <div class="card-body">
                <?php if(empty($treatments)): ?>
                    <p class="text-muted">No treatments found</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Rencana Mitigasi</th>
                                <th>PIC</th>
                                <th>Evidence</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($treatments as $i => $treatment): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></td>
                                    <td><?= htmlspecialchars($treatment['pic']) ?></td>
                                    <td><?= htmlspecialchars($treatment['evidence_type']) ?></td>
                                    <td>
                                        <?php
                                        $status = getRealisasiStatus($treatment);
                                        echo "<span class='badge badge-{$status['color']}'>{$status['label']}</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <a href="../treatments/edit.php?id=<?= $treatment['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>