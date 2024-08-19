<?php
include '../db.php';

header('Content-Type: application/json');

if (isset($_GET['model_name']) && isset($_GET['device_id'])) {
    $model_name = $_GET['model_name'];
    $device_id = $_GET['device_id'];

    // Récupérer les informations de l'appareil spécifique
    $sql = "SELECT * FROM `$model_name` WHERE device_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Device not found"]);
        exit();
    }

    $device_data = $result->fetch_assoc();
    
    // Récupérer les colonnes de la table du modèle
    $sql = "SHOW COLUMNS FROM `$model_name`";
    $columns_result = $conn->query($sql);

    if ($columns_result->num_rows === 0) {
        echo json_encode(["error" => "No columns found for the table of this model."]);
        exit();
    }

    $fields = [];
    while ($column = $columns_result->fetch_assoc()) {
        $column_name = $column['Field'];
        $field_value = isset($device_data[$column_name]) ? $device_data[$column_name] : '';
        
        // Ajouter les champs à la liste
        $fields[] = [
            "name" => str_replace('_', ' ', ucfirst($column_name)),
            "column_name" => $column_name,
            "value" => $field_value
        ];
    }

    echo json_encode(["fields" => $fields]);
} else {
    echo json_encode(["error" => "Missing parameters."]);
}
?>
