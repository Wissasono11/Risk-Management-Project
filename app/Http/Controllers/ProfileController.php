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
        // Cek login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $userId = $_SESSION['user_id'];
        $user   = $this->userService->findUserById($userId);
        // misal userService->findUserById() diimplementasi men-join ke fakultas

        global $profileUser;
        $profileUser = $user;

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

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        $data = [
            'email' => $_POST['email'] ?? '',
        ];
    
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['profile_picture']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
            if (in_array($ext, $allowed)) {
                $newName = 'profile_' . $userId . '_' . time() . '.' . $ext;
                $uploadPath = __DIR__ . '/../../../public/uploads/profiles/';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
    
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath . $newName)) {
                    $data['profile_picture'] = $newName;
                }
            }
        }
    
        $this->userService->updateProfile($userId, $data);
        
        // Return JSON response for AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        exit();
    }
}
