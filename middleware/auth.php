<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        if (isset($_COOKIE['remember_token'])) {
            global $conn;
            $token = $conn->real_escape_string($_COOKIE['remember_token']);
            $query = "SELECT * FROM users WHERE remember_token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fakultas_id'] = $user['fakultas_id'];
                return true;
            }
        }
        header("Location: ../index.php");
        exit();
    }
    return true;
}
