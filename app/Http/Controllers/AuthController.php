<?php
namespace App\Http\Controllers;

use App\Services\UserService;

class AuthController
{
    public function showLogin()
    {
        // Tampilkan form login
        require_once __DIR__ . '/../../../resources/views/auth/login.blade.php';
    }

    public function processLogin() {
        $email = $_POST['email'] ?? '';
        $plainPassword = $_POST['password'] ?? '';
    
        $userService = new UserService();
        $user = $userService->findUserByEmailAndPassword($email, $plainPassword);
    
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
    
            // Update last login
            $userService->updateLastLogin($user['id']);
    
            header('Location: '.$_SESSION['base_uri'].'/dashboard');
            exit();
        } else {
            header('Location: '.$_SESSION['base_uri'].'/login?error=invalid');
            exit();
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: '.$_SESSION['base_uri'].'/login');
        exit();
    }
}
