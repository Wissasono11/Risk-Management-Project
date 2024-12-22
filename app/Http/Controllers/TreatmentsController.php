<?php
namespace App\Http\Controllers;

use App\Services\TreatmentService;
use function App\Helpers\canAccessRisk; // Pastikan helper ini tersedia

class TreatmentsController
{
    private $service;

    public function __construct()
    {
        $this->service = new TreatmentService();
        require_once dirname(__DIR__, 2) . '/Helpers/calculateTreatmentStatus.php';
    }

    // Method untuk menampilkan daftar treatments
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $userRole   = $_SESSION['user_role'] ?? 'fakultas';
        $fakultasId = $_SESSION['fakultas_id'] ?? null;
        $treatments = $this->service->getAll($userRole, $fakultasId);

        // Tambahkan status ke setiap treatment
        foreach ($treatments as &$treatment) {
            $treatment['status'] = calculateTreatmentStatus($treatment); // Pastikan fungsi ini tersedia
        }

        global $treatmentList;
        $treatmentList = $treatments;

        require __DIR__ . '/../../../resources/views/treatments/index.blade.php';
    }

    // Method untuk menampilkan halaman create
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        // Ambil data risk_id dan nama risiko yang diperlukan
        $riskId = $_GET['risk_id'] ?? null;
        if (!$riskId) {
            // Redirect atau tampilkan error jika tidak ada risk_id
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        // Ambil nama risiko berdasarkan risk_id (implementasikan sesuai kebutuhan)
        $riskEventName = $this->service->getRiskEventName($riskId);

        global $riskId, $riskEventName;
        $riskId = $riskId;
        $riskEventName = $riskEventName;

        require __DIR__ . '/../../../resources/views/treatments/create.blade.php';
    }

    // Method untuk menampilkan halaman edit
    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            // Redirect atau tampilkan error jika tidak ada id
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        $treatment = $this->service->findById($id);
        if (!$treatment) {
            // Redirect atau tampilkan error jika treatment tidak ditemukan
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        // Cek hak akses
        if (!canAccessRisk($treatment['risk_id'])) {
            // Redirect atau tampilkan error jika akses ditolak
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        global $treatmentEdit;
        $treatmentEdit = $treatment;

        require __DIR__ . '/../../../resources/views/treatments/edit.blade.php';
    }

    // Method untuk menyimpan Treatment baru
    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $risk_id = $_POST['risk_id'] ?? null;
        if (!$risk_id) {
            // Redirect atau tampilkan error jika tidak ada risk_id
            die('No risk_id provided');
        }

        // Cek hak akses
        if (!canAccessRisk($risk_id)) {
            // Redirect atau tampilkan error jika akses ditolak
            die('Access denied or invalid risk');
        }

        $data = [
            'risk_register_id' => $risk_id,
            'rencana_mitigasi' => $_POST['rencana_mitigasi'] ?? '',
            'pic'             => $_POST['pic'] ?? '',
            'evidence_type'   => $_POST['evidence_type'] ?? ''
        ];
        $timeline = $_POST['timeline'] ?? []; // array of quarters => 1

        try {
            $treatment_id = $this->service->create($data, $timeline);
            // Redirect ke halaman treatments setelah sukses
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
        } catch (\Exception $e) {
            error_log("Store Treatment Error: " . $e->getMessage());
            // Redirect atau tampilkan error jika gagal
            die("Failed to create treatment.");
        }
        exit();
    }

    // Method untuk memperbarui Treatment
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }


        $id = $_POST['id'] ?? null;
        if (!$id) {
            // Redirect atau tampilkan error jika tidak ada id
            die('No treatment ID provided');
        }

        $treatment = $this->service->findById($id);
        if (!$treatment) {
            // Redirect atau tampilkan error jika treatment tidak ditemukan
            die('Treatment not found');
        }

        // Cek hak akses
        if (!canAccessRisk($treatment['risk_id'])) {
            // Redirect atau tampilkan error jika akses ditolak
            die('Access denied or invalid risk');
        }

        $data = [
            'rencana_mitigasi' => $_POST['rencana_mitigasi'] ?? '',
            'pic'             => $_POST['pic'] ?? '',
            'evidence_type'   => $_POST['evidence_type'] ?? ''
        ];
        $timeline = $_POST['timeline'] ?? []; // e.g., timeline[1] = 1

        try {
            $this->service->update($id, $data, $timeline);
            // Redirect ke halaman treatments setelah sukses
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
        } catch (\Exception $e) {
            error_log("Update Treatment Error: " . $e->getMessage());
            // Redirect atau tampilkan error jika gagal
            die("Failed to update treatment.");
        }
        exit();
    }

    // Method untuk menghapus Treatment
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }


        $id = $_POST['id'] ?? null;
        if (!$id) {
            // Redirect atau tampilkan error jika tidak ada id
            die('No treatment ID provided');
        }

        $treatment = $this->service->findById($id);
        if (!$treatment) {
            // Redirect atau tampilkan error jika treatment tidak ditemukan
            die('Treatment not found');
        }

        // Cek hak akses
        if (!canAccessRisk($treatment['risk_id'])) {
            // Redirect atau tampilkan error jika akses ditolak
            die('Access denied or invalid risk');
        }

        try {
            $this->service->delete($id);
            // Redirect ke halaman treatments setelah sukses
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
        } catch (\Exception $e) {
            error_log("Delete Treatment Error: " . $e->getMessage());
            // Redirect atau tampilkan error jika gagal
            die("Failed to delete treatment.");
        }
        exit();
    }
}
