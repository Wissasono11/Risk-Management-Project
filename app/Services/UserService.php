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
            return $result->fetch_all(\MYSQLI_ASSOC);
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

        $sql = "SELECT u.*, f.nama as fakultas_nama 
                FROM users u 
                LEFT JOIN fakultas f ON u.fakultas_id = f.id
                WHERE u.email = ? AND u.password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $passwordHash);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    

    public function createUser(array $data)
    {
        $sql = "INSERT INTO users (email, password, role, fakultas_id)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi",
            $data['email'],
            $data['password'],
            $data['role'],
            $data['fakultas_id']
        );
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function updateUser($id, array $data)
    {
        // Jika password diubah
        if (!empty($data['password'])) {
            $sql = "UPDATE users SET email=?, password=?, role=?, fakultas_id=? 
                    WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssii",
                $data['email'],
                $data['password'],
                $data['role'],
                $data['fakultas_id'],
                $id
            );
        } else {
            // Password tidak diubah
            $sql = "UPDATE users SET email=?, role=?, fakultas_id=?
                    WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssii",
                $data['email'],
                $data['role'],
                $data['fakultas_id'],
                $id
            );
        }
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function updateProfile($userId, array $data)
    {
        try {
            $setFields = [];
            $values = [];
            $types = '';

            if (isset($data['email'])) {
                $setFields[] = 'email = ?';
                $values[] = $data['email'];
                $types .= 's';
            }

            if (isset($data['profile_picture'])) {
                $setFields[] = 'profile_picture = ?';
                $values[] = $data['profile_picture'];
                $types .= 's';
            }

            if (empty($setFields)) {
                return false;
            }

            $values[] = $userId;
            $types .= 'i';

            $sql = "UPDATE users SET " . implode(', ', $setFields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new \Exception($this->conn->error);
            }

            $stmt->bind_param($types, ...$values);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }
}
