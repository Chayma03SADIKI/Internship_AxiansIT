<?php
include("db.php");

function shorten_text($text, $word_limit) {
    $words = explode(" ", $text);
    if (count($words) > $word_limit) {
        return implode(" ", array_slice($words, 0, $word_limit)) . "...";
    }
    return $text;
}

$sql = "SELECT category_name, category_image, comments FROM categories";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("ERROR : " . mysqli_error($conn));
}

echo "<div class='category-list'>";
while ($row = mysqli_fetch_assoc($result)) {
    $category_name = htmlspecialchars($row['category_name']);
    $category_image = htmlspecialchars($row['category_image']);
    $comments = htmlspecialchars($row['comments']);
    $short_comments = shorten_text($comments, 23);

    echo "<div class='category-item'>";
    echo "<div class='category-text'>";
    echo "<a href='find_devices.php?category=$category_name'>$category_name</a>";
    echo "<div class='category-description'>$short_comments</div>";
    echo "</div>";
    echo "<div class='category-image'>";
    echo "<img src='$category_image' alt='$category_name'>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";

mysqli_close($conn);
?>
