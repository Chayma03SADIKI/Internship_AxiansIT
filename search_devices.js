document.getElementById('model-dropdown').addEventListener('change', function() {
    updateDeviceTable();
});

document.getElementById('search-bar').addEventListener('input', function() {
    updateDeviceTable();
});

window.onload = function() {
    updateDeviceTable();
};

function updateDeviceTable() {
    const search = document.getElementById('search-bar').value.trim();
    const modelDropdown = document.getElementById('model-dropdown');
    const selectedModel = modelDropdown.options[modelDropdown.selectedIndex].value;
    const category = new URLSearchParams(window.location.search).get('category');

    fetch(`search_devices.php?search=${encodeURIComponent(search)}&model=${encodeURIComponent(selectedModel)}&category=${encodeURIComponent(category)}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('device-table').innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}
