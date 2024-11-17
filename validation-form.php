<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Validasi Form dengan PHP</title>
  <link rel="stylesheet" href="style/valform-style.css"> 
</head>
<body>
  <div class="form-container">
    <h1>Form Registrasi</h1>

    <?php
  
    $name = $email = $password = $confirmPassword = "";
    $nameError = $emailError = $passwordError = $confirmPasswordError = "";
    $successMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $email = $password = $confirmPassword = "";
        $nameError = $emailError = $passwordError = $confirmPasswordError = "";
        $successMessage = "";
    

        if (empty($_POST["name"])) {
            $nameError = "Nama harus diisi.";
        } else {
            $name = $_POST["name"];
        }
    
  
        if (empty($_POST["email"])) {
            $emailError = "Email harus diisi.";
        } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $emailError = "Format email tidak valid.";
        } else {
            $email = $_POST["email"];
        }
    

        if (empty($_POST["password"])) {
            $passwordError = "Password harus diisi.";
        } else {
            $password = $_POST["password"];
        }
    

        if (empty($_POST["confirmPassword"])) {
            $confirmPasswordError = "Konfirmasi password harus diisi.";
        } elseif ($_POST["confirmPassword"] !== $password) {
            $confirmPasswordError = "Password tidak cocok.";
        } else {
            $confirmPassword = $_POST["confirmPassword"];
        }

        if (empty($nameError) && empty($emailError) && empty($passwordError) && empty($confirmPasswordError)) {
            $successMessage = "Form berhasil dikirim!";
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label for="name">Nama Lengkap:</label>
      <input type="text" id="name" name="name" value="<?php echo $name; ?>">
      <div class="error"><?php echo $nameError; ?></div>

      <label for="email">Email:</label>
      <input type="text" id="email" name="email" value="<?php echo $email; ?>">
      <div class="error"><?php echo $emailError; ?></div>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password">
      <div class="error"><?php echo $passwordError; ?></div>

      <label for="confirmPassword">Konfirmasi Password:</label>
      <input type="password" id="confirmPassword" name="confirmPassword">
      <div class="error"><?php echo $confirmPasswordError; ?></div>

      <button type="submit">Daftar</button>
    </form>

    <?php
    if ($successMessage) {
      echo "<p class='success'>$successMessage</p>";
    }
    ?>
  </div>
</body>
</html>
