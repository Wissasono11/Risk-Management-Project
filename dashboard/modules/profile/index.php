<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
checkAuth();

$user = getUserDetails($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../../assets/css/profile.css">
</head>
<body>
    <div class="container my-5">
        <div class="profile-section">
            <div class="profile-card">
                <div class="profile-picture">
                    <img src="../assets/img/picture.jpg" alt="Profile Picture">
                </div>
                <h4 class="profile-name"><?= htmlspecialchars($user['email']) ?></h4>
                <p class="profile-role"><?= $user['role'] ?></p>
            </div>
            <div class="profile-details">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Profile Details</h4>
                    </div>
                    <div class="card-body">
                        <table class="profile-table">
                            <tbody>
                                <tr>
                                    <th>Fakultas</th>
                                    <td><?= htmlspecialchars($user['fakultas_nama'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Last Login</th>
                                    <td><?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
