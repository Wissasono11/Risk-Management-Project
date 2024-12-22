<?php
namespace App\Services;

use App\Interfaces\RiskRegisterInterface;
use Config\Database;
use mysqli_sql_exception;
use Exception;

class RiskRegisterService implements RiskRegisterInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        require_once dirname(__DIR__) . '/Helpers/calculateRiskLevel.php';
    }

    public function getAll($facultyId = null)
    {
        $sql = "
            SELECT 
                r.*, 
                rc.nama AS kategori_nama, 
                f.nama AS fakultas_nama,
                COUNT(DISTINCT t.id) AS treatments_count,
                COUNT(DISTINCT CASE WHEN mt.realisasi IS NOT NULL THEN mt.id END) AS completed_treatments
            FROM risk_registers r
            LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
            LEFT JOIN fakultas f ON r.fakultas_id = f.id
            LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
            LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id
            WHERE 1=1
        ";
    
        // Tambahkan kondisi fakultas jika `$facultyId` diberikan
        $params = [];
        if ($facultyId) {
            $sql .= " AND r.fakultas_id = ?";
            $params[] = $facultyId;
        }
    
        $sql .= " GROUP BY r.id ORDER BY r.created_at DESC";
    
        // Gunakan prepared statements untuk keamanan
        try {
            $stmt = $this->conn->prepare($sql);
            if ($facultyId) {
                $stmt->bind_param('i', ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $risks = $result->fetch_all(MYSQLI_ASSOC);
            return $risks;
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error fetching risk data: " . $e->getMessage());
        }
    }
    
    

    public function findById($id)
    {
        $sql = "SELECT r.*, 
                       rc.nama as kategori_nama, 
                       f.nama as fakultas_nama
                FROM risk_registers r
                LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
                LEFT JOIN fakultas f ON r.fakultas_id = f.id
                WHERE r.id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $risk = $res->fetch_assoc();
        $stmt->close();
        return $risk;
    }

    public function create(array $data)
    {
        // Tidak mengubah 'risk_level_inherent'
        $sql = "INSERT INTO risk_registers 
                (fakultas_id, objective, proses_bisnis, kategori_id, 
                 risk_event, risk_cause, risk_source, risk_owner,
                 likelihood_inherent, impact_inherent, risk_level_inherent, 
                 created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param(
            "ississssiss", 
            $data['fakultas_id'],
            $data['objective'],
            $data['proses_bisnis'],
            $data['kategori_id'],
            $data['risk_event'],
            $data['risk_cause'],
            $data['risk_source'],
            $data['risk_owner'],
            $data['likelihood_inherent'],
            $data['impact_inherent'],
            $data['risk_level_inherent']
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return $insert_id;
    }

    public function update($id, array $data)
    {
        // Tidak mengubah 'risk_level_inherent'
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
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param(
            "ssissssissi", 
            $data['objective'],
            $data['proses_bisnis'],
            $data['kategori_id'],
            $data['risk_event'],
            $data['risk_cause'],
            $data['risk_source'],
            $data['risk_owner'],
            $data['likelihood_inherent'],
            $data['impact_inherent'],
            $data['risk_level_inherent'],
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        return true;
    }

    public function delete($id)
    {
        // Hapus data child (risk_treatments) jika perlu
        $sqlTreat = "DELETE FROM risk_treatments WHERE risk_register_id = ?";
        $stmtTreat = $this->conn->prepare($sqlTreat);
        if (!$stmtTreat) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmtTreat->bind_param("i", $id);
        if (!$stmtTreat->execute()) {
            throw new Exception("Execute failed: " . $stmtTreat->error);
        }
        $stmtTreat->close();

        // Lalu hapus risk_register
        $sql = "DELETE FROM risk_registers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();
        return true;
    }
}
