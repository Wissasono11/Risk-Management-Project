<?php
namespace App\Http\Controllers;

use App\Services\UserService;

class UsersController
{
    private $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    public function index()
    {
        // Cek login & role
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        if ($_SESSION['user_role'] !== 'admin') {
            echo "Access Denied: not admin";
            return;
        }

        // Ambil semua user
        $users = $this->service->getAllUsers();

        // Global var
        global $usersList;
        $usersList = $users;

        // Tampilkan view
        require __DIR__ . '/../../../resources/views/users/index.blade.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        // butuh data fakultas?
        $conn = \Config\Database::getInstance()->getConnection();
        $faks = $conn->query("SELECT * FROM fakultas ORDER BY nama");
        global $fakultasList;
        $fakultasList = $faks ? $faks->fetch_all(\MYSQLI_ASSOC) : [];

        require __DIR__ . '/../../../resources/views/users/create.blade.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $email       = $_POST['email'] ?? '';
        $plainPass   = $_POST['password'] ?? '';
        $role        = $_POST['role'] ?? 'fakultas';
        $fakultas_id = null;
        if ($role === 'fakultas') {
            $fakultas_id = (int)($_POST['fakultas_id'] ?? 0);
        }
        $passwordHash = hash('sha256', $plainPass);

        $data = [
            'email'     => $email,
            'password'  => $passwordHash,
            'role'      => $role,
            'fakultas_id' => $fakultas_id
        ];

        $this->service->createUser($data);

        header('Location: ' . $_SESSION['base_uri'] . '/users');
        exit();
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No user id specified";
            return;
        }

        $user = $this->service->findUserById($id);
        if (!$user) {
            echo "User not found";
            return;
        }

        // fakultas list
        $conn = \Config\Database::getInstance()->getConnection();
        $faks = $conn->query("SELECT * FROM fakultas ORDER BY nama");
        global $fakultasList;
        $fakultasList = $faks ? $faks->fetch_all(\MYSQLI_ASSOC) : [];

        global $userEdit;
        $userEdit = $user;

        require __DIR__ . '/../../../resources/views/users/edit.blade.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }

        $id          = $_POST['id'] ?? null;
        $email       = $_POST['email'] ?? '';
        $role        = $_POST['role'] ?? 'fakultas';
        $fakultas_id = ($role === 'fakultas') ? (int)$_POST['fakultas_id'] : null;
        $plainPass   = $_POST['password'] ?? '';

        $data = [
            'email'     => $email,
            'role'      => $role,
            'fakultas_id' => $fakultas_id
        ];

        if (!empty($plainPass)) {
            $data['password'] = hash('sha256', $plainPass);
        }

        $this->service->updateUser($id, $data);

        header('Location: ' . $_SESSION['base_uri'] . '/users');
        exit();
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . $_SESSION['base_uri'] . '/login');
            exit();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "No user id specified for delete";
            return;
        }
        $this->service->deleteUser($id);

        header('Location: ' . $_SESSION['base_uri'] . '/users');
        exit();
    }
}
