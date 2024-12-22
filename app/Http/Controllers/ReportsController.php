<?php
namespace App\Http\Controllers;

use App\Services\ReportsService;
use function App\Helpers\calculateRiskLevel; // jika perlu

class ReportsController
{
    private $service;

    public function __construct()
    {
        $this->service = new ReportsService();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $tahun       = $_GET['tahun'] ?? date('Y');
        $fakultas_id = $_GET['fakultas_id'] ?? null;

        $faculties = $this->service->getFaculties();
        $summary   = $this->service->getSummary($tahun, $fakultas_id);

        global $reportsFaculties, $reportsSummary, $filterYear, $filterFaculty;
        $reportsFaculties = $faculties;
        $reportsSummary   = $summary;
        $filterYear       = $tahun;
        $filterFaculty    = $fakultas_id;

        // Tampilkan view index.blade.php
        require __DIR__ . '/../../../resources/views/reports/index.blade.php';
    }

    public function detail()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
    
        $fakultas_id = $_GET['fakultas_id'] ?? null;
        $tahun       = $_GET['tahun'] ?? date('Y');
    
        // cek data fakultas
        $faculties = $this->service->getFaculties();
        $exists = array_filter($faculties, fn($f) => $f['id'] == $fakultas_id);
        if (!$exists) {
            echo "Fakultas not found";
            return;
        }
    
        // Ambil data detail risiko
        $risks = $this->service->getDetailData($fakultas_id, $tahun);
    
        // Hitung statistik
        $total_risks = count($risks);
        $high_risks = 0;
        $total_treatments = 0;
        $completed = 0;
    
        foreach ($risks as $r) {
            // Pastikan risk_level_inherent menggunakan level risiko yang benar
            if (in_array($r['risk_level_inherent'], ['High', 'Very-High'])) {
                $high_risks++;
            }
            $total_treatments += $r['total_treatments'];
            $completed += $r['completed_treatments'];
        }
    
        // Simpan hasil ke variabel global untuk view
        global $detailRisks, $detailFakultasId, $detailYear, $detailTotalRisks, $detailHighRisks, $detailTotalTreatments, $detailCompleted;
        $detailRisks = $risks;
        $detailFakultasId = $fakultas_id;
        $detailYear = $tahun;
        $detailTotalRisks = $total_risks;
        $detailHighRisks = $high_risks;
        $detailTotalTreatments = $total_treatments;
        $detailCompleted = $completed;
    
        require __DIR__ . '/../../../resources/views/reports/detail.blade.php';
    }
    

    public function export()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $fakultas_id = $_GET['fakultas_id'] ?? null;
        $tahun       = $_GET['tahun'] ?? date('Y');

        // set headers
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="risk_report_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        $rows = $this->service->exportData($fakultas_id, $tahun);
        
        // Render view export, atau echo table langsung
        global $exportData, $exportYear;
        $exportData = $rows;
        $exportYear = $tahun;

        require __DIR__ . '/../../../resources/views/reports/export.blade.php';
    }
}
