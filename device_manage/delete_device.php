<?php
include '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $model_name = $data['model_name'];
    $device_id = $data['device_id'];

    if (empty($model_name) || empty($device_id)) {
        echo json_encode(['error' => 'Model name and device ID are required']);
        exit;
    }

    $model_name = $conn->real_escape_string($model_name);
    $device_id = (int)$device_id; // Ensure device ID is an integer

    // Prepare the query to delete the device
    $query = "DELETE FROM `" . $model_name . "` WHERE device_id = $device_id";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => 'Device deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting device: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
