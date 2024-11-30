<!-- <?php
session_start(); 
define("DEFINE_MY_ACCESS", true);
require_once 'config/database.php';

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = hash('sha256', $_POST['password']);
    
    $query = "SELECT u.*, f.nama as fakultas_nama 
              FROM users u 
              LEFT JOIN fakultas f ON u.fakultas_id = f.id 
              WHERE email = ? AND password = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fakultas_id'] = $user['fakultas_id'];
        $_SESSION['fakultas_nama'] = $user['fakultas_nama'];
        
        // Handle Remember Me
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->bind_param("si", $token, $user['id']);
            $stmt->execute();
            setcookie('remember_token', $token, time() + 30*24*60*60); // 30 days
        }
        
        // Update last login
        $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        header("Location: dashboard/");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Risk Management System</title>
    <link rel="stylesheet" href="./style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Poppins:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="container">
    <div class="header-top"></div>
    <div class="header">
        <div class="logo">
            <i class="fa fa-ge"></i>
        </div>
        <h2 class="logo">Risk 
        Management</h2>
    </div>

    <div class="form-box login">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                <input type="email" name="email" required placeholder="Email" />
            </div>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </span>
                <input type="password" name="password" required placeholder="Password" />
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" name="remember" />Remember me</label>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
</div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
