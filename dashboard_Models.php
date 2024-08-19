<?php
@session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}
include 'db.php';
include 'Models_Operations.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard_Models.css">
    <link rel="stylesheet" href="params_modal.css">
    <script src="dashboard_Models.js" defer></script>
    <title>Dashboard Models</title>
</head>
<body>
<div class="main-content">
    <div class="container">
        <div class="add-model">
            <h2><strong>New Model ?</strong></h2>
            <form method="POST" action="admin_dashboard.php?page=models">
                <label for="model_name">Model Name:</label>
                <input type="text" id="model_name" name="model_name" required>
                <label for="model_category">Category:</label>
                <select id="model_category" name="model_category" required>
                <?php
                    $sql = "SELECT * FROM categories";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                ?>
                </select>
                <input type="submit" name="add_model" value="Add Model">
            </form>
        </div>

        <div class="model-list">
            <h2>Model List</h2>
            <input type="text" id="search" placeholder="Search model name">
            <table id="modelTable">
                <tr>
                    <th>Model</th>
                    <th>Category</th>
                    <th>Operations</th>
                </tr>
                <!-- Les résultats seront insérés ici par JavaScript -->
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal modal-edit">
        <div class="modal-content form-section">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Model</h2>
            <form method="POST" action="admin_dashboard.php?page=models">
                <input type="hidden" id="model_id" name="model_id">
                <label for="edit_model_name">Model Name:</label>
                <input type="text" id="edit_model_name" name="model_name" required>
                <label for="edit_model_category">Category:</label>
                <select id="edit_model_category" name="model_category" required>
                    <?php
                    $sql = "SELECT * FROM categories";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="edit_model" value="Update Model">
            </form>
        </div>
    </div>

    <!-- Message Modal -->
    <div id="messageModal" class="modal modal-message">
        <div class="modal-content">
            <span class="close" onclick="closeMessageModal()">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <!-- Parameters Modal -->
    <div id="paramsModal" class="modal modal-params" data-model-id="">
        <div class="modal-content">
            <span class="close" onclick="closeParamsModal()">&times;</span>
            <h2>Manage Parameters for Model <span id="modelName"></span></h2>

            <h3>Parameters</h3> <!-- Add this line to add the title before the list of parameters -->
            <div id="paramsList"></div> <!-- Liste des paramètres sera insérée ici -->

            <div class="add-parameter">
                <label for="newParameterName">Add New Parameter:</label>
                <input type="text" id="newParameterName" name="newParameterName">
                <button class="add-button" onclick="addParameter()">Add</button>
            </div>
        </div>
    </div>


    <?php if ($message): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modalMessage').textContent = "<?php echo $message; ?>";
        document.getElementById('messageModal').style.display = 'block';
    });
    </script>
    <?php elseif ($error): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modalMessage').textContent = "<?php echo $error; ?>";
        document.getElementById('messageModal').style.display = 'block';
    });
    </script>
    <?php endif; ?>
</div>
</body>
</html>
