<?php
include '../db.php';

if (isset($_GET['model_id'])) {
    $model_id = intval($_GET['model_id']);

    // Récupérer le nom du modèle
    $model_name_query = "SELECT model_name FROM models WHERE model_id = ?";
    $stmt = $conn->prepare($model_name_query);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $stmt->bind_result($model_name);
    $stmt->fetch();
    $stmt->close();

    if ($model_name) {
        // Paramètres par défaut
        $default_parameters = ["device_id", "device_name", "device_image", "comment", "model_id"];

        // Récupérer les colonnes de la table associée au modèle
        $columns_query = "SHOW COLUMNS FROM `$model_name`";
        $result = $conn->query($columns_query);

        $parameters = [];
        while ($row = $result->fetch_assoc()) {
            if (!in_array($row['Field'], $default_parameters)) {
                // Remplacer les underscores par des espaces pour l'affichage
                $parameter_display_name = str_replace('_', ' ', $row['Field']);
                $parameters[] = $parameter_display_name;
            }
        }

        // Générer la liste HTML des paramètres
        $html = '<table>';
        $html .= '<tbody>';

        if (empty($parameters)) {
            $html .= '<tr><td colspan="2">No parameter found</td></tr>';
        } else {
            foreach ($parameters as $parameter) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($parameter) . '</td>';
                $html .= '<td>';
                $html .= '<button class="edit-button" onclick="editParameter(\'' . htmlspecialchars($parameter) . '\')">✏️</button>';
                $html .= '<button class="delete-button" onclick="deleteParameter(\'' . htmlspecialchars($parameter) . '\')">🗑️</button>';
                $html .= '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        echo $html;
    } else {
        echo "Model not found";
    }
} else {
    echo "Invalid input";
}
?>
