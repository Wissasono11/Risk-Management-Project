<?php
namespace App\Http\Middleware;

use App\Services\UserService;

class AuthMiddleware
{
    public function handle()
    {
        // Cek session
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        // Cek remember token
        if (isset($_COOKIE['remember_token'])) {
            $userService = new UserService();
            $user = $userService->findUserByRememberToken($_COOKIE['remember_token']);

            if ($user) {
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_profile_picture'] = $user['profile_picture'] ?? 'default.jpg';

                // Update last login
                $userService->updateLastLogin($user['id']);
                
                return true;
            }
        }

        header('Location: ' . $_SESSION['base_uri'] . '/login');
        exit();
    }
}