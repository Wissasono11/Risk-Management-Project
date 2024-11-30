<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

// Parameters
$fakultas_id = isset($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : null;
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="risk_report_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');

// Query untuk data
$sql = "SELECT 
           f.nama as fakultas_nama,
           r.objective,
           r.proses_bisnis,
           rc.nama as kategori_nama,
           r.risk_event,
           r.risk_cause,
           r.risk_source,
           r.risk_owner,
           r.likelihood_inherent,
           r.impact_inherent,
           r.risk_level_inherent,
           t.rencana_mitigasi,
           t.pic,
           t.evidence_type,
           GROUP_CONCAT(
               CONCAT('Q', mt.triwulan, ': ', 
               CASE WHEN mt.realisasi = 1 THEN 'Done' ELSE 'Pending' END)
               ORDER BY mt.triwulan
               SEPARATOR ' | '
           ) as timeline_status
       FROM risk_registers r
       JOIN fakultas f ON r.fakultas_id = f.id
       JOIN risk_categories rc ON r.kategori_id = rc.id
       LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
       LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
       WHERE 1=1";

if ($fakultas_id) {
   $sql .= " AND f.id = ?";
}

$sql .= " GROUP BY r.id, t.id
         ORDER BY f.nama, r.created_at";

$stmt = $conn->prepare($sql);
if ($fakultas_id) {
   $stmt->bind_param("ii", $tahun, $fakultas_id);
} else {
   $stmt->bind_param("i", $tahun);
}
$stmt->execute();
$risks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<table border="1">
   <thead>
       <tr>
           <th colspan="15" style="text-align: center; font-size: 14pt;">
               RISK MANAGEMENT REPORT <?= $tahun ?>
           </th>
       </tr>
       <tr>
           <th>No</th>
           <th>Fakultas</th>
           <th>Objective</th>
           <th>Proses Bisnis</th>
           <th>Kategori Risiko</th>
           <th>Risk Event</th>
           <th>Risk Cause</th>
           <th>Risk Source</th>
           <th>Risk Owner</th>
           <th>Likelihood</th>
           <th>Impact</th>
           <th>Risk Level</th>
           <th>Rencana Mitigasi</th>
           <th>PIC</th>
           <th>Evidence</th>
           <th>Timeline Status</th>
       </tr>
   </thead>
   <tbody>
       <?php foreach($risks as $i => $risk): ?>
           <tr>
               <td><?= $i + 1 ?></td>
               <td><?= $risk['fakultas_nama'] ?></td>
               <td><?= $risk['objective'] ?></td>
               <td><?= $risk['proses_bisnis'] ?></td>
               <td><?= $risk['kategori_nama'] ?></td>
               <td><?= $risk['risk_event'] ?></td>
               <td><?= $risk['risk_cause'] ?></td>
               <td><?= $risk['risk_source'] ?></td>
               <td><?= $risk['risk_owner'] ?></td>
               <td><?= $risk['likelihood_inherent'] ?></td>
               <td><?= $risk['impact_inherent'] ?></td>
               <td>
                   <?php 
                   $level = calculateRiskLevel($risk['likelihood_inherent'], $risk['impact_inherent']);
                   echo $level['label'];
                   ?>
               </td>
               <td><?= $risk['rencana_mitigasi'] ?></td>
               <td><?= $risk['pic'] ?></td>
               <td><?= $risk['evidence_type'] ?></td>
               <td><?= $risk['timeline_status'] ?></td>
           </tr>
       <?php endforeach; ?>
   </tbody>
</table>