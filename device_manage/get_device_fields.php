<?php
include '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $model_name = $data['model_name'];

    $model_name = $conn->real_escape_string($model_name);

    $query = "DESCRIBE `$model_name`";
    $result = $conn->query($query);

    if ($result) {
        $fields = [];
        while ($row = $result->fetch_assoc()) {
            $fields[] = [
                'name' => $row['Field'],
                'label' => ucwords(str_replace('_', ' ', $row['Field']))
            ];
        }
        echo json_encode(['fields' => $fields]);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
