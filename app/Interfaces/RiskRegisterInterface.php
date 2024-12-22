<?php
namespace App\Interfaces;

interface RiskRegisterInterface
{
    /**
     * Ambil semua risk registers (bisa difilter fakultas_id)
     */
    public function getAll($facultyId = null);

    /**
     * Ambil satu risk register by ID
     */
    public function findById($id);

    /**
     * Buat risk register baru (kembalikan ID)
     */
    public function create(array $data);

    /**
     * Update risk register by ID
     */
    public function update($id, array $data);

    /**
     * Hapus risk register by ID
     */
    public function delete($id);

    /**
     * Fungsi view detail (opsional, 
     *   tapi kita cukup pakai findById + 
     *   join/treatments dsb. di service)
     */
    // public function getDetail($id);
}
