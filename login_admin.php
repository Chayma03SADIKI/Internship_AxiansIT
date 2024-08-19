<?php
session_start();
include('db.php'); // Inclure le fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Éviter l'injection SQL
    $email = mysqli_real_escape_string($conn, $email);

    // Récupérer les détails de l'admin dans la base de données
    $sql = "SELECT * FROM admin WHERE mail = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Vérifier le mot de passe
        if (password_verify($password, $admin['password'])) {
            // Définir les variables de session
            $_SESSION['admin'] = $admin['mail'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = "Email or password incorrect !";
        }
    } else {
        $error = "Email or password incorrect !";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="login_admin.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <section id="header">
        <a href="#"><img src="image/logo_removed bg.png" class="logo" alt="Logo"></a>
        <div>
            <ul id="navbar">
                <li><a href="index.html">Home</a></li>
                <li><a class="active" href="login_admin.php">Admin</a></li>
                <li><a href="About.html">About</a></li>
                <li><a href="Contact.html">Contact</a></li>
            </ul>
        </div>
    </section>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="login_admin.php" method="POST">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i class="far fa-eye" id="togglePassword"></i>
                </div>
            </div>
            <div class="input-group">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
