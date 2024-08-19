<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consult Categories</title>
    <link rel="stylesheet" href="categories.css">
</head>
<body>
    <section id="header">
        <a href="#"><img src="image/logo_removed bg.png" class="logo" alt="Logo"></a>
        <div>
            <ul id="navbar">
                <li><a href="index.html">Home</a></li>
                <li><a href="login_admin.php">Admin</a></li>
                <li><a href="About.html">About</a></li>
                <li><a href="Contact.html">Contact</a></li>
            </ul>
        </div>
    </section>
    <section id="container">
        <?php 
            include("find_categories.php"); 
        ?>
    </section>
</body>
</html>

