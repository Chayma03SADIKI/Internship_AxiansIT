<?php
include("db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devices</title>
    <link rel="stylesheet" href="find_devices.css">
    <script src="search_devices.js" defer></script>
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

    <div id="controls">

        <a href="categories.php" id="go-back-button">
            <img src="icones/go_back.svg" alt="Go Back" id="go-back-icon">
            <style>
                #go-back-button {
                    margin-right: 20px; 
                }

                #go-back-icon {
                    width: 30px; 
                    height: 30px;
                }
            </style>
        </a>
        <div id="category-display">
            <?php
            if (isset($_GET['category'])) {
                $category_name = htmlspecialchars($_GET['category']);
                echo "<h2>Category: $category_name</h2>";
            } else {
                echo "<h2>Select a Category</h2>";
            }
            ?>
        </div>

        <select id="model-dropdown">
            <option value="" selected disabled>Select a Model</option>
            <option value="All">All</option>
            <?php
            if (isset($_GET['category'])) {
                $category_name = htmlspecialchars($_GET['category']);

                // Get the category ID from the category name
                $sql = "SELECT category_id FROM categories WHERE category_name = '$category_name'";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("ERROR: " . mysqli_error($conn));
                }

                $row = mysqli_fetch_assoc($result);
                if (!$row) {
                    die("No such category found.");
                }

                $category_id = $row['category_id'];

                // Fetch models associated with the category
                $sql = "SELECT model_name FROM models WHERE category_id = $category_id";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $model_name = htmlspecialchars($row['model_name']);
                        echo "<option value='$model_name'>$model_name</option>";
                    }
                } else {
                    echo "<option value='' disabled>Error loading models</option>";
                }
            }
            ?>
        </select>


        <input type="text" id="search-bar" placeholder="Search devices...">
    </div>

    <div id="device-table">
        <?php
        if (isset($_GET['category'])) {
            $category_name = htmlspecialchars($_GET['category']);

            // Get the category ID from the category name
            $sql = "SELECT category_id FROM categories WHERE category_name = '$category_name'";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("ERROR: " . mysqli_error($conn));
            }

            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                die("No such category found.");
            }

            $category_id = $row['category_id'];

            // Check if a model is selected
            $selected_model = isset($_GET['model']) ? htmlspecialchars($_GET['model']) : '';

            // Get the models associated with this category
            $sql_models = "SELECT model_name FROM models WHERE category_id = $category_id";
            $result_models = mysqli_query($conn, $sql_models);

            if (!$result_models) {
                die("ERROR: " . mysqli_error($conn));
            }

            echo "<table border='1'>";
            echo "<tr><th>Device Name</th><th>Model</th><th>Show details</th></tr>";

            $devices_found = false;
            while ($row = mysqli_fetch_assoc($result_models)) {
                $model_name = $row['model_name'];

                // Skip models that are not selected
                if ($selected_model && $selected_model != 'All' && $selected_model != $model_name) {
                    continue;
                }

                // Use the model name to filter the devices
                $table_name = mysqli_real_escape_string($conn, $model_name);
                $sql_devices = "SELECT * FROM `$table_name`";
                $result_devices = mysqli_query($conn, $sql_devices);

                if (!$result_devices) {
                    die("ERROR: " . mysqli_error($conn));
                }

                while ($device_row = mysqli_fetch_assoc($result_devices)) {
                    $devices_found = true;
                    $device_name = htmlspecialchars($device_row['device_name']);
                    $device_id = $device_row['device_id'];
                    echo "<tr>
                            <td>$device_name</td>
                            <td>$model_name</td>
                            <td><a href='find_details.php?model=$model_name&device_id=$device_id'>Show details</a></td>
                          </tr>";
                }
            }

            if (!$devices_found) {
                echo "<tr><td colspan='3'>No devices found.</td></tr>";
            }

            echo "</table>";
        } else {
            echo "No category selected.";
        }

        mysqli_close($conn);
        ?>
    </div>

</body>
</html>
