<?php
@session_start();
include('db.php');
use PHPMailer\PHPMailer\PHPMailer;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}

$admin_email = $_SESSION['admin'];
$response = ['error' => '', 'success' => ''];

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mohamirahh1999@gmail.com';
    $mail->Password = 'nwhebzhehdqvnikn';
    $mail->SMTPSecure = 'tsl';
    $mail->Port = 587;

    $mail->setFrom('mohamirahh1999@gmail.com', 'Admin');
    $mail->addAddress($to);
    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body    = $body;

    if(!$mail->send()) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 'Message has been sent';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_account'])) {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $family_name = mysqli_real_escape_string($conn, $_POST['family_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $image = '';

        if (!empty($_FILES['image']['name'])) {
            $target_dir = "admin_profile/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $target_file;
            } else {
                $response['error'] = 'Error uploading profile picture.';
            }
        }

        if (empty($response['error'])) {
            $update_sql = "UPDATE admin SET first_name = '$first_name', family_name = '$family_name', mail = '$email'";
            if (!empty($image)) {
                $update_sql .= ", image = '$image'";
            }
            $update_sql .= " WHERE mail = '$admin_email'";

            if (mysqli_query($conn, $update_sql)) {
                $_SESSION['admin'] = $email;
                $response['success'] = 'Account updated successfully';
            } else {
                $response['error'] = 'Error updating account.';
            }
        }
    } elseif (isset($_POST['confirm_change_admin'])) {
        $new_email = mysqli_real_escape_string($conn, $_POST['new_email']);
        // Check if the new admin already exists
        $check_sql = "SELECT * FROM admin WHERE mail = '$new_email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $response['error'] = 'An account with this email already exists';
        } else {
            // Delete the old admin
            $delete_sql = "DELETE FROM admin WHERE mail = '$admin_email'";
            if (mysqli_query($conn, $delete_sql)) {
                // Add the new admin
                $insert_sql = "INSERT INTO admin (mail, first_name, family_name) VALUES ('$new_email', 'Admin', 'Admin')";
                if (mysqli_query($conn, $insert_sql)) {
                    $_SESSION['admin'] = $new_email;
                    $response['success'] = 'Admin changed successfully';

                    // Send email to the new admin
                    $subject = 'You are the new admin';
                    $body = 'You have been assigned as the new admin. Please click the link below to reset your password:<br><a href="http://localhost/website/forgot_password.php">Forgot Password</a>';
                    sendEmail($new_email, $subject, $body);

                    // Expire the session
                    session_destroy();
                    header('Location: login_admin.php');
                    exit();
                } else {
                    $response['error'] = 'Error creating new admin';
                }
            } else {
                $response['error'] = 'Error deleting old admin';
            }
        }
    }
}

// Retrieve current admin details
$admin_sql = "SELECT * FROM admin WHERE mail = '$admin_email'";
$admin_result = mysqli_query($conn, $admin_sql);
$admin = mysqli_fetch_assoc($admin_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Management</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="container">
        <form action="account.php" method="POST" enctype="multipart/form-data">
            <h3>Update Account</h3>
            <?php if (!empty($response['error'])): ?>
                <div class="error"><?php echo $response['error']; ?></div>
            <?php endif; ?>
            <?php if (!empty($response['success'])): ?>
                <div class="success"><?php echo $response['success']; ?></div>
            <?php endif; ?>
            <div class="input-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($admin['first_name']); ?>" >
            </div>
            <div class="input-group">
                <label for="family_name">Family Name:</label>
                <input type="text" id="family_name" name="family_name" value="<?php echo htmlspecialchars($admin['family_name']); ?>" >
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['mail']); ?>" >
            </div>
            <div class="input-group">
                <label for="image">Profile Picture:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="update_account">Update</button>
        </form>

        <form action="account.php" method="POST" onsubmit="return showChangeAdminModal();">
            <h3>Change Admin</h3>
            <div class="input-group">
                <label for="new_email">New Email:</label>
                <input type="email" id="new_email" name="new_email" required>
            </div>
            <button type="submit" name="change_admin">Change Admin</button>
        </form>

        <!-- Modal for change admin confirmation -->
        <div id="changeAdminModal" class="modal">
            <div class="modal-content">
                <p>Your session will expire, and you will no longer be able to access the dashboard.</p>
                <p>It is really important to make sure the new admin email is correct.</p>
                <div class="button-container">
                    <button id="cancelBtn">Cancel</button>
                    <form id="confirmChangeAdminForm" action="account.php" method="POST" style="display: inline;">
                        <input type="hidden" id="confirm_new_email" name="new_email">
                        <button id="confirmBtn" type="submit" name="confirm_change_admin">OK</button>
                    </form>
                </div>
            </div>
        </div>

    <script>
        function showChangeAdminModal() {
            var newEmail = document.getElementById('new_email').value;
            document.getElementById('confirm_new_email').value = newEmail;
            var modal = document.getElementById('changeAdminModal');
            modal.style.display = "block";
            return false;
        }

        document.getElementById('cancelBtn').onclick = function() {
            var modal = document.getElementById('changeAdminModal');
            modal.style.display = "none";
        }
    </script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;

        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;  /* Réduire la largeur de la fenêtre modale */
            border-radius: 20px;
        }
        .modal-content p {
            margin-bottom: 20px;
        }
        .modal-content button {
            margin-right: 10px;
            padding: 10px 5px;
            cursor: pointer;
        }

        .modal-content button#cancelBtn {
            background-color: transparent;  /* Bouton Cancel sans couleur de fond */
            color: black;
            border: 1px solid #888; /* Bordure grise */
        }

        .modal-content button#confirmBtn {
            background-color: green;  /* Bouton OK en vert */
            color: white;
            border: none; /* Aucune bordure pour le bouton OK */
        }

        .button-container {
            display: flex;
            justify-content: flex-end;  /* Alignement des boutons à droite */
        }
    </style>

</style>
</body>
</html>
