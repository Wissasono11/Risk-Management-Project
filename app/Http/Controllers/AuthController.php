<?php
namespace App\Http\Controllers;

use App\Services\UserService;

class AuthController
{
    public function showLogin()
    {
        require_once __DIR__ . '/../../../resources/views/auth/login.blade.php';
    }

    public function processLogin()
    {
        $email = $_POST['email'] ?? '';
        $plainPassword = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember']) && $_POST['remember'] === 'on';

        $userService = new UserService();
        $user = $userService->findUserByEmailAndPassword($email, $plainPassword);

        if ($user) {
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_profile_picture'] = $user['profile_picture'] ?? 'default.jpg';

            // Handle Remember Me
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                
                // Set cookie yang expired dalam 30 hari
                setcookie(
                    'remember_token',
                    $token,
                    [
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
                
                // Update remember token di database
                $userService->updateRememberToken($user['id'], $token);
            }

            // Update last login
            $userService->updateLastLogin($user['id']);

            header('Location: ' . $_SESSION['base_uri'] . '/dashboard');
            exit();
        } else {
            header('Location: ' . $_SESSION['base_uri'] . '/login?error=invalid');
            exit();
        }
    }

    public function logout()
    {
        // Hapus remember token dari database dan cookie
        if (isset($_COOKIE['remember_token'])) {
            $userService = new UserService();
            $userService->removeRememberToken($_SESSION['user_id']);
            
            setcookie(
                'remember_token',
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        }

        session_destroy();
        header('Location: ' . $_SESSION['base_uri'] . '/login');
        exit();
    }
}