<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

$id = (int)$_GET['id'];

// Get treatment data
$sql = "SELECT t.*, r.id as risk_id, r.risk_event 
        FROM risk_treatments t
        JOIN risk_registers r ON t.risk_register_id = r.id
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$treatment = $stmt->get_result()->fetch_assoc();

if (!$treatment || !canAccessRisk($treatment['risk_id'])) {
    setAlert('error', 'Access denied or treatment not found');
    header('Location: index.php');
    exit();
}

// Get existing timeline
$sql = "SELECT * FROM mitigation_timeline WHERE treatment_id = ? ORDER BY tahun, triwulan";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$timelines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rencana_mitigasi = cleanInput($_POST['rencana_mitigasi']);
    $pic = cleanInput($_POST['pic']);
    $evidence_type = cleanInput($_POST['evidence_type']);

    try {
        // Update treatment
        $sql = "UPDATE risk_treatments SET 
                rencana_mitigasi = ?, 
                pic = ?, 
                evidence_type = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $rencana_mitigasi, $pic, $evidence_type, $id);
        
        if ($stmt->execute()) {
            // Update timeline status
            foreach ($_POST['timeline'] as $timeline_id => $status) {
                $sql = "UPDATE mitigation_timeline SET realisasi = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $realisasi = isset($status['realisasi']) ? 1 : 0;
                $stmt->bind_param("ii", $realisasi, $timeline_id);
                $stmt->execute();
            }

            setAlert('success', 'Treatment berhasil diupdate');
            header('Location: ../risk_register/view.php?id=' . $treatment['risk_id']);
            exit();
        }
    } catch (Exception $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}
?>

<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Edit Treatment</h1>
        <a href="../risk_register/view.php?id=<?= $treatment['risk_id'] ?>" class="btn btn-secondary">Back</a>
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
                        <td><?= htmlspecialchars($treatment['risk_event']) ?></td>
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
                        <textarea name="rencana_mitigasi" class="form-control" required rows="3"><?= htmlspecialchars($treatment['rencana_mitigasi']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>PIC/Penanggung Jawab *</label>
                        <input type="text" name="pic" class="form-control" required value="<?= htmlspecialchars($treatment['pic']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Evidence Type *</label>
                        <input type="text" name="evidence_type" class="form-control" required 
                               value="<?= htmlspecialchars($treatment['evidence_type']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Timeline & Realisasi</label>
                        <div class="timeline-grid">
                            <?php foreach($timelines as $timeline): ?>
                                <div class="timeline-item">
                                    <div class="timeline-header">
                                        Triwulan <?= $timeline['triwulan'] ?> - <?= $timeline['tahun'] ?>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   name="timeline[<?= $timeline['id'] ?>][realisasi]" 
                                                   value="1" 
                                                   <?= $timeline['realisasi'] ? 'checked' : '' ?>
                                                   class="custom-control-input" 
                                                   id="realisasi<?= $timeline['id'] ?>">
                                            <label class="custom-control-label" for="realisasi<?= $timeline['id'] ?>">
                                                Realisasi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Treatment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>