<?php
include("db.php");

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$model = isset($_GET['model']) ? mysqli_real_escape_string($conn, $_GET['model']) : '';
$category_name = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Get the category ID from the category name
$sql = "SELECT category_id FROM categories WHERE category_name = '$category_name'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $category_id = $row['category_id'];
        $models_to_search = [];
        if ($model && $model != 'All') {
            $models_to_search[] = $model;
        } else {
            $sql = "SELECT model_name FROM models WHERE category_id = $category_id";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $models_to_search[] = $row['model_name'];
                }
            } else {
                die("ERROR: " . mysqli_error($conn));
            }
        }
        echo "<table border='1'>";
        echo "<tr><th>Device Name</th><th>Model</th><th>Show details</th></tr>";
        $devices_found = false;
        foreach ($models_to_search as $model_name) {
            $table_name = mysqli_real_escape_string($conn, $model_name);
            if ($search) {
                $sql_devices = "SELECT * FROM `$table_name` WHERE ";
                $column_query = "SHOW COLUMNS FROM `$table_name`";
                $columns_result = mysqli_query($conn, $column_query);
                $conditions = [];
                while ($column = mysqli_fetch_assoc($columns_result)) {
                    $column_name = $column['Field'];
                    if (!in_array($column_name, ['device_id', 'device_image', 'model_id'])) {
                        $conditions[] = "`$column_name` LIKE '%$search%'";
                    }
                }
                $sql_devices .= implode(' OR ', $conditions);
            } else {
                $sql_devices = "SELECT * FROM `$table_name`";
            }
            $result_devices = mysqli_query($conn, $sql_devices);
            if ($result_devices) {
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
            } else {
                die("ERROR: " . mysqli_error($conn));
            }
        }
        if (!$devices_found) {
            echo "<tr><td colspan='3'>No devices found.</td></tr>";
        }
        echo "</table>";
    } else {
        die("No such category found.");
    }
} else {
    die("ERROR: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
