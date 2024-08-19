<?php
include '../db.php';

if (isset($_POST['model_id']) && isset($_POST['old_parameter_name']) && isset($_POST['new_parameter_name'])) {
    $model_id = intval($_POST['model_id']);
    $old_parameter_name = $_POST['old_parameter_name'];
    $new_parameter_name = $_POST['new_parameter_name'];

    // Vérifier la longueur du nom du paramètre
    if (strlen($new_parameter_name) > 64) {
        echo json_encode(["error" => "Parameter's name is too long"]);
        exit();
    }

    // Vérifier si le paramètre existe déjà pour ce modèle
    $model_name_query = "SELECT model_name FROM models WHERE model_id = ?";
    $stmt = $conn->prepare($model_name_query);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $stmt->bind_result($model_name);
    $stmt->fetch();
    $stmt->close();

    if ($model_name) {
        // Convertir les noms des paramètres pour correspondre au format dans la base de données
        $old_parameter_name_db = str_replace(' ', '_', $old_parameter_name);
        $new_parameter_name_db = str_replace(' ', '_', $new_parameter_name);

        // Modifier le nom de la colonne dans la table associée au modèle
        $alter_table_sql = "ALTER TABLE `$model_name` CHANGE `$old_parameter_name_db` `$new_parameter_name_db` VARCHAR(255)";
        if ($conn->query($alter_table_sql) === TRUE) {
            echo json_encode(["success" => "Parameter name updated successfully"]);
        } else {
            echo json_encode(["error" => "Error updating parameter name in model's table", "sql_error" => $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Model not found"]);
    }
} else {
    echo json_encode(["error" => "Invalid input"]);
}
?>
