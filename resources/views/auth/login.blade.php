<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Risk Management System</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/login.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body>
    <!-- Animated Background -->
    <div class="bg-pattern"></div>

    <!-- Corner Logo -->
    <div class="corner-logo">
        <img src="assets/img/logo.png" alt="UINSUKA Logo">
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Left Info Panel -->
        <div class="info-panel">
            <div class="logo-container">
                <i class="fa-solid fa-shield-halved"></i>
                <h2>Risk Management</h2>
                <p>Sistem Manajemen Risiko UIN Sunan Kalijaga</p>
            </div>
        </div>

        <!-- Login Form Panel -->
        <div class="login-panel">
            <div class="form-header">
                <h2>Login</h2>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
            <div class="error-message">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Email atau password salah!</span>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= $_SESSION['base_uri'] ?>/login">
                <div class="input-group">
                    <input type="email" name="email" required placeholder="Email">
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password" required placeholder="Password">
                    <i class="fa-solid fa-lock"></i>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="togglePassword"></i>
                    </span>
                </div>

                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" name="login" class="login-btn">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </button>
            </form>
        </div>
    </div>
    <script src="assets/js/particles.js"></script>
    <script src="assets/js/password.js"></script>
</body>
</html>