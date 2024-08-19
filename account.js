document.addEventListener('DOMContentLoaded', () => {
    const updateForm = document.getElementById('update-account-form');
    const changeForm = document.getElementById('change-admin-form');
    const messageBox = document.getElementById('message-box');

    updateForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(updateForm);
        const response = await fetch('account.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        showMessage(result);
    });

    changeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(changeForm);
        const response = await fetch('account.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        showMessage(result);
    });

    function showMessage(result) {
        messageBox.innerHTML = '';
        if (result.error) {
            messageBox.innerHTML = `<div class="error">${result.error}</div>`;
        } else if (result.success) {
            messageBox.innerHTML = `<div class="success">${result.success}</div>`;
        }
    }
});

