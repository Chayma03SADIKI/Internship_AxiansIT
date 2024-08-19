<?php
include('db.php');
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Verify the token and expiry
    $sql = "SELECT * FROM admin WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the password
        $sql = "UPDATE admin SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $token);
        $stmt->execute();

        echo '<div class="success-message">Password has been reset successfully.</div>';
        header('Location: login_admin.php');
        exit();  // Make sure to call exit after header to stop further script execution
    } else {
        $error_message = 'Invalid or expired token.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form id="reset-password-form" action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?= isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '' ?>">
            <div class="input-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="checkbox" id="show-password"> Show Password
            </div>
            <div class="error-message" id="password-error"></div>
            <button type="submit">Reset Password</button>
        </form>
    </div>

    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });

        document.getElementById('reset-password-form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const passwordError = document.getElementById('password-error');
            const passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*?])(?=.{8,})/;

            if (!passwordRegex.test(password)) {
                passwordError.textContent = 'Password must be at least 8 characters long, contain at least one number, and one special character.';
                event.preventDefault();
            } else {
                passwordError.textContent = '';
            }
        });
    </script>
</body>
</html>
