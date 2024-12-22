<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class ProfileController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userService->findUserById($userId);

        // Ambil data total risks dan mitigations
        $counts = $this->userService->getRiskAndMitigationCounts($userId);

        global $profileUser;
        $profileUser = $user;
        $profileUser['total_risks'] = $counts['total_risks'];
        $profileUser['total_mitigations'] = $counts['total_mitigations'];

        require __DIR__ . '/../../../resources/views/profile/index.blade.php';
    }
    

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userService->findUserById($userId);

        global $profileUser;
        $profileUser = $user;

        require __DIR__ . '/../../../resources/views/profile/edit.blade.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        try {
            $userId = $_SESSION['user_id'];
            $data = [
                'email' => $_POST['email'] ?? '',
            ];

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['profile_picture']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $_SESSION['error'] = 'Format file tidak diizinkan';
                    header('Location: ' . $_SESSION['base_uri'] . '/profile/edit');
                    exit();
                }

                $newName = 'profile_' . $userId . '_' . time() . '.' . $ext;
                $uploadPath = __DIR__ . '/../../../public/uploads/profiles/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath . $newName)) {
                    $data['profile_picture'] = $newName;
                    $_SESSION['user_profile_picture'] = $newName;
                }
            }

            $success = $this->userService->updateProfile($userId, $data);

            if ($success) {
                $_SESSION['success'] = 'Profile updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update profile';
            }

            // Redirect ke halaman profil utama
            header('Location: ' . $_SESSION['base_uri'] . '/profile');
            exit();

        } catch (\Exception $e) {
            // Jika terjadi kesalahan, redirect kembali ke halaman edit dengan pesan error
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . $_SESSION['base_uri'] . '/profile/edit');
            exit();
        }
    }
}