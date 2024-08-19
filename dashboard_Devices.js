// Function to fetch devices based on the selected model
function fetchDevices() {
    const modelName = document.getElementById('model_name').value;
    
    fetch('device_manage/get_device.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ model_name: modelName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('deviceList').innerHTML = '<p>Error fetching devices: ' + data.error + '</p>';
        } else {
            if (data.devices.length === 0) {
                document.getElementById('deviceList').innerHTML = '<p>No devices found for this model.</p>';
            } else {
                let deviceListHtml = `
                    <table>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Operations</th>
                        </tr>
                `;
                data.devices.forEach(device => {
                    deviceListHtml += `
                        <tr>
                            <td><img src="device_manage/${device.device_image}" width="50"></td>
                            <td>${device.device_name}</td>
                            <td>
                                <button class="btn" onclick="showEditDeviceModal('${modelName}', ${device.device_id})">✏️</button>
                                <button class="btn" onclick="deleteDevice('${modelName}', ${device.device_id})">🗑️</button>
                            </td>
                        </tr>
                    `;
                });
                deviceListHtml += '</table>';
                document.getElementById('deviceList').innerHTML = deviceListHtml;
            }
        }
    })
    .catch(error => {
        document.getElementById('deviceList').innerHTML = '<p>Error: ' + error.message + '</p>';
    });
}


// Function to delete a device
function deleteDevice(modelName, deviceId) {
    if (!confirm('Are you sure you want to delete this device?')) {
        return;
    }

    fetch('device_manage/delete_device.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ model_name: modelName, device_id: deviceId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error deleting device: ' + data.error);
        } else {
            alert(data.success);
            fetchDevices(); // Refresh the device list
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Function to show the Add Device modal with fields based on model columns
function showAddDeviceModal() {
    const modelName = document.getElementById('model_name').value;
    if (!modelName) {
        alert('Please select a model first.');
        return;
    }
    
    document.getElementById('model_name_input').value = modelName;
    
    // Fetch columns for the selected model
    fetch('device_manage/get_device_fields.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ model_name: modelName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error fetching fields: ' + data.error);
            return;
        }
        
        let fieldsHtml = '';
        data.fields.forEach(field => {
            if (field.name !== 'device_id' && field.name !== 'model_id') {
                if (field.name === 'device_image') {
                    fieldsHtml += `
                        <label for="${field.name}">Device Image:</label>
                        <input type="file" id="${field.name}" name="${field.name}">
                        <br>
                    `;
                } else if (field.name === 'device_name') {
                    fieldsHtml += `
                        <label for="${field.name}">${field.label}:</label>
                        <input type="text" id="${field.name}" name="${field.name}" required>
                        <br>
                    `;
                } else {
                    fieldsHtml += `
                        <label for="${field.name}">${field.label}:</label>
                        <textarea id="${field.name}" name="${field.name}" rows="4" cols="50"></textarea>
                        <br>
                    `;
                }
            }
        });
        document.getElementById('deviceFields').innerHTML = fieldsHtml;
        document.getElementById('addDeviceModal').style.display = 'block';
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}


// Function to handle form submission
function handleAddDeviceSubmit(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('addDeviceForm'));
    
    fetch('device_manage/add_device.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error adding device: ' + data.error);
        } else {
            alert(data.success);
            fetchDevices(); // Refresh the device list
            document.getElementById('addDeviceModal').style.display = 'none';
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Function to show the Edit Device modal with fields based on model columns
function showEditDeviceModal(modelName, deviceId) {
    document.getElementById('edit_model_name').value = modelName;
    document.getElementById('edit_device_id').value = deviceId;
    const editDeviceFieldsContainer = document.getElementById('editDeviceFields');

    // Clear existing fields
    editDeviceFieldsContainer.innerHTML = '';

    // Fetch the fields of the device and existing values
    fetch(`device_manage/get_device_value.php?model_name=${encodeURIComponent(modelName)}&device_id=${deviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error fetching fields: ' + data.error);
                return;
            }
            data.fields.forEach(field => {
                if (field.column_name !== 'device_id' && field.column_name !== 'model_id') {
                    const fieldLabel = document.createElement('label');
                    fieldLabel.textContent = field.name;

                    let fieldInput;
                    if (field.column_name === 'device_image') {
                        fieldInput = document.createElement('input');
                        fieldInput.type = 'file';
                    } else if (field.column_name === 'device_name') {
                        fieldInput = document.createElement('input');
                        fieldInput.type = 'text';
                    } else {
                        fieldInput = document.createElement('textarea');
                        fieldInput.rows = 4; 
                        fieldInput.cols = 50;
                        fieldInput.style.height = '50px'; // Set fixed height
                        fieldInput.style.resize = 'none';
                    }
                    fieldInput.name = field.column_name;

                    if (field.column_name !== 'device_image' && field.value) {
                        fieldInput.value = field.value;
                    }

                    editDeviceFieldsContainer.appendChild(fieldLabel);
                    editDeviceFieldsContainer.appendChild(fieldInput);
                    editDeviceFieldsContainer.appendChild(document.createElement('br'));
                }
            });
        })
        .catch(error => console.error('Error fetching fields:', error));

    document.getElementById('editDeviceModal').style.display = 'block';
}


// Function to handle the form submission for editing device
document.getElementById('editDeviceForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(document.getElementById('editDeviceForm'));

    fetch('device_manage/update_device.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error updating device: ' + data.error);
            } else {
                alert(data.success);
                fetchDevices(); // Refresh the device list
                document.getElementById('editDeviceModal').style.display = 'none';
            }
        })
        .catch(error => console.error('Error:', error));
});