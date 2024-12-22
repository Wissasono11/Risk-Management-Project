<?php
// DashboardController.php

namespace App\Http\Controllers;

use Config\Database;

class DashboardController
{
    public function index()
    {
        session_start(); // Pastikan session dimulai

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit;
        }

        $conn = Database::getInstance()->getConnection();
        $showRiskSummary = true;

        // Contoh data statis (ganti dengan query nyata)
        $stats = [
            'total_risks'    => 20,
            'high_risks'     => 5,
            'total_fakultas' => 3
        ];

        // Contoh distribusi risiko berdasarkan level (ganti dengan query nyata)
        $risk_distribution = [
            'sr_count' => 2, // Very Low
            'r_count'  => 5, // Low
            's_count'  => 8, // Medium
            't_count'  => 3, // High
            'st_count' => 2  // Very High
        ];

        // Contoh aktivitas terbaru (ganti dengan query nyata)
        $recent_activities = [
            [
                'risk_event'          => 'Data Breach',
                'rencana_mitigasi'    => 'Implement encryption',
                'likelihood_inherent' => 4,
                'impact_inherent'     => 5,
                'triwulan'            => 1,
                'tahun'               => 2024
            ],
            // Tambahkan aktivitas lainnya sesuai kebutuhan
        ];

        // Menyimpan data ke variabel global untuk Blade template
        global $stats, $risk_distribution, $recent_activities;

        // Memuat view
        require __DIR__ . '/../../../resources/views/dashboard/index.blade.php';
    }
}
