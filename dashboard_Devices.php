<?php
@session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Management</title>
    <link rel="stylesheet" href="dashboard_Devices.css">
    <script src="dashboard_Devices.js" defer></script>
</head>
<body>
    <div class="container">
        <div id="modelSelect">
            <label for="model_name">Select Model : </label>
            <select id="model_name" name="model_name" onchange="fetchDevices()">
                <?php
                include 'db.php';
                $result = $conn->query("SELECT model_name FROM models");
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['model_name'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['model_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
            </select>
        </div>
        <h3>New device ?</h3>
        <button onclick="showAddDeviceModal()">Add Device</button>
        <div id="deviceList"></div>
    </div>

    <!-- Modal for adding device -->
    <div id="addDeviceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addDeviceModal').style.display='none'">&times;</span>
            <h2>Add Device</h2>
            <form id="addDeviceForm" enctype="multipart/form-data" onsubmit="handleAddDeviceSubmit(event)">
                <input type="hidden" id="model_name_input" name="model_name">
                <div id="deviceFields"></div>
                <button type="submit">Add Device</button>
            </form>
        </div>
    </div>

    <!-- Modal for editing device -->
    <div id="editDeviceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editDeviceModal').style.display='none'">&times;</span>
            <h2>Edit Device</h2>
            <form id="editDeviceForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_model_name" name="model_name">
                <input type="hidden" id="edit_device_id" name="device_id">
                <div id="editDeviceFields"></div>
                <button type="submit">Update Device</button>
            </form>
        </div>
    </div>


</body>
</html>
