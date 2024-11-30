<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
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
        $sql = "INSERT INTO risk_registers (
            fakultas_id, objective, proses_bisnis, kategori_id, 
            risk_event, risk_cause, risk_source, risk_owner,
            likelihood_inherent, impact_inherent, 
            risk_level_inherent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $level = calculateRiskLevel($likelihood, $impact);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ississssiii", 
            $_SESSION['fakultas_id'], $objective, $proses_bisnis, $kategori_id,
            $risk_event, $risk_cause, $risk_source, $risk_owner,
            $likelihood, $impact, $level['level']
        );

        if($stmt->execute()) {
            setAlert('success', 'Risk berhasil ditambahkan');
            header('Location: index.php?module=risk_register');
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Risk</title>
    <link rel="stylesheet" href="../../../assets/css/risk-register-create.css">
</head>
<body>
    <div class="container my-5">
        <div class="header-section">
            <h1 class="title">Add New Risk</h1>
            <a href="index.php?module=risk_register" class="btn-back">Back to List</a>
        </div>

        <form method="POST" action="index.php?module=risk_register&action=store" class="form-risk">
            <div class="form-group">
                <label class="form-label">Objective/Tujuan *</label>
                <textarea name="objective" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Proses Bisnis</label>
                <input type="text" name="proses_bisnis" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Kategori Risiko *</label>
                <select name="kategori_id" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Risk Event *</label>
                <textarea name="risk_event" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Risk Cause/Penyebab</label>
                <textarea name="risk_cause" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Risk Source *</label>
                <select name="risk_source" class="form-control" required>
                    <option value="internal">Internal</option>
                    <option value="external">External</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Risk Owner *</label>
                <input type="text" name="risk_owner" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="form-label">Likelihood *</label>
                    <select name="likelihood" class="form-control" required>
                        <option value="">Pilih Likelihood</option>
                        <option value="1">Rare (1)</option>
                        <option value="2">Unlikely (2)</option>
                        <option value="3">Moderate (3)</option>
                        <option value="4">Likely (4)</option>
                        <option value="5">Almost Certain (5)</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Impact *</label>
                    <select name="impact" class="form-control" required>
                        <option value="">Pilih Impact</option>
                        <option value="1">Insignificant (1)</option>
                        <option value="2">Minor (2)</option>
                        <option value="3">Moderate (3)</option>
                        <option value="4">Major (4)</option>
                        <option value="5">Catastrophic (5)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-save">Save Risk</button>
        </form>
    </div>
</body>
</html>