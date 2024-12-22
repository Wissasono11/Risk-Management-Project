<?php
namespace App\Services;

use App\Interfaces\ReportsInterface;
use Config\Database;
use mysqli_sql_exception;

class ReportsService implements ReportsInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        require_once dirname(__DIR__) . '/Helpers/calculateRiskLevel.php';
    }

    public function getSummary($tahun, $fakultasId = null)
    {
        $sql = "SELECT 
            f.id AS fakultas_id,
            f.nama AS fakultas_nama,
            COUNT(DISTINCT r.id) AS total_risks,
            COUNT(DISTINCT CASE 
                WHEN r.risk_level_inherent IN ('High', 'Very-High') THEN r.id
                ELSE NULL
            END) AS high_risks,
            COUNT(DISTINCT t.id) AS total_treatments,
            COUNT(DISTINCT CASE 
                WHEN mt.realisasi = '1' THEN t.id
                ELSE NULL
            END) AS completed_treatments
        FROM fakultas f
        LEFT JOIN risk_registers r ON f.id = r.fakultas_id AND YEAR(r.created_at) = ?
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id
        WHERE 1=1";
    
        $params = [$tahun];
        $types  = 'i';
    
        if ($fakultasId) {
            $sql .= " AND f.id = ?";
            $params[] = $fakultasId;
            $types .= 'i';
        }
    
        $sql .= " GROUP BY f.id, f.nama ORDER BY f.nama";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . $this->conn->error);
        }
    
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    
        return $stmt->get_result()->fetch_all(\MYSQLI_ASSOC);
    }
    
    
    
    

    public function getFaculties()
    {
        $sql = "SELECT id, nama FROM fakultas ORDER BY nama";
        $res = $this->conn->query($sql);
        return $res->fetch_all(\MYSQLI_ASSOC);
    }

    public function getDetailData($fakultasId, $tahun)
    {
        $sql = "SELECT 
            r.id,
            r.fakultas_id,
            r.objective,
            r.proses_bisnis,
            rc.nama AS kategori_nama,
            r.risk_event,
            r.risk_cause,
            r.risk_source,
            r.risk_owner,
            r.likelihood_inherent,
            r.impact_inherent,
            r.risk_level_inherent,
            COUNT(t.id) AS total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) AS completed_treatments,
            r.created_at
        FROM risk_registers r
        LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE r.fakultas_id = ? AND YEAR(r.created_at) = ?
        GROUP BY r.id
        ORDER BY r.created_at DESC";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("iii", $tahun, $fakultasId, $tahun);
        $stmt->execute();
    
        $result = $stmt->get_result()->fetch_all(\MYSQLI_ASSOC);
    
        // Debug jika hasil kosong
        if (empty($result)) {
            error_log("No data returned for fakultas_id: {$fakultasId}, tahun: {$tahun}");
        }
    
        // Terapkan `calculateRiskLevel` untuk setiap risiko
        foreach ($result as &$risk) {
            $riskLevel = calculateRiskLevel(
                $risk['likelihood_inherent'] ?? 0,
                $risk['impact_inherent'] ?? 0
            );
            $risk['calculated_level'] = $riskLevel['level'];
    
            // Debug untuk memastikan nilai level risiko benar
            error_log("Risk ID: {$risk['id']}, Level: {$risk['calculated_level']}");
        }
    
        return $result;
    }
    
    

    
    public function exportData($fakultasId, $tahun)
    {
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
            ) as timeline_status,
            MAX(r.created_at) as max_created_at
        FROM fakultas f
        LEFT JOIN risk_registers r ON f.id = r.fakultas_id AND YEAR(r.created_at) = ?
        LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE 1=1";
    
        $params = [$tahun, $tahun];
        $types  = 'ii';
    
        if ($fakultasId) {
            $sql .= " AND f.id = ?";
            $params[] = $fakultasId;
            $types .= 'i';
        }
    
        $sql .= " GROUP BY f.nama, r.objective, r.proses_bisnis, rc.nama, r.risk_event, r.risk_cause, 
                         r.risk_source, r.risk_owner, r.likelihood_inherent, r.impact_inherent, 
                         r.risk_level_inherent, t.rencana_mitigasi, t.pic, t.evidence_type
                  ORDER BY f.nama, max_created_at";  // Gunakan kolom yang sudah di-aggregate
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    
        return $stmt->get_result()->fetch_all(\MYSQLI_ASSOC);
    }  
}
