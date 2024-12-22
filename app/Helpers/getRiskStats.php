<?php
function getRiskStats($conn, $user_role = null, $fakultas_id = null) {
    $stats = [
        'high_risks' => 0,
        'last_update' => null
    ];

    try {
        // Build fakultas condition
        $fakultasCondition = "";
        if ($user_role === 'fakultas' && $fakultas_id) {
            $fakultasCondition = " AND fakultas_id = " . (int)$fakultas_id;
        }

        // Get high risks count
        $query = "SELECT COUNT(*) as count 
                 FROM risk_registers 
                 WHERE (likelihood_inherent * impact_inherent >= 10 
                    OR (impact_inherent = 5 AND likelihood_inherent = 1)
                    OR (impact_inherent = 1 AND likelihood_inherent = 5))" 
                 . $fakultasCondition;
        
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['high_risks'] = (int)$row['count'];
        }

        // Get last update
        $query = "SELECT MAX(updated_at) as last_update 
                 FROM risk_registers 
                 WHERE updated_at IS NOT NULL" . $fakultasCondition;
        
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['last_update'] = $row['last_update'];
        }

        return $stats;
    } catch (\Exception $e) {
        error_log("Error getting risk stats: " . $e->getMessage());
        return $stats;
    }
}