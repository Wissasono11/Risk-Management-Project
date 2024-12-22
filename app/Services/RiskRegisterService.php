<?php
namespace App\Services;

use App\Interfaces\RiskRegisterInterface;
use Config\Database;
use mysqli_sql_exception;

class RiskRegisterService implements RiskRegisterInterface
{
    private $conn;

    public function __construct()
    {
        // Dapatkan koneksi DB dari Singleton
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll($facultyId = null)
    {
        $sql = "SELECT r.*, rc.nama as kategori_nama 
                FROM risk_registers r
                LEFT JOIN risk_categories rc ON r.kategori_id = rc.id
                WHERE 1=1";

        if ($facultyId) {
            $sql .= " AND r.fakultas_id = " . (int)$facultyId;
        }

        $sql .= " ORDER BY r.created_at DESC";

        try {
            $result = $this->conn->query($sql);
            return $result->fetch_all(\MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            // handle error
            throw $e;
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
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO risk_registers 
                (fakultas_id, objective, proses_bisnis, kategori_id, 
                 risk_event, risk_cause, risk_source, risk_owner,
                 likelihood_inherent, impact_inherent, 
                 risk_level_inherent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);

        // Kita asumsikan data array seperti:
        // [
        //   'fakultas_id' => 3,
        //   'objective' => '...',
        //   'proses_bisnis' => '...',
        //   'kategori_id' => 2,
        //   'risk_event' => '...',
        //   'risk_cause' => '...',
        //   'risk_source' => 'external',
        //   'risk_owner' => 'Nama PIC',
        //   'likelihood' => 4,
        //   'impact' => 3,
        //   'risk_level' => 'S'
        // ]
        $stmt->bind_param(
            "ississssiii", 
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
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE risk_registers SET
                objective = ?, 
                proses_bisnis = ?,
                kategori_id = ?,
                risk_event = ?,
                risk_cause = ?,
                risk_source = ?, /* ENUM type */
                risk_owner = ?,
                likelihood_inherent = ?,
                impact_inherent = ?,
                risk_level_inherent = ?,
                updated_at = NOW()
                WHERE id = ?";
    
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Ubah tipe binding parameter - 's' untuk string, 'i' untuk integer
            $stmt->bind_param(
                "ssissssiisi", // Perhatikan 's' untuk risk_source
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
            
            // Execute dan validasi hasilnya
            if (!$stmt->execute()) {
                throw new Exception("Failed to update risk register: " . $stmt->error);
            }
            
            return true;
            
        } catch (mysqli_sql_exception $e) {
            // Log error dan throw kembali
            error_log("Error updating risk register: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        // Hapus data child (risk_treatments) jika butuh
        $sqlTreat = "DELETE FROM risk_treatments WHERE risk_register_id = ?";
        $stmtTreat = $this->conn->prepare($sqlTreat);
        $stmtTreat->bind_param("i", $id);
        $stmtTreat->execute();

        // Lalu hapus risk_register
        $sql = "DELETE FROM risk_registers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
