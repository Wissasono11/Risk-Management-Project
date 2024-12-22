<?php
namespace App\Interfaces;

interface UserInterface
{
    /**
     * Ambil semua user
     */
    public function getAllUsers();

    /**
     * Cari user by ID
     */
    public function findUserById($id);

    /**
     * Buat user baru
     */
    public function createUser(array $data);

    /**
     * Update user
     */
    public function updateUser($id, array $data);

    /**
     * Hapus user
     */
    public function deleteUser($id);
}
