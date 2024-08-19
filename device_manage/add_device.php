<?php
include '../db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $model_name = $_POST['model_name'];
    $device_name = $_POST['device_name'];
    $device_image = null;

    // Valider les entrées
    if (empty($model_name) || empty($device_name)) {
        echo json_encode(["error" => "model name and  device name are required."]);
        exit();
    }

    // Récupérer le model_id à partir de la table models
    $stmt = $conn->prepare("SELECT model_id FROM models WHERE model_name = ?");
    $stmt->bind_param("s", $model_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "Model not found."]);
        exit();
    }

    $model_row = $result->fetch_assoc();
    $model_id = $model_row['model_id'];

    // Vérifier si le device existe déjà
    $stmt = $conn->prepare("SELECT * FROM `$model_name` WHERE device_name = ?");
    $stmt->bind_param("s", $device_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["error" => "Device name already exists !"]);
        exit();
    }

    // Gestion du téléchargement de l'image
    if (isset($_FILES['device_image']) && $_FILES['device_image']['error'] == 0) {
        $target_dir = "devices_image/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["device_image"]["name"]);

        if (!move_uploaded_file($_FILES["device_image"]["tmp_name"], $target_file)) {
            echo json_encode(["error" => "Sorry, an error occurred while uploading your file."]);
            exit();
        }

        $device_image = $target_file;
    }

    // Construire la requête SQL pour insérer le nouveau device
    $sql = "INSERT INTO `$model_name` (`device_name`, `model_id`";
    $values = " VALUES (?, ?";
    $params = [$device_name, $model_id];
    $types = "si";

    if ($device_image) {
        $sql .= ", `device_image`";
        $values .= ", ?";
        $params[] = $device_image;
        $types .= "s";
    }

    foreach ($_POST as $key => $value) {
        if ($key != 'model_name' && $key != 'device_name' && !empty($value)) {
            $escaped_key = $conn->real_escape_string($key);
            $sql .= ", `$escaped_key`";
            $values .= ", ?";
            $params[] = $value;
            $types .= "s";
        }
    }

    $sql .= ")";
    $values .= ")";
    $sql .= $values;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        echo json_encode(["error" => "Error adding the device.", "sql_error" => $stmt->error]);
        exit();
    }

    echo json_encode(["success" => "New device added successfully."]);
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>
