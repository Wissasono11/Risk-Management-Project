<?php
require_once '../../config/database.php';
require_once '../../middleware/auth.php';
require_once '../../includes/functions.php';
checkAuth();

$risk_id = isset($_GET['risk_id']) ? (int)$_GET['risk_id'] : 0;

// Check if risk exists and user has permission
if (!$risk_id || !canAccessRisk($risk_id)) {
    setAlert('error', 'Invalid risk or access denied');
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rencana_mitigasi = cleanInput($_POST['rencana_mitigasi']);
    $pic = cleanInput($_POST['pic']);
    $evidence_type = cleanInput($_POST['evidence_type']);

    try {
        // Insert treatment
        $sql = "INSERT INTO risk_treatments (risk_register_id, rencana_mitigasi, pic, evidence_type) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $risk_id, $rencana_mitigasi, $pic, $evidence_type);
        
        if ($stmt->execute()) {
            $treatment_id = $conn->insert_id;

            // Insert timeline
            foreach ($_POST['timeline'] as $quarter => $value) {
                if ($value == 1) {  // Jika quarter dipilih
                    $tahun = date('Y');
                    $sql = "INSERT INTO mitigation_timeline (treatment_id, tahun, triwulan) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $treatment_id, $tahun, $quarter);
                    $stmt->execute();
                }
            }

            setAlert('success', 'Treatment berhasil ditambahkan');
            header('Location: ../risk_register/view.php?id=' . $risk_id);
            exit();
        }
    } catch (Exception $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Get risk details
$risk = getRiskById($risk_id);
?>

<?php include '../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Add Treatment</h1>
        <a href="../risk_register/view.php?id=<?= $risk_id ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="content">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Risk Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr>
                        <th width="200">Risk Event</th>
                        <td><?= htmlspecialchars($risk['risk_event']) ?></td>
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
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Treatment Details</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Rencana Mitigasi *</label>
                        <textarea name="rencana_mitigasi" class="form-control" required rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>PIC/Penanggung Jawab *</label>
                        <input type="text" name="pic" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Evidence Type *</label>
                        <input type="text" name="evidence_type" class="form-control" required 
                               placeholder="Contoh: Notulen Rapat, SOP, Dokumentasi, dll">
                    </div>

                    <div class="form-group">
                        <label>Timeline (Tahun <?= date('Y') ?>)</label>
                        <div class="timeline-checkboxes">
                            <?php for($i = 1; $i <= 4; $i++): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="timeline[<?= $i ?>]" value="1" 
                                           class="custom-control-input" id="quarter<?= $i ?>">
                                    <label class="custom-control-label" for="quarter<?= $i ?>">
                                        Triwulan <?= $i ?>
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Treatment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>