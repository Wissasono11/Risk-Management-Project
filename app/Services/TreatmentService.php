<?php
namespace App\Services;

use App\Interfaces\TreatmentInterface;
use Config\Database;
use mysqli_sql_exception;

class TreatmentService implements TreatmentInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll($userRole, $fakultasId = null)
    {
        // Mirip code lama di index.php
        $sql = "SELECT t.*, r.risk_event, r.fakultas_id
                FROM risk_treatments t
                JOIN risk_registers r ON t.risk_register_id = r.id";

        // Jika bukan admin, filter by fakultas_id
        if ($userRole !== 'admin' && $fakultasId) {
            $sql .= " WHERE r.fakultas_id = " . (int)$fakultasId;
        }

        try {
            $result = $this->conn->query($sql);
            return $result->fetch_all(\MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function findById($id)
    {
        // Dapatkan data treatment + risk info
        $sql = "SELECT t.*, r.id as risk_id, r.risk_event
                FROM risk_treatments t
                JOIN risk_registers r ON t.risk_register_id = r.id
                WHERE t.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $treatment = $stmt->get_result()->fetch_assoc();

        if (!$treatment) {
            return null;
        }

        // Dapatkan timeline
        $sqlTime = "SELECT * FROM mitigation_timeline 
                    WHERE treatment_id = ?
                    ORDER BY tahun, triwulan";
        $stmt2 = $this->conn->prepare($sqlTime);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $timelines = $stmt2->get_result()->fetch_all(\MYSQLI_ASSOC);

        // satukan
        $treatment['timelines'] = $timelines;
        return $treatment;
    }

    public function create(array $data, array $timeline)
    {
        // Insert treatment
        $sql = "INSERT INTO risk_treatments 
                (risk_register_id, rencana_mitigasi, pic, evidence_type) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss",
            $data['risk_register_id'],
            $data['rencana_mitigasi'],
            $data['pic'],
            $data['evidence_type']
        );
        $stmt->execute();
        $treatment_id = $this->conn->insert_id;

        // Insert timeline
        foreach ($timeline as $quarter => $isChecked) {
            if ($isChecked == 1) {
                $tahun = date('Y');
                $sql2 = "INSERT INTO mitigation_timeline (treatment_id, tahun, triwulan)
                         VALUES (?, ?, ?)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bind_param("iii", $treatment_id, $tahun, $quarter);
                $stmt2->execute();
            }
        }
        return $treatment_id;
    }

    public function update($id, array $data, array $timeline)
    {
        // update treatment
        $sql = "UPDATE risk_treatments SET
                rencana_mitigasi = ?,
                pic = ?,
                evidence_type = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi",
            $data['rencana_mitigasi'],
            $data['pic'],
            $data['evidence_type'],
            $id
        );
        $stmt->execute();

        // update timeline
        // misalnya user mengirim 'timeline[timeline_id][realisasi]=1'
        // kita loop
        foreach ($timeline as $timeline_id => $value) {
            $sql3 = "UPDATE mitigation_timeline 
                     SET realisasi = ?
                     WHERE id = ?";
            $stmt3 = $this->conn->prepare($sql3);
            $realisasi = isset($value['realisasi']) ? 1 : 0;
            $stmt3->bind_param("ii", $realisasi, $timeline_id);
            $stmt3->execute();
        }
    }

    public function delete($id)
    {
        // cari treatment untuk ambil risk_id (jika perlu)
        $data = $this->findById($id);
        if (!$data) {
            return false; 
        }
        // hapus timeline
        $sql = "DELETE FROM mitigation_timeline WHERE treatment_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // hapus risk_treatment
        $sql2 = "DELETE FROM risk_treatments WHERE id = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        return $stmt2->execute();
    }
}
