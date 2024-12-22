<?php
namespace App\Http\Controllers;

use App\Services\TreatmentService;
use function App\Helpers\canAccessRisk; // misal helper

class TreatmentsController
{
    private $service;

    public function __construct()
    {
        $this->service = new TreatmentService();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $userRole   = $_SESSION['user_role'] ?? 'fakultas';
        $fakultasId = $_SESSION['fakultas_id'] ?? null;
        $treatments = $this->service->getAll($userRole, $fakultasId);

        global $treatmentList;
        $treatmentList = $treatments;

        require __DIR__ . '/../../../resources/views/treatments/index.blade.php';
    }

    public function create()
    {
        // menambahkan treatment = /treatments/create?risk_id=XX
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $risk_id = $_GET['risk_id'] ?? null;
        if (!$risk_id) {
            echo "No risk_id specified";
            return;
        }
        // check canAccessRisk($risk_id)
        if (!canAccessRisk($risk_id)) {
            echo "Access denied or invalid risk";
            return;
        }

        global $riskId;
        $riskId = $risk_id;

        // Mungkin ambil detail risk, dsb. 
        // require 'create.blade.php'
        require __DIR__ . '/../../../resources/views/treatments/create.blade.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $risk_id = $_POST['risk_id'] ?? null;
        // check canAccessRisk($risk_id)...

        $data = [
            'risk_register_id' => $risk_id,
            'rencana_mitigasi' => $_POST['rencana_mitigasi'] ?? '',
            'pic'             => $_POST['pic'] ?? '',
            'evidence_type'   => $_POST['evidence_type'] ?? ''
        ];
        $timeline = $_POST['timeline'] ?? []; // array of quarters => 1

        // service->create($data, $timeline)
        $this->service->create($data, $timeline);

        // redirect ke detail risk
        header('Location: ' . $_SESSION['base_uri'] . '/risk-register/view?id=' . $risk_id);
        exit();
    }

    public function edit()
    {
        // GET /treatments/edit?id=XX
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No treatment ID";
            return;
        }
        $treatment = $this->service->findById($id);
        if (!$treatment) {
            echo "Treatment not found";
            return;
        }
        // check canAccessRisk($treatment['risk_id'])...
        if (!canAccessRisk($treatment['risk_id'])) {
            echo "Access denied or invalid risk";
            return;
        }

        global $treatmentEdit;
        $treatmentEdit = $treatment;

        require __DIR__ . '/../../../resources/views/treatments/edit.blade.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "No treatment ID in POST";
            return;
        }

        // temukan data lama
        $old = $this->service->findById($id);
        if (!$old) {
            echo "Data not found";
            return;
        }
        // check canAccessRisk($old['risk_id'])

        $data = [
            'rencana_mitigasi' => $_POST['rencana_mitigasi'] ?? '',
            'pic'             => $_POST['pic'] ?? '',
            'evidence_type'   => $_POST['evidence_type'] ?? ''
        ];
        $timeline = $_POST['timeline'] ?? []; // e.g. timeline[timeline_id][realisasi] => 1

        $this->service->update($id, $data, $timeline);

        header('Location: ' . $_SESSION['base_uri'] . '/risk-register/view?id=' . $old['risk_id']);
        exit();
    }

    public function delete()
    {
        // GET /treatments/delete?id=XX
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No treatment ID";
            return;
        }
        // cari data
        $old = $this->service->findById($id);
        if (!$old) {
            echo "Data not found";
            return;
        }

        if (!canAccessRisk($old['risk_id'])) {
            echo "Access denied or invalid risk";
            return;
        }        
        // check canAccessRisk($old['risk_id'])...

        $this->service->delete($id);

        header('Location: ' . $_SESSION['base_uri'] . '/risk-register/view?id=' . $old['risk_id']);
        exit();
    }
}
