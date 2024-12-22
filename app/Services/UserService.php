<?php

namespace App\Services;

use App\Interfaces\UserInterface;
use Config\Database;
use mysqli_sql_exception;

class UserService implements UserInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAllUsers()
    {
        $sql = "SELECT u.*, f.nama as fakultas_nama 
                FROM users u 
                LEFT JOIN fakultas f ON u.fakultas_id = f.id 
                ORDER BY u.created_at DESC";
        try {
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function findUserById($id)
    {
        $sql = "SELECT u.*, f.nama as fakultas_nama 
                FROM users u 
                LEFT JOIN fakultas f ON u.fakultas_id = f.id 
                WHERE u.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findUserByEmailAndPassword($email, $plainPassword)
    {
        $passwordHash = hash('sha256', $plainPassword);
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $passwordHash);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($userId, array $data)
    {
        try {
            $setFields = [];
            $types = '';
            $values = [];

            if (isset($data['email'])) {
                $setFields[] = 'email = ?';
                $types .= 's';
                $values[] = $data['email'];
            }

            if (isset($data['profile_picture'])) {
                $setFields[] = 'profile_picture = ?';
                $types .= 's';
                $values[] = $data['profile_picture'];
            }

            if (empty($setFields)) {
                return false;
            }

            $types .= 'i';
            $values[] = $userId;

            $sql = "UPDATE users SET " . implode(', ', $setFields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new \Exception($this->conn->error);
            }

            $stmt->bind_param($types, ...$values);

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            return false;
        }
    }

    public function createUser(array $data)
    {
        try {
            $sql = "INSERT INTO users (email, password, fakultas_id) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $passwordHash = hash('sha256', $data['password']);
            $stmt->bind_param("ssi", $data['email'], $passwordHash, $data['fakultas_id']);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, array $data)
    {
        try {
            $sql = "UPDATE users SET email = ?, fakultas_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $data['email'], $data['fakultas_id'], $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id)
    {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }
}
