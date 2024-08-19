<?php
@session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit();
}
?>
<?php
include 'Categories_Operations.php'
?>
<head>
<link rel="stylesheet" href="dashboard_Categories.css">
</head>
<div class="main-content">
    <div class="container">
        <div class="add-category">
            <h2><strong>New Category ?</strong></h2>
            <form method="POST" action="admin_dashboard.php?page=categories" enctype="multipart/form-data">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
                <label for="category_image">Image (optional):</label>
                <input type="file" id="category_image" name="category_image">
                <label for="comments">Description (optional):</label>
                <input type="text" id="comments" name="comments"></textarea>
                <input type="submit" name="add_category" value="Add Category">
            </form>
        </div>

        <div class="category-list">
            <h2>Category List</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Comments</th>
                    <th>Operations</th>
                </tr>
                <?php
                $sql = "SELECT * FROM categories";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["category_name"]) . "</td>
                                <td><img src='" . htmlspecialchars($row["category_image"]) . "' width='50'></td>
                                <td>" . htmlspecialchars($row["comments"]) . "</td>
                                <td class='operations'>
                                    <button class='btn' onclick=\"openModal('" . htmlspecialchars($row["category_id"]) . "', '" . htmlspecialchars($row["category_name"]) . "', '" . htmlspecialchars($row["category_image"]) . "', '" . htmlspecialchars($row["comments"]) . "')\">✏️</button>
                                    <form method='GET' action='admin_dashboard.php' style='display:inline;'>
                                        <input type='hidden' name='delete_id' value='" . htmlspecialchars($row["category_id"]) . "'>
                                        <input type='hidden' name='page' value='categories'>
                                        <button type='submit' class='btn' onclick='return confirmDelete()'>🗑️</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No categories found</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <!-- Edit Modal -->
    <div id="editModal" class="modal modal-edit">
        <div class="modal-content form-section">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Category</h2>
            <form method="POST" action="admin_dashboard.php?page=categories" enctype="multipart/form-data">
                <input type="hidden" id="category_id" name="category_id">
                <input type="hidden" id="existing_category_image" name="existing_category_image">
                <label for="edit_category_name">Category Name:</label>
                <input type="text" id="edit_category_name" name="category_name" required>
                <label for="edit_category_image">Image (optional):</label>
                <input type="file" id="edit_category_image" name="category_image">
                <img id="edit_category_image_preview" src="" style="display:none; width:100px;">
                <label for="edit_comments">Description (optional):</label>
                <input type="text" id="edit_comments" name="comments"></textarea>
                <input type="submit" name="edit_category" value="Update Category">
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
</div>