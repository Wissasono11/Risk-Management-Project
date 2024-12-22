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
    }

    public function getSummary($tahun, $fakultasId = null)
    {
        // Logika sama seperti di index.php lama
        // SELECT f.id, f.nama, COUNT(r.id), dsb.
        $sql = "SELECT 
            f.id as fakultas_id,
            f.nama as fakultas_nama,
            COUNT(r.id) as total_risks,
            SUM(CASE WHEN r.risk_level_inherent IN ('T','ST') THEN 1 ELSE 0 END) as high_risks,
            COUNT(t.id) as total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
        FROM fakultas f
        LEFT JOIN risk_registers r ON f.id = r.fakultas_id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE 1=1";
        
        $params = [];
        $types  = 'i'; // pertama untuk tahun
        $params[] = $tahun;

        if ($fakultasId) {
            $sql .= " AND f.id = ?";
            $types .= 'i';
            $params[] = $fakultasId;
        }

        $sql .= " GROUP BY f.id, f.nama
                  ORDER BY f.nama";

        $stmt = $this->conn->prepare($sql);
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
        // SELECT r.*, rc.nama as kategori_nama, ...
        $sql = "SELECT 
            r.*,
            rc.nama as kategori_nama,
            COUNT(t.id) as total_treatments,
            SUM(CASE WHEN mt.realisasi = 1 THEN 1 ELSE 0 END) as completed_treatments
        FROM risk_registers r
        LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
        LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
        LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id AND mt.tahun = ?
        WHERE r.fakultas_id = ?
        GROUP BY r.id
        ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $tahun, $fakultasId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(\MYSQLI_ASSOC);
    }

    public function exportData($fakultasId, $tahun)
    {
        // Kode di export.php lama
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

        $params = [];
        $types  = 'i';
        $params[] = $tahun;

        if ($fakultasId) {
            $sql .= " AND f.id = ?";
            $types .= 'i';
            $params[] = $fakultasId;
        }

        $sql .= " GROUP BY r.id, t.id
                  ORDER BY f.nama, r.created_at";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(\MYSQLI_ASSOC);
    }
}
