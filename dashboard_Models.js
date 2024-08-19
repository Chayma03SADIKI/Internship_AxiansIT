document.getElementById('search').addEventListener('input', function() {
    const search = this.value.trim();

    fetch(`search_models.php?search=${encodeURIComponent(search)}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modelTable').innerHTML = `
                <tr>
                    <th>Model</th>
                    <th>Category</th>
                    <th>Operations</th>
                </tr>
                ${html}
            `;
        })
        .catch(error => console.error('Error:', error));
});

// Charger tous les modèles au départ
window.onload = function() {
    document.getElementById('search').dispatchEvent(new Event('input'));
};

function openEditModal(modelId, modelName, categoryId) {
    document.getElementById('model_id').value = modelId;
    document.getElementById('edit_model_name').value = modelName;
    document.getElementById('edit_model_category').value = categoryId;
    document.getElementById('editModal').style.display = "block";
}

function closeModal() {
    document.getElementById('editModal').style.display = "none";
}

// Fermer la modale lorsque l'utilisateur clique en dehors de celle-ci
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
}

// Ouvrir la fenêtre modale pour gérer les paramètres
function openParamsModal(modelId, modelName) {
    document.getElementById('modelName').textContent = modelName;
    document.getElementById('paramsModal').setAttribute('data-model-id', modelId);
    fetchParameters(modelId);
    document.getElementById('paramsModal').style.display = 'block';
}

// Fermer la fenêtre modale
function closeParamsModal() {
    document.getElementById('paramsModal').style.display = 'none';
}

// ajouter un parametre a un model
function addParameter() {
    const modelId = document.querySelector('#paramsModal').getAttribute('data-model-id');
    const parameterName = document.getElementById('newParameterName').value.trim();

    if (parameterName === '') {
        alert('Parameter name cannot be empty');
        return;
    }

    const formData = new FormData();
    formData.append('model_id', modelId);
    formData.append('parameter_name', parameterName);

    fetch('params_manager/add_params.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            document.getElementById('newParameterName').value = ''; // Clear the input field
            fetchParameters(modelId); // Refresh the list of parameters
        } else {
            alert(data.error);
            if (data.sql_error) {
                console.error('SQL Error:', data.sql_error);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function fetchParameters(modelId) {
    fetch(`params_manager/get_params.php?model_id=${modelId}`)
    .then(response => response.text())
    .then(html => {
        const paramsList = document.getElementById('paramsList');
        paramsList.innerHTML = html;
    })
    .catch(error => console.error('Error fetching parameters:', error));
}

// Modifier un paramètre existant
function editParameter(parameter) {
    const newParamName = prompt("Enter new parameter name:", parameter);
    
    if (newParamName === null) {
        // User canceled the prompt
        return;
    }

    if (newParamName.trim() === "") {
        alert("Parameter name cannot be empty");
        return;
    }

    const modelId = document.querySelector('#paramsModal').getAttribute('data-model-id');
    const formData = new FormData();
    formData.append('model_id', modelId);
    formData.append('old_parameter_name', parameter);
    formData.append('new_parameter_name', newParamName);

    fetch('params_manager/update_params.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            fetchParameters(modelId); // Refresh the list of parameters
        } else {
            alert(data.error);
            if (data.sql_error) {
                console.error('SQL Error:', data.sql_error);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteParameter(parameter) {
    const confirmDeletion = confirm("Are you sure you want to delete this parameter?");
    if (!confirmDeletion) {
        return;
    }

    const modelId = document.querySelector('#paramsModal').getAttribute('data-model-id');
    const formData = new FormData();
    formData.append('model_id', modelId);
    formData.append('parameter_name', parameter);

    fetch('params_manager/delete_params.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            fetchParameters(modelId); // Refresh the list of parameters
        } else {
            alert(data.error);
            if (data.sql_error) {
                console.error('SQL Error:', data.sql_error);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
