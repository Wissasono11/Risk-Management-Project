<?php
namespace App\Http\Controllers;

use Config\Database;
use function App\Helpers\calculateRiskLevel; // optional if needed

class DashboardController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit;
        }

        $conn = Database::getInstance()->getConnection();
        $showRiskSummary = true;

        // misalnya $stats
        $stats = [
            'total_risks'   => 0,
            'high_risks'    => 0,
            'total_fakultas'=> 0
        ];
        // ... query real

        // misal $risk_distribution
        $risk_distribution = [
            'sr_count' => 2,
            'r_count'  => 3,
            // ...
        ];
        // ... query real

        // misal $recent_activities
        $recent_activities = []; 
        // ... query real

        // Lalu simpan ke global
        global $stats, $risk_distribution, $recent_activities;
        
        // Load view
        require __DIR__ . '/../../../resources/views/dashboard/index.blade.php';
    }
}
