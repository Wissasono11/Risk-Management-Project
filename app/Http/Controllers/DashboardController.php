<?php
namespace App\Http\Controllers;

use Config\Database;

class DashboardController {
    private $conn;
    private $user_id;
    private $user_role;
    private $fakultas_id;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->user_id = $_SESSION['user_id'] ?? null;
        $this->user_role = $_SESSION['user_role'] ?? null;
        $this->fakultas_id = $_SESSION['fakultas_id'] ?? null;
        require_once dirname(__DIR__, 2) . '/Helpers/calculateRiskLevel.php';
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit;
        }

        $stats = $this->getDashboardStats();
        $risk_distribution = $this->getRiskDistribution();
        $recent_activities = $this->getRecentActivities();

        $fakultasName = null;
        if ($this->user_role === 'fakultas' && $this->fakultas_id) {
            $stmt = $this->conn->prepare("SELECT nama FROM fakultas WHERE id = ?");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                die("Internal Server Error");
            }
            $stmt->bind_param("i", $this->fakultas_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($row = $result->fetch_assoc()) {
                $fakultasName = $row['nama'];
            }
            $stmt->close();
        }

        // Siapkan data untuk view
        $data = [
            'stats' => $stats,
            'risk_distribution' => $risk_distribution,
            'recent_activities' => $recent_activities,
            'fakultasName' => $fakultasName
        ];

        // Render view
        require __DIR__ . '/../../../resources/views/dashboard/index.blade.php';
    }

    private function getDashboardStats() {
        $stats = ['total_risks' => 0, 'high_risks' => 0, 'total_fakultas' => 0, 'active_risks' => 0];
        
        try {
            // Build fakultas condition
            $whereClause = "WHERE 1=1";
            if ($this->user_role === 'fakultas' && $this->fakultas_id) {
                $whereClause .= " AND fakultas_id = " . (int)$this->fakultas_id;
            }

            // Total risks
            $query = "SELECT COUNT(*) as count FROM risk_registers " . $whereClause;
            if ($result = $this->conn->query($query)) {
                $row = $result->fetch_assoc();
                $stats['total_risks'] = (int)$row['count'];
            }

            // High & Very High risks
            $whereClauseHigh = $whereClause . " AND risk_level_inherent IN ('High', 'Very-High')";
            $query = "SELECT COUNT(*) as count FROM risk_registers " . $whereClauseHigh;
            if ($result = $this->conn->query($query)) {
                $row = $result->fetch_assoc();
                $stats['high_risks'] = (int)$row['count'];
            }

            // Total fakultas (always show all)
            $query = "SELECT COUNT(*) as count FROM fakultas";
            if ($result = $this->conn->query($query)) {
                $row = $result->fetch_assoc();
                $stats['total_fakultas'] = (int)$row['count'];
            }

            // Active Risks
            $query = "SELECT COUNT(DISTINCT r.id) as count 
                     FROM risk_registers r
                     JOIN risk_treatments t ON r.id = t.risk_register_id " . 
                     $whereClause;
            if ($result = $this->conn->query($query)) {
                $row = $result->fetch_assoc();
                $stats['active_risks'] = (int)$row['count'];
            }

        } catch (\Exception $e) {
            error_log("Error in getDashboardStats: " . $e->getMessage());
        }

        return $stats;
    }

    private function getRiskDistribution() {
        $distribution = [
            'low_count' => 0,
            'medium_count' => 0,
            'high_count' => 0,
            'very_high_count' => 0
        ];

        try {
            $whereClause = "WHERE 1=1";
            if ($this->user_role === 'fakultas' && $this->fakultas_id) {
                $whereClause .= " AND fakultas_id = " . (int)$this->fakultas_id;
            }

            $query = "SELECT likelihood_inherent, impact_inherent
                     FROM risk_registers " . $whereClause;

            if ($result = $this->conn->query($query)) {
                while ($row = $result->fetch_assoc()) {
                    $riskLevel = calculateRiskLevel(
                        (int)$row['likelihood_inherent'],
                        (int)$row['impact_inherent']
                    );
                    
                    switch($riskLevel['level']) {
                        case 'Very-High':
                            $distribution['very_high_count']++;
                            break;
                        case 'High':
                            $distribution['high_count']++;
                            break;
                        case 'Medium':
                            $distribution['medium_count']++;
                            break;
                        case 'Low':
                            $distribution['low_count']++;
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Error in getRiskDistribution: " . $e->getMessage());
        }

        return $distribution;
    }

    private function getRecentActivities() {
        $fakultasCondition = "";
        if ($this->user_role === 'fakultas' && $this->fakultas_id) {
            $fakultasCondition = " AND r.fakultas_id = " . (int)$this->fakultas_id;
        }

        $query = "SELECT 
                    r.id,
                    r.risk_event,
                    r.likelihood_inherent,
                    r.impact_inherent,
                    r.risk_level_inherent,
                    t.rencana_mitigasi,
                    mt.triwulan,
                    mt.tahun,
                    rc.nama as kategori_nama,
                    f.nama as fakultas_nama,
                    COALESCE(r.updated_at, r.created_at) as last_update
                 FROM risk_registers r
                 LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
                 LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id
                 LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
                 LEFT JOIN fakultas f ON r.fakultas_id = f.id
                 WHERE 1=1" . $fakultasCondition . "
                 ORDER BY last_update DESC
                 LIMIT 5";

        $result = $this->conn->query($query);
        if (!$result) {
            error_log("Recent Activities Query Error: " . $this->conn->error);
            return [];
        }

        $activities = [];
        while ($row = $result->fetch_assoc()) {
            $activities[] = $row;
        }

        return $activities;
    }
}
