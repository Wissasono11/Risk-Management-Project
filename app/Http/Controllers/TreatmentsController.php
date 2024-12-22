<?php
namespace App\Http\Controllers;

use App\Services\TreatmentService;

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

        foreach ($treatments as &$treatment) {
            // Decode timelines jika berupa JSON string
            if (is_string($treatment['timelines'])) {
                $treatment['timelines'] = json_decode($treatment['timelines'], true);
            }
        
            // Hitung status berdasarkan timelines
            $treatment['status'] = calculateTreatmentStatus($treatment);
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

        // Ambil data risk_id dari URL
        $riskId = $_GET['risk_id'] ?? null;
        if (!$riskId) {
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        // Ambil nama Risk Event berdasarkan risk_id
        $riskEventName = $this->service->getRiskEventName($riskId);

        global $riskId, $riskEventName;
        $riskId = $riskId;
        $riskEventName = $riskEventName;

        require __DIR__ . '/../../../resources/views/treatments/create.blade.php';
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
            die('No risk_id provided');
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

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
    
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Treatment ID is required';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }
    
        try {
            $treatment = $this->service->findById($id);
            if (!$treatment) {
                $_SESSION['error'] = 'Treatment not found';
                header('Location: ' . $_SESSION['base_uri'] . '/treatments');
                exit();
            }
    
            global $treatmentEdit;
            $treatmentEdit = $treatment;
    
            require __DIR__ . '/../../../resources/views/treatments/edit.blade.php';
        } catch (Exception $e) {
            error_log("Edit Error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load treatment';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
        }
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
            $_SESSION['error'] = 'Treatment ID is required';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        try {
            $data = [
                'rencana_mitigasi' => $_POST['rencana_mitigasi'] ?? '',
                'pic' => $_POST['pic'] ?? '',
                'evidence_type' => $_POST['evidence_type'] ?? ''
            ];

            $timeline = $_POST['timeline'] ?? [];

            $this->service->update($id, $data, $timeline);
            $_SESSION['success'] = 'Treatment updated successfully';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();

        } catch (Exception $e) {
            error_log("Error updating treatment: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating treatment';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments/edit?id=' . $id);
            exit();
        }
    }

    // Method untuk menghapus Treatment
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $id = $_POST['id'] ?? null; // Pastikan method POST digunakan
        if (!$id) {
            $_SESSION['error'] = 'No treatment ID provided';
            header('Location: ' . $_SESSION['base_uri'] . '/treatments');
            exit();
        }

        try {
            $this->service->delete($id); // Pastikan method delete di Service benar
            $_SESSION['success'] = 'Treatment deleted successfully';
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to delete treatment';
        }

        header('Location: ' . $_SESSION['base_uri'] . '/treatments');
        exit();
    }
}
