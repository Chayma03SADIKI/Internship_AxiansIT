<?php
include '../db.php';

// Vérifier si les données POST sont définies
if (isset($_POST['model_id']) && isset($_POST['parameter_name'])) {
    $model_id = intval($_POST['model_id']);
    $parameter_name = $_POST['parameter_name'];

    // Vérifier la longueur du nom du paramètre
    if (strlen($parameter_name) > 64) {
        echo json_encode(["error" => "Parameter's name is too long"]);
        exit();
    }

    // Récupérer le nom de la table associée au modèle
    $model_name_query = "SELECT model_name FROM models WHERE model_id = ?";
    $stmt = $conn->prepare($model_name_query);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $stmt->bind_result($model_name);
    $stmt->fetch();
    $stmt->close();

    if ($model_name) {
        // Échapper les noms de table pour éviter les problèmes de sécurité SQL
        $table_name_escaped = $conn->real_escape_string($model_name);

        // Remplacer les espaces par des underscores dans le nom du paramètre
        $parameter_name_modified = str_replace(' ', '_', $parameter_name);
        $parameter_name_escaped = $conn->real_escape_string($parameter_name_modified);

        // Ajouter la colonne à la table associée au modèle
        $alter_table_sql = "ALTER TABLE `$table_name_escaped` ADD COLUMN `$parameter_name_escaped` TEXT";
        if ($conn->query($alter_table_sql) === TRUE) {
            echo json_encode(["success" => "Parameter added successfully"]);
        } else {
            echo json_encode(["error" => "Error adding parameter as column in model's table", "sql_error" => $conn->error, "query" => $alter_table_sql]);
        }
    } else {
        echo json_encode(["error" => "Model not found"]);
    }
} else {
    echo json_encode(["error" => "Invalid input"]);
}
?>
