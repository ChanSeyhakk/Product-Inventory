<?php

require_once "auth.php";
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"];

    if (empty($name)) {

        die("Category name is required.");

    }

    $sql = "INSERT INTO categories (name) VALUES (?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {

        $stmt->close();

        header("Location: categories.php?message=created");
        exit;

    } else {

        die("Insert failed: " . $stmt->error);

    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Category</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content standalone-page">

    <h1>Add Category</h1>


    <form method="POST">

        <label for="name">
            Category Name:
        </label>

        <br>

        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter category name"
            required
        >

        <br><br>

        <button type="submit">
            Add Category
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