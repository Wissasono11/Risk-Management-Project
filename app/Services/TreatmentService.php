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
        $sql = "SELECT t.*, 
                    r.risk_event, 
                    r.likelihood_inherent, 
                    r.impact_inherent, 
                    r.fakultas_id, 
                    rc.nama as kategori_nama, 
                    f.nama as fakultas_nama,
                    (
                        SELECT JSON_ARRAYAGG(JSON_OBJECT(
                            'id', mt.id,
                            'tahun', mt.tahun,
                            'triwulan', mt.triwulan,
                            'realisasi', mt.realisasi
                        ))
                        FROM mitigation_timeline mt
                        WHERE mt.treatment_id = t.id
                    ) AS timelines
                FROM risk_registers r
                LEFT JOIN risk_treatments t ON r.id = t.risk_register_id
                LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
                LEFT JOIN fakultas f ON r.fakultas_id = f.id
                WHERE 1=1";

        if ($userRole === 'fakultas' && $fakultasId) {
            $sql .= " AND r.fakultas_id = " . (int)$fakultasId;
        }

        $sql .= " ORDER BY t.created_at DESC";

        try {
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }

    public function findById($id)
    {
        $sql = "SELECT t.*, r.risk_event, r.fakultas_id,
                    rc.nama as kategori_nama,
                    f.nama as fakultas_nama
                FROM risk_treatments t
                JOIN risk_registers r ON t.risk_register_id = r.id
                LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
                LEFT JOIN fakultas f ON r.fakultas_id = f.id
                WHERE t.id = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $treatment = $result->fetch_assoc();
            $stmt->close();

            // Get treatment timelines
            if ($treatment) {
                $timelineSql = "SELECT * FROM mitigation_timeline 
                                WHERE treatment_id = ? 
                                ORDER BY tahun, triwulan";
                $stmt = $this->conn->prepare($timelineSql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $treatment['timelines'] = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }

            // Debug log
            error_log("Found Treatment: " . json_encode($treatment));

            return $treatment;
        } catch (mysqli_sql_exception $e) {
            error_log("Error in findById: " . $e->getMessage());
            return null;
        }
    }


    public function getRiskEventName($riskId)
    {
        $sql = "SELECT risk_event FROM risk_registers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $riskId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['risk_event'] ?? null;
    }
    


    public function create($data, $timeline)
    {
        try {
            $this->conn->begin_transaction();

            // Insert treatment
            $sql = "INSERT INTO risk_treatments 
                    (risk_register_id, rencana_mitigasi, pic, evidence_type) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("isss",
                $data['risk_register_id'],
                $data['rencana_mitigasi'],
                $data['pic'],
                $data['evidence_type']
            );

            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }

            $treatment_id = $this->conn->insert_id;
            $stmt->close();

            // Insert timeline entries
            if (!empty($timeline)) {
                $sql = "INSERT INTO mitigation_timeline 
                        (treatment_id, tahun, triwulan) 
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);

                foreach ($timeline as $quarter => $isChecked) {
                    if ($isChecked) {
                        $tahun = date('Y');
                        $stmt->bind_param("iii", $treatment_id, $tahun, $quarter);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            $this->conn->commit();
            return $treatment_id;

        } catch (\Exception $e) {
            $this->conn->rollback();
            error_log("Error in create: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $data, $timeline)
    {
        try {
            $this->conn->begin_transaction();

            // Update treatment
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
            $stmt->close();

            // Update timelines
            if (!empty($timeline)) {
                $sql = "UPDATE mitigation_timeline 
                        SET realisasi = ?
                        WHERE id = ?";
                $stmt = $this->conn->prepare($sql);

                foreach ($timeline as $timeline_id => $value) {
                    $realisasi = isset($value['realisasi']) ? 1 : 0;
                    $stmt->bind_param("ii", $realisasi, $timeline_id);
                    $stmt->execute();
                }
                $stmt->close();
            }

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollback();
            error_log("Error in update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $this->conn->begin_transaction();
    
            // Delete timelines terlebih dahulu
            $sql = "DELETE FROM mitigation_timeline WHERE treatment_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
    
            // Delete treatment
            $sql = "DELETE FROM risk_treatments WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
    
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            error_log("Error in delete: " . $e->getMessage());
            throw $e;
        }
    }
}