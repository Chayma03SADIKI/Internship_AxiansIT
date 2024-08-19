<?php
include('db.php');
@session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}

// Query to get the admin information
$sql = "SELECT family_name, first_name, image FROM admin LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
    $family_name = $admin['family_name'];
    $first_name = $admin['first_name'];
    $image = $admin['image'];
} else {
    // Default values if no admin found
    $family_name = "Admin";
    $first_name = "Name";
    $image = "image/blank_profile.jpeg";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link rel="stylesheet" href="side_bar.css">
</head>
<body>
<div id="sidebar" class="sidebar">
    <div class="content">
        <div class="logo">
            <img src="image/logo_removed bg.png" alt="Logo" class="logo-image">
        </div>
        <div id="profile" class="profile">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="Avatar user" class="avatar">
            <div class="text">
                <h2 class="name"><?php echo htmlspecialchars($first_name . ' ' . $family_name); ?></h2>
                <p class="role">Administrator</p>
            </div>
        </div>
        <div id="menu" class="menu">
            <div class="menu-section">
                <div class="section-title">Account</div>
                <a href="admin_dashboard.php?page=account" class="menu-item">
                    <img src="icones/account.svg" alt="Account Icon">
                    <span><strong>My account</strong></span>
                </a>
                <a href="admin_dashboard.php?page=logout" class="menu-item">
                    <img src="icones/log_out.svg" alt="Log Out Icon">
                    <span><strong>Log out</strong></span>
                </a>
            </div>
            <div class="menu-section">
                <div class="section-title">Settings</div>
                <a href="admin_dashboard.php?page=categories" class="menu-item">
                    <img src="icones/category.svg" alt="Categories Icon">
                    <span><strong>Categories</strong></span>
                </a>
                <a href="admin_dashboard.php?page=models" class="menu-item">
                    <img src="icones/model.svg" alt="Models Icon">
                    <span><strong>Models</strong></span>
                </a>
                <a href="admin_dashboard.php?page=devices" class="menu-item">
                    <img src="icones/device.svg" alt="Devices Icon">
                    <span><strong>Devices</strong></span>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
