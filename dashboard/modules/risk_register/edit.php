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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $objective = cleanInput($_POST['objective']);
    $proses_bisnis = cleanInput($_POST['proses_bisnis']);
    $kategori_id = (int)$_POST['kategori_id'];
    $risk_event = cleanInput($_POST['risk_event']);
    $risk_cause = cleanInput($_POST['risk_cause']);
    $risk_source = cleanInput($_POST['risk_source']);
    $risk_owner = cleanInput($_POST['risk_owner']);
    $likelihood = (int)$_POST['likelihood'];
    $impact = (int)$_POST['impact'];

    try {
        $sql = "UPDATE risk_registers SET 
                objective = ?, 
                proses_bisnis = ?,
                kategori_id = ?,
                risk_event = ?,
                risk_cause = ?,
                risk_source = ?,
                risk_owner = ?,
                likelihood_inherent = ?,
                impact_inherent = ?,
                risk_level_inherent = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $level = calculateRiskLevel($likelihood, $impact);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssississssi", 
            $objective, $proses_bisnis, $kategori_id,
            $risk_event, $risk_cause, $risk_source, $risk_owner,
            $likelihood, $impact, $level['level'], $id
        );

        if($stmt->execute()) {
            setAlert('success', 'Risk berhasil diupdate');
            header('Location: index.php');
            exit();
        }
    } catch (Exception $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Get kategori risiko untuk dropdown
$sql = "SELECT * FROM risk_categories";
$categories = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="header">
        <h1>Edit Risk</h1>
        <a href="index.php" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="content">
        <form method="POST" class="form-risk">
            <div class="form-group">
                <label>Objective/Tujuan *</label>
                <textarea name="objective" class="form-control" required><?= htmlspecialchars($risk['objective']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Proses Bisnis</label>
                <input type="text" name="proses_bisnis" class="form-control" value="<?= htmlspecialchars($risk['proses_bisnis']) ?>">
            </div>

            <div class="form-group">
                <label>Kategori Risiko *</label>
                <select name="kategori_id" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $risk['kategori_id'] ? 'selected' : '' ?>>
                            <?= $cat['nama'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Risk Event *</label>
                <textarea name="risk_event" class="form-control" required><?= htmlspecialchars($risk['risk_event']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Risk Cause/Penyebab</label>
                <textarea name="risk_cause" class="form-control"><?= htmlspecialchars($risk['risk_cause']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Risk Source *</label>
                <select name="risk_source" class="form-control" required>
                    <option value="internal" <?= $risk['risk_source'] == 'internal' ? 'selected' : '' ?>>Internal</option>
                    <option value="external" <?= $risk['risk_source'] == 'external' ? 'selected' : '' ?>>External</option>
                </select>
            </div>

            <div class="form-group">
                <label>Risk Owner *</label>
                <input type="text" name="risk_owner" class="form-control" required value="<?= htmlspecialchars($risk['risk_owner']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Likelihood *</label>
                    <select name="likelihood" class="form-control" required>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $risk['likelihood_inherent'] == $i ? 'selected' : '' ?>>
                                <?= getLikelihoodLabel($i) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>Impact *</label>
                    <select name="impact" class="form-control" required>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $risk['impact_inherent'] == $i ? 'selected' : '' ?>>
                                <?= getImpactLabel($i) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Risk</button>
        </form>
    </div>
</div>
