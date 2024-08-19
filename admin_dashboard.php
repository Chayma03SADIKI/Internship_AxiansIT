<?php
include('db.php');


session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard_Categories';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMR6zXsZ71/A1gxREpI7o3uh5bOEzjlM2B4+frs" crossorigin="anonymous">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div id="view">
        <?php
        // Rediriger vers login_admin.php si la page est 'logout'
        if ($page === 'logout') {
            header('Location: logout.php');
            exit();
        }
        include 'side_bar.php';
        
        // Inclure la page en fonction du paramètre 'page'
        switch ($page) {
            case 'account':
                include 'account.php';
                break;
            case 'models':
                include 'dashboard_Models.php';
                break;
            case 'devices':
                include 'dashboard_Devices.php';
                break;
            case 'account':
                include 'account.php';
                break;
            default:
                include 'dashboard_Categories.php';
                break;
        }
        ?>
    </div>

    <script src="dashboard_Categories.js"></script>
    <script src="dashboard_Models.js"></script>
    <script>
        <?php if (!empty($message) || !empty($error)): ?>
            var modalMessage = "<?php echo !empty($message) ? $message : $error; ?>";
            document.getElementById('modalMessage').innerText = modalMessage;
            document.getElementById('messageModal').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>
