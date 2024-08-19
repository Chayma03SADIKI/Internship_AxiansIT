<?php
include('db.php');
use PHPMailer\PHPMailer\PHPMailer;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the admin table
    $sql = "SELECT * FROM admin WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Save the token in the database with an expiration date of 10 minutes
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));
        $sql = "UPDATE admin SET reset_token = ?, reset_token_expiry = ? WHERE mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send the email with PHPMailer
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mohamirahh1999@gmail.com';
        $mail->Password = 'nwhebzhehdqvnikn'; //password double step verification
        $mail->SMTPSecure = 'tls'; // ou 'ssl'
        $mail->Port = 587; // ou 465 pour 'ssl'
        
        $mail->SMTPDebug = 0; // Désactiver le débogage

        $mail->setFrom('mohamirahh1999@gmail.com', 'noreply');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Click <a href='http://localhost/website/reset_password.php?token=$token'>here</a> to reset your password. This link will expire in 10 minutes.";

        if ($mail->send()) {
            $message = 'Password reset link has been sent to your email.';
        } else {
            $message = 'Mailer Error: ' . $mail->ErrorInfo;
        }
    } else {
        $message = 'No account found with that email.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="forgot_password.css">
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <form action="forgot_password.php" method="POST">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Submit</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
