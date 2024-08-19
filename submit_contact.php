<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize it
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_SPECIAL_CHARS);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Recipient's email address
    $to = "mohamirahh1999@gmail.com";

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'mohamirahh1999@gmail.com'; // SMTP username
        $mail->Password = 'nwhebzhehdqvnikn'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('mohamirahh1999@gmail.com', 'AXIANS Solution');
        $mail->addAddress($to); // Add a recipient
        $mail->addReplyTo($email); // Add the sender's email as the reply-to address

        // Content
        $mail->isHTML(false); // Set email format to plain text
        $mail->Subject = "Contact Form Submission: " . $subject;
        $mail->Body    = "You have received a new message from the contact form:\n\n" .
                         "Message:\n$message\n\n" .
                         "Answer in this mail: $email";

        // Send the email
        $mail->send();
        $response = ['message' => 'Message sent successfully!'];
    } catch (Exception $e) {
        $response = ['message' => "Failed to send message. Mailer Error: {$mail->ErrorInfo}"];
    }
} else {
    $response = ['message' => 'Invalid request.'];
}

echo json_encode($response);
?>
