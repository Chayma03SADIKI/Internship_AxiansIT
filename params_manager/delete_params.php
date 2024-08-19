<?php
include '../db.php';

if (isset($_POST['model_id']) && isset($_POST['parameter_name'])) {
    $model_id = intval($_POST['model_id']);
    $parameter_name = $_POST['parameter_name'];

    // Vérifier la longueur du nom du paramètre
    if (strlen($parameter_name) > 64) {
        echo json_encode(["error" => "Parameter's name is too long"]);
        exit();
    }

    // Récupérer le nom du modèle
    $model_name_query = "SELECT model_name FROM models WHERE model_id = ?";
    $stmt = $conn->prepare($model_name_query);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $stmt->bind_result($model_name);
    $stmt->fetch();
    $stmt->close();

    if ($model_name) {
        // Convertir le nom du paramètre pour correspondre au format dans la base de données
        $parameter_name_db = str_replace(' ', '_', $parameter_name);

        // Supprimer la colonne de la table associée au modèle
        $alter_table_sql = "ALTER TABLE `$model_name` DROP COLUMN `$parameter_name_db`";
        if ($conn->query($alter_table_sql) === TRUE) {
            echo json_encode(["success" => "Parameter deleted successfully"]);
        } else {
            echo json_encode(["error" => "Error deleting parameter from model's table", "sql_error" => $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Model not found"]);
    }
} else {
    echo json_encode(["error" => "Invalid input"]);
}
?>
