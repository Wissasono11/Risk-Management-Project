<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Ambil parameter filter
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$fakultas_id = isset($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : null;

// Query untuk mengambil data
$sql = "SELECT 
            f.nama as fakultas_nama,
            COUNT(r.id) as total_risks,
            SUM(CASE WHEN r.risk_level_inherent IN ('T', 'ST') THEN 1 ELSE 0 END) as high_risks,
            COUNT(t.id) as total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
        FROM fakultas f
        LEFT JOIN risk_registers r ON f.id = r.fakultas_id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?";

if ($fakultas_id) {
    $sql .= " WHERE f.id = ?";
}

$sql .= " GROUP BY f.id, f.nama";

$stmt = $conn->prepare($sql);
if ($fakultas_id) {
    $stmt->bind_param("ii", $tahun, $fakultas_id);
} else {
    $stmt->bind_param("i", $tahun);
}

$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Header untuk download file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"export_risk_management_" . $tahun . ".xls\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output data dalam format tabel HTML
echo "<table border='1'>";
echo "<thead>
        <tr>
            <th>Fakultas</th>
            <th>Total Risks</th>
            <th>High Risks</th>
            <th>Total Treatments</th>
            <th>Completed Treatments</th>
        </tr>
      </thead>";
echo "<tbody>";
foreach ($data as $row) {
    echo "<tr>
            <td>" . htmlspecialchars($row['fakultas_nama']) . "</td>
            <td>" . $row['total_risks'] . "</td>
            <td>" . $row['high_risks'] . "</td>
            <td>" . $row['total_treatments'] . "</td>
            <td>" . $row['completed_treatments'] . "</td>
          </tr>";
}
echo "</tbody>";
echo "</table>";
