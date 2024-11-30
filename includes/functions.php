<?php
// Risk Register Functions
function getRiskRegisters($fakultas_id = null) {
   global $conn;
   
   $sql = "SELECT r.*, f.nama as fakultas_nama, rc.nama as kategori_nama, 
           rc.kode as kategori_kode 
           FROM risk_registers r
           LEFT JOIN fakultas f ON r.fakultas_id = f.id 
           LEFT JOIN risk_categories rc ON r.kategori_id = rc.id";
   
   if ($fakultas_id) {
       $sql .= " WHERE r.fakultas_id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $fakultas_id);
   } else {
       $stmt = $conn->prepare($sql);
   }
   
   $stmt->execute();
   $result = $stmt->get_result();
   return $result->fetch_all(MYSQLI_ASSOC);
}

// Get single risk register
function getRiskById($id) {
   global $conn;
   
   $sql = "SELECT r.*, f.nama as fakultas_nama, rc.nama as kategori_nama 
           FROM risk_registers r
           LEFT JOIN fakultas f ON r.fakultas_id = f.id
           LEFT JOIN risk_categories rc ON r.kategori_id = rc.id 
           WHERE r.id = ?";
           
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $id);
   $stmt->execute();
   $result = $stmt->get_result();
   return $result->fetch_assoc();
}

// Calculate Risk Level berdasarkan Likelihood dan Impact
function calculateRiskLevel($likelihood, $impact) {
   $score = $likelihood * $impact;
   
   if ($score >= 16) return ['level' => 'ST', 'label' => 'Sangat Tinggi', 'color' => 'red'];
   if ($score >= 10) return ['level' => 'T', 'label' => 'Tinggi', 'color' => 'orange'];
   if ($score >= 5) return ['level' => 'S', 'label' => 'Sedang', 'color' => 'yellow'];
   if ($score >= 1) return ['level' => 'R', 'label' => 'Rendah', 'color' => 'green'];
   return ['level' => 'SR', 'label' => 'Sangat Rendah', 'color' => 'blue'];
}

// Get all treatments for a risk
function getRiskTreatments($risk_id) {
   global $conn;
   
   $sql = "SELECT t.*, mt.tahun, mt.triwulan, mt.rencana, mt.realisasi
           FROM risk_treatments t
           LEFT JOIN mitigation_timeline mt ON t.id = mt.treatment_id
           WHERE t.risk_register_id = ?
           ORDER BY mt.tahun, mt.triwulan";
           
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $risk_id);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get dashboard statistics
function getDashboardStats($fakultas_id = null) {
   global $conn;
   
   $where = $fakultas_id ? "WHERE fakultas_id = ?" : "";
   
   // Total risks
   $sql = "SELECT 
           COUNT(*) as total_risks,
           SUM(CASE WHEN risk_level_inherent IN ('T', 'ST') THEN 1 ELSE 0 END) as high_risks,
           COUNT(DISTINCT fakultas_id) as total_fakultas
           FROM risk_registers " . $where;
           
   $stmt = $conn->prepare($sql);
   if ($fakultas_id) {
       $stmt->bind_param("i", $fakultas_id);
   }
   $stmt->execute();
   return $stmt->get_result()->fetch_assoc();
}

// Format tanggal Indonesia
function formatTanggal($date) {
   $bulan = array(
       1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
   );
   
   $split = explode('-', $date);
   return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Security functions
function cleanInput($data) {
   global $conn;
   return $conn->real_escape_string(trim($data));
}

function generateRandomString($length = 10) {
   return bin2hex(random_bytes($length));
}

// Alert messages
function setAlert($type, $message) {
   $_SESSION['alert'] = [
       'type' => $type,
       'message' => $message
   ];
}

function getAlert() {
   if (isset($_SESSION['alert'])) {
       $alert = $_SESSION['alert'];
       unset($_SESSION['alert']);
       return $alert;
   }
   return null;
}

// Check permission based on role & fakultas
function canAccessRisk($risk_id) {
   global $conn;
   
   // Admin can access all
   if ($_SESSION['role'] === 'admin') return true;
   
   // Fakultas can only access their own risks
   $sql = "SELECT fakultas_id FROM risk_registers WHERE id = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $risk_id);
   $stmt->execute();
   $result = $stmt->get_result()->fetch_assoc();
   
   return $result['fakultas_id'] === $_SESSION['fakultas_id'];
}

// Logging function
function logActivity($user_id, $activity, $details = null) {
   global $conn;
   
   $sql = "INSERT INTO activity_logs (user_id, activity, details) VALUES (?, ?, ?)";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("iss", $user_id, $activity, $details);
   $stmt->execute();
}
?>