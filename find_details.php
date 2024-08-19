<?php
include("db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Details</title>
    <link rel="stylesheet" href="find_details.css">
</head>
<body>
    <section id="header">
        <a href="#"><img src="image/logo_removed bg.png" class="logo" alt="Logo"></a>
        <div>
            <ul id="navbar">
                <li><a href="index.html">Home</a></li>
                <li><a href="login_admin.php">Admin</a></li>
                <li><a href="About.html">About</a></li>
                <li><a href="Contact.html">Contact</a></li>
            </ul>
        </div>
    </section>

    <div class="container">
        <div id="presentation">
            <?php
            if (isset($_GET['model']) && isset($_GET['device_id'])) {
                $model_name = htmlspecialchars($_GET['model']);
                $device_id = intval($_GET['device_id']);

                // Get the device details from the table named after the model
                $sql = "SELECT * FROM `$model_name` WHERE device_id = $device_id";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("ERROR: " . mysqli_error($conn));
                }

                $row = mysqli_fetch_assoc($result);
                if (!$row) {
                    die("No such device found.");
                }

                $device_name = htmlspecialchars($row['device_name']);
                $device_image = htmlspecialchars($row['device_image']);
                echo "<img src='device_manage/$device_image' alt='$device_name'>";
                echo "<div class='device-info'>";
                echo "<h1>$device_name</h1>";
                echo "<h2>Model: $model_name</h2>";
                echo "</div>";
            } else {
                echo "No device selected.";
            }
            ?>
        </div>

        <div class="device-details">
            <?php
            if (isset($row)) {
                echo "<table>";

                foreach ($row as $key => $value) {
                    if ($key !== 'device_id' && $key !== 'model_id' && $key !== 'device_image') {
                        $key = ucwords(str_replace('_', ' ', htmlspecialchars($key)));
                        $value = nl2br(htmlspecialchars($value)); // Preserve line breaks
                        echo "<tr><th>$key</th><td>$value</td></tr>";
                    }
                }

                echo "</table>";
            }
            ?>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>
