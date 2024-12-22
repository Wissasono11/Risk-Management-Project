<?php
namespace App\Interfaces;

interface TreatmentInterface
{
    /**
     * Ambil semua treatments (opsional filter by fakultas_id)
     */
    public function getAll($userRole, $fakultasId = null);

    /**
     * Temukan satu treatment by ID
     */
    public function findById($id);

    /**
     * Membuat treatment baru
     */
    public function create(array $data, array $timeline);

    /**
     * Update data treatment + timeline
     */
    public function update($id, array $data, array $timeline);

    /**
     * Hapus treatment (beserta timeline)
     */
    public function delete($id);

    /**
     * (Opsional) method lain seperti check permission, dsb.
     */
}

