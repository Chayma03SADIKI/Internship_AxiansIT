function openModal(id, name, image, comments) {
    document.getElementById('category_id').value = id;
    document.getElementById('edit_category_name').value = name;
    document.getElementById('existing_category_image').value = image;
    document.getElementById('edit_category_image_preview').src = image;
    document.getElementById('edit_comments').value = comments.replace(/<br\s*\/?>/mg, "\n");
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal') || event.target == document.getElementById('messageModal')) {
        closeModal();
        closeMessageModal();
    }
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this category?');
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}