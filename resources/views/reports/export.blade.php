<?php
global $exportData, $exportYear;
?>
<table border="1" style="border-collapse: collapse; width: 100%;">
   <thead>
       <tr>
           <th colspan="16" style="text-align: center; font-size: 16pt; background-color: #4CAF50; color: white; padding: 10px;">
               RISK MANAGEMENT REPORT <?= htmlspecialchars($exportYear) ?>
           </th>
       </tr>
       <tr style="background-color: #f2f2f2;">
           <th style="padding: 8px; text-align: left;">No</th>
           <th style="padding: 8px; text-align: left;">Faculty</th>
           <th style="padding: 8px; text-align: left;">Objective</th>
           <th style="padding: 8px; text-align: left;">Business Process</th>
           <th style="padding: 8px; text-align: left;">Risk Category</th>
           <th style="padding: 8px; text-align: left;">Risk Event</th>
           <th style="padding: 8px; text-align: left;">Risk Cause</th>
           <th style="padding: 8px; text-align: left;">Risk Source</th>
           <th style="padding: 8px; text-align: left;">Risk Owner</th>
           <th style="padding: 8px; text-align: left;">Likelihood</th>
           <th style="padding: 8px; text-align: left;">Impact</th>
           <th style="padding: 8px; text-align: left;">Risk Level</th>
           <th style="padding: 8px; text-align: left;">Mitigation Plan</th>
           <th style="padding: 8px; text-align: left;">PIC</th>
           <th style="padding: 8px; text-align: left;">Evidence</th>
           <th style="padding: 8px; text-align: left;">Timeline Status</th>
       </tr>
   </thead>
   <tbody>
   <?php foreach($exportData as $i => $risk): ?>
       <?php 
       // Menggunakan fungsi calculateRiskLevel untuk memformat level risiko
       $lvl = calculateRiskLevel($risk['likelihood_inherent'] ?? 0, $risk['impact_inherent'] ?? 0);
       ?>
       <tr>
            <td><?= htmlspecialchars($risk['fakultas_nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['objective'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['proses_bisnis'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['kategori_nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['risk_event'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['risk_cause'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['risk_source'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['risk_owner'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $risk['likelihood_inherent'] ?? '' ?></td>
            <td><?= $risk['impact_inherent'] ?? '' ?></td>
            <td>
                <?php 
                $lvl = calculateRiskLevel($risk['likelihood_inherent'] ?? 0, $risk['impact_inherent'] ?? 0);
                echo htmlspecialchars($lvl['label'], ENT_QUOTES, 'UTF-8');
                ?>
            </td>
            <td><?= htmlspecialchars($risk['rencana_mitigasi'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['pic'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['evidence_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($risk['timeline_status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
       </tr>
   <?php endforeach; ?>
   </tbody>
</table>
