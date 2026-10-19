<?php

require_once "auth.php";
require_once "db.php";


// Get category ID from URL
$id = $_GET["id"] ?? 0;


// Get category from database
$sql = "SELECT id, name FROM categories WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$category = $result->fetch_assoc();

$stmt->close();


// Check if category exists
if (!$category) {

    die("Category not found.");
}


// Update category
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);


    // Validation
    if (empty($name)) {

        die("Category name is required.");
    }


    // Update database
    $sql = "UPDATE categories
            SET name = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("si", $name, $id);


    if ($stmt->execute()) {

        $stmt->close();

        header("Location: categories.php?message=updated");

        exit;
    } else {

        die("Update failed: " . $stmt->error);
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Category</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content standalone-page">

    <h1>Edit Category</h1>


    <form method="POST">

        <label for="name">
            Category Name:
        </label>

        <br><br>

        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($category["name"]); ?>"
            required>

        <br><br>

        <button type="submit">
            Update Category
        </button>

    </form>


    <br>

    <a href="categories.php">
        Back to Categories
    </a>

    </main>
</div>
</body>

</html>