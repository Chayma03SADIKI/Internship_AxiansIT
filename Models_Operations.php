<?php
include("db.php");

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_model'])) {
        $model_name = $_POST['model_name'];
        $category_id = $_POST['model_category'];

        // Ajouter un nouveau modèle et créer sa table associée
        if (!empty($model_name) && !empty($category_id)) {
            // Vérifier si le modèle existe déjà
            $check_sql = "SELECT * FROM models WHERE model_name = ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param("s", $model_name);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    $error = "Model already exists!";
                } else {
                    // Insérer un nouveau modèle
                    $sql = "INSERT INTO models (model_name, category_id) VALUES (?, ?)";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("si", $model_name, $category_id);
                        if ($stmt->execute()) {
                            $model_id = $stmt->insert_id;

                            // Créer une nouvelle table pour le modèle
                            $create_table_sql = "CREATE TABLE `$model_name` (
                                device_id INT(11) NOT NULL AUTO_INCREMENT,
                                device_name VARCHAR(255) NOT NULL UNIQUE,
                                device_image TEXT DEFAULT NULL,
                                comment TEXT DEFAULT NULL,
                                model_id INT(11) NOT NULL,
                                PRIMARY KEY (device_id),
                                FOREIGN KEY (model_id) REFERENCES models(model_id) ON DELETE CASCADE
                            )";
                            if ($conn->query($create_table_sql) === TRUE) {
                                $message = "Model and table added successfully!";
                            } else {
                                $error = "Error creating table: " . $conn->error;
                            }
                        } else {
                            $error = "Error adding model: " . $stmt->error;
                        }
                    }
                }
            }
        } else {
            $error = "All fields are required!";
        }
    }

    // Modifier un modèle et son nom de table associé
    if (isset($_POST['edit_model'])) {
        $model_id = intval($_POST['model_id']);
        $model_name = htmlspecialchars(trim($_POST['model_name']));
        $category_id = intval($_POST['model_category']);
    
        if (!empty($model_id) && !empty($model_name) && !empty($category_id)) {
            // Vérifier si le nom du modèle existe déjà
            $sql = "SELECT COUNT(*) FROM models WHERE model_name = ? AND model_id != ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("si", $model_name, $model_id);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
    
                if ($count > 0) {
                    $error = "Model already exists!";
                } else {
                    // Récupérer l'ancien nom du modèle
                    $old_model_name = "";
                    $sql = "SELECT model_name FROM models WHERE model_id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("i", $model_id);
                        $stmt->execute();
                        $stmt->bind_result($old_model_name);
                        $stmt->fetch();
                        $stmt->close();
                    }
    
                    // Mettre à jour le modèle
                    $sql = "UPDATE models SET model_name = ?, category_id = ? WHERE model_id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("sii", $model_name, $category_id, $model_id);
                        if ($stmt->execute()) {
                            // Renommer la table associée
                            $rename_table_sql = "ALTER TABLE `$old_model_name` RENAME TO `$model_name`";
                            if ($conn->query($rename_table_sql) === TRUE) {
                                $message = "Model and table name updated successfully!";
                            } else {
                                $error = "Error renaming table: " . $conn->error;
                            }
                        } else {
                            $error = "Error updating model: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                }
            }
        } else {
            $error = "All fields are required!";
        }
    }
    

    // Supprimer un modèle et sa table associée
    if (isset($_POST['delete_model'])) {
        $model_id = intval($_POST['delete_id']);

        if (!empty($model_id)) {
            // Récupérer le nom du modèle avant de le supprimer
            $sql = "SELECT model_name FROM models WHERE model_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $model_id);
                $stmt->execute();
                $stmt->bind_result($model_name);
                $stmt->fetch();
                $stmt->close();

                // Supprimer le modèle
                $delete_sql = "DELETE FROM models WHERE model_id = ?";
                if ($delete_stmt = $conn->prepare($delete_sql)) {
                    $delete_stmt->bind_param("i", $model_id);
                    if ($delete_stmt->execute()) {
                        // Supprimer la table associée
                        $drop_table_sql = "DROP TABLE IF EXISTS `$model_name`";
                        if ($conn->query($drop_table_sql) === TRUE) {
                            $message = "Model and table deleted successfully!";
                        } else {
                            $error = "Error dropping table: " . $conn->error;
                        }
                    } else {
                        $error = "Error deleting model: " . $delete_stmt->error;
                    }
                }
            }
        } else {
            $error = "Invalid model ID!";
        }
    }
}
?>
