<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $model_name = $data['model_name'];

    if (empty($model_name)) {
        echo json_encode(['error' => 'Model name is required']);
        exit;
    }

    $model_name = $conn->real_escape_string($model_name);

    // Validate if the model table exists
    $result = $conn->query("SHOW TABLES LIKE '$model_name'");
    if ($result->num_rows == 0) {
        echo json_encode(['error' => 'Model table does not exist']);
        exit;
    }

    $query = "SELECT * FROM `" . $model_name . "` ORDER BY device_name ASC";
    $result = $conn->query($query);

    if ($result) {
        $devices = [];
        while ($row = $result->fetch_assoc()) {
            $devices[] = $row;
        }
        echo json_encode(['devices' => $devices]);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
