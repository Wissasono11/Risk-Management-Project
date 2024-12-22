<?php
global $exportData, $exportYear;
?>
<table border="1">
   <thead>
       <tr>
           <th colspan="15" style="text-align: center; font-size: 14pt;">
               RISK MANAGEMENT REPORT <?= $exportYear ?>
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
   <?php foreach($exportData as $i => $risk): ?>
       <?php 
       // misal define calculateRiskLevel, dsb.
       ?>
       <tr>
           <td><?= $i+1 ?></td>
           <td><?= htmlspecialchars($risk['fakultas_nama']) ?></td>
           <td><?= htmlspecialchars($risk['objective']) ?></td>
           <td><?= htmlspecialchars($risk['proses_bisnis']) ?></td>
           <td><?= htmlspecialchars($risk['kategori_nama']) ?></td>
           <td><?= htmlspecialchars($risk['risk_event']) ?></td>
           <td><?= htmlspecialchars($risk['risk_cause']) ?></td>
           <td><?= htmlspecialchars($risk['risk_source']) ?></td>
           <td><?= htmlspecialchars($risk['risk_owner']) ?></td>
           <td><?= $risk['likelihood_inherent'] ?></td>
           <td><?= $risk['impact_inherent'] ?></td>
           <td>
               <?php 
               $lvl = calculateRiskLevel($risk['likelihood_inherent']??0, $risk['impact_inherent']??0);
               echo $lvl['label'];
               ?>
           </td>
           <td><?= htmlspecialchars($risk['rencana_mitigasi']) ?></td>
           <td><?= htmlspecialchars($risk['pic']) ?></td>
           <td><?= htmlspecialchars($risk['evidence_type']) ?></td>
           <td><?= htmlspecialchars($risk['timeline_status']) ?></td>
       </tr>
   <?php endforeach; ?>
   </tbody>
</table>
