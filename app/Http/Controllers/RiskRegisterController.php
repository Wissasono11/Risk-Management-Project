<?php
namespace App\Http\Controllers;

use App\Services\RiskRegisterService;
use Exception;

class RiskRegisterController
{
    private $service;

    public function __construct()
    {
        // constructor
        $this->service = new RiskRegisterService();
    }

    public function index()
    {
        // 1. Cek login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
    
        // 2. Cek role, ambil facultyId jika bukan admin
        $facultyId = null;
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin') {
            $facultyId = $_SESSION['fakultas_id'] ?? null;
        }
    
        // 3. Panggil service
        $risks = $this->service->getAll($facultyId);
    
        // 4. Sediakan data untuk view
        //    Kita simpan di variable global misalnya $riskRegisters
        global $riskRegisters;
        $riskRegisters = $risks;
    
        // 5. Render view
        require __DIR__ . '/../../../resources/views/risk_register/index.blade.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        // Tampilkan form create
        require __DIR__ . '/../../../resources/views/risk_register/create.blade.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        // Tangkap POST data
        $fakultas_id      = $_SESSION['fakultas_id'] ?? null; 
        $objective        = $_POST['objective'] ?? '';
        $proses_bisnis    = $_POST['proses_bisnis'] ?? '';
        $kategori_id      = (int)($_POST['kategori_id'] ?? 1);
        $risk_event       = $_POST['risk_event'] ?? '';
        $risk_cause       = $_POST['risk_cause'] ?? '';
        $risk_source      = $_POST['risk_source'] ?? 'internal';
        $risk_owner       = $_POST['risk_owner'] ?? '';
        $likelihood       = (int)($_POST['likelihood'] ?? 1);
        $impact           = (int)($_POST['impact'] ?? 1);

        // Hitung risk_level
        $score = $likelihood * $impact;
        $riskLevel = $this->computeRiskLevel($score);

        // Build array
        $data = [
            'fakultas_id' => $fakultas_id,
            'objective' => $objective,
            'proses_bisnis' => $proses_bisnis,
            'kategori_id' => $kategori_id,
            'risk_event' => $risk_event,
            'risk_cause' => $risk_cause,
            'risk_source' => $risk_source,
            'risk_owner' => $risk_owner,
            'likelihood_inherent' => $likelihood,
            'impact_inherent' => $impact,
            'risk_level_inherent' => $riskLevel
        ];

        // panggil service->create($data)
        $this->service->create($data);

        // redirect ke /risk-register
        header('Location: ' . $_SESSION['base_uri'] . '/risk-register');
        exit();
    }

    public function view()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            // handle error
            echo "No ID specified";
            return;
        }

        $risk = $this->service->findById($id);
        if (!$risk) {
            echo "Data not found";
            return;
        }

        // global variable or just local
        global $riskDetail;
        $riskDetail = $risk;

        require __DIR__ . '/../../../resources/views/risk_register/view.blade.php';
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No ID specified for edit";
            return;
        }

        $risk = $this->service->findById($id);
        if (!$risk) {
            echo "Risk not found or no access";
            return;
        }

        global $riskEdit;
        $riskEdit = $risk;

        require __DIR__ . '/../../../resources/views/risk_register/edit.blade.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
    
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "No ID specified for update";
            return;
        }
    
        try {
            // Validasi risk_source hanya bisa 'internal' atau 'external'
            $risk_source = $_POST['risk_source'] ?? 'internal';
            if (!in_array($risk_source, ['internal', 'external'])) {
                $risk_source = 'internal'; // default value
            }
    
            // tangkap dan validasi data lainnya
            $data = [
                'objective' => trim($_POST['objective'] ?? ''),
                'proses_bisnis' => trim($_POST['proses_bisnis'] ?? ''),
                'kategori_id' => (int)($_POST['kategori_id'] ?? 1),
                'risk_event' => trim($_POST['risk_event'] ?? ''),
                'risk_cause' => trim($_POST['risk_cause'] ?? ''),
                'risk_source' => $risk_source,
                'risk_owner' => trim($_POST['risk_owner'] ?? ''),
                'likelihood_inherent' => (int)($_POST['likelihood'] ?? 1),
                'impact_inherent' => (int)($_POST['impact'] ?? 1),
                'risk_level_inherent' => $this->computeRiskLevel(
                    (int)($_POST['likelihood'] ?? 1) * (int)($_POST['impact'] ?? 1)
                )
            ];
    
            // Update data
            $this->service->update($id, $data);
    
            // Redirect setelah sukses
            header('Location: ' . $_SESSION['base_uri'] . '/risk-register');
            exit();
    
        } catch (Exception $e) {
            // Handle error
            $error = "Failed to update risk: " . $e->getMessage();
            
            // Simpan error ke session untuk ditampilkan di form
            $_SESSION['error'] = $error;
            
            // Redirect kembali ke form edit
            header('Location: ' . $_SESSION['base_uri'] . '/risk-register/edit?id=' . $id);
            exit();
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No ID";
            return;
        }

        $this->service->delete($id);
        header('Location: ' . $_SESSION['base_uri'] . '/risk-register');
        exit();
    }

    private function computeRiskLevel($score)
    {
        if ($score >= 16) return 'ST';
        if ($score >= 10) return 'T';
        if ($score >= 5)  return 'S';
        if ($score >= 1)  return 'R';
        return 'SR';
    }
}
