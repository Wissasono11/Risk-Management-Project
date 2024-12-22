<?php
namespace App\Interfaces;

interface ReportsInterface
{
    /**
     * Ambil summary data (untuk index)
     */
    public function getSummary($tahun, $fakultasId = null);

    /**
     * Ambil daftar fakultas (untuk filter)
     */
    public function getFaculties();

    /**
     * Ambil detail data (untuk detail report)
     */
    public function getDetailData($fakultasId, $tahun);

    /**
     * Export data
     */
    public function exportData($fakultasId, $tahun);
}
