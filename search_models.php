<?php
include 'db.php';

// Échapper la valeur de la recherche pour éviter les injections SQL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Construction de la requête SQL
$sql = "SELECT models.model_id, models.model_name, categories.category_id, categories.category_name
        FROM models
        JOIN categories ON models.category_id = categories.category_id";

// Ajouter la clause WHERE si une recherche est effectuée
if (!empty($search)) {
    $sql .= " WHERE models.model_name LIKE '%$search%'";
}

// Ajouter la clause ORDER BY pour trier les résultats
$sql .= " ORDER BY categories.category_name, models.model_name";

$result = $conn->query($sql);

// Affichage des résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["model_name"]) . "</td>
                <td>" . htmlspecialchars($row["category_name"]) . "</td>
                <td class='operations'>
                    <button class='btn' onclick=\"openEditModal('" . htmlspecialchars($row["model_id"]) . "', '" . htmlspecialchars($row["model_name"]) . "', '" . htmlspecialchars($row["category_id"]) . "')\">✏️</button>
                    <button class='btn' onclick=\"openParamsModal('" . htmlspecialchars($row["model_id"]) . "', '" . htmlspecialchars($row["model_name"]) . "')\">⚙️</button>
                    <form method='POST' action='admin_dashboard.php?page=models' style='display:inline;'>
                        <input type='hidden' name='delete_id' value='" . htmlspecialchars($row["model_id"]) . "'>
                        <input type='hidden' name='delete_model' value='1'>
                        <button type='submit' class='btn' onclick='return confirmDelete()'>🗑️</button>
                    </form>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No models found</td></tr>";
}
?>
