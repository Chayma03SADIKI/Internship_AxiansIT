<?php
include("db.php");

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {
    $category_name = $_POST["category_name"];
    $comments = isset($_POST["comments"]) ? $_POST["comments"] : null;
    $category_image = null;

    // Vérifier si la catégorie existe déjà
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Category already exists!";
    } else {
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $target_dir = "equipments_images/";
            $target_file = $target_dir . basename($_FILES["category_image"]["name"]);

            if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
                $category_image = $target_file;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO categories (category_name, category_image, comments) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $category_name, $category_image, $comments);
            if ($stmt->execute()) {
                $message = "New category added successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_category"])) {
    $category_id = $_POST["category_id"];
    $category_name = $_POST["category_name"];
    $comments = isset($_POST["comments"]) ? $_POST["comments"] : null;
    $category_image = $_FILES['category_image']['error'] == 0 ? $_FILES['category_image']['name'] : $_POST['existing_category_image'];

    // Vérifier si la catégorie existe déjà avec un ID différent
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ? AND category_id != ?");
    $stmt->bind_param("si", $category_name, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Category already exists!";
    } else {
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $target_dir = "equipments_images/";
            $target_file = $target_dir . basename($_FILES["category_image"]["name"]);

            if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
                $category_image = $target_file;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $category_image = $_POST['existing_category_image'];
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_image = ?, comments = ? WHERE category_id = ?");
            $stmt->bind_param("sssi", $category_name, $category_image, $comments, $category_id);

            if ($stmt->execute()) {
                $message = "Category updated successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete_id"])) {
    $category_id = $_GET["delete_id"];

    // Récupérer les noms des modèles associés à la catégorie
    $stmt = $conn->prepare("SELECT model_name FROM models WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Supprimer les tables associées aux modèles
    while ($row = $result->fetch_assoc()) {
        $model_table_name = $row['model_name'];
        $drop_table_stmt = $conn->prepare("DROP TABLE IF EXISTS `$model_table_name`");
        $drop_table_stmt->execute();
        $drop_table_stmt->close();
    }
    $stmt->close();

    // Supprimer les modèles associés
    $stmt = $conn->prepare("DELETE FROM models WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->close();

    // Supprimer la catégorie
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    if ($stmt->execute()) {
        $message = "Category and associated models deleted successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

?>