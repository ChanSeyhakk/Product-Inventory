<?php

require_once "auth.php";
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $stock_quantity = $_POST["stock_quantity"] ?? "";

    if (
        $name === "" ||
        $category_id === "" ||
        $price === "" ||
        $stock_quantity === "" ||
        $price < 0 ||
        $stock_quantity < 0
    ) {
        die("Invalid product data.");
    }

    $imageName = null;

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            die("Image upload failed.");
        }

        if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
            die("Image must be smaller than 5 MB.");
        }

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "image/gif"  => "gif",
            "image/webp" => "webp"
        ];

        $fileType = mime_content_type(
            $_FILES["image"]["tmp_name"]
        );

        if (!array_key_exists($fileType, $allowedTypes)) {
            die("Only JPG, PNG, GIF, and WEBP images are allowed.");
        }

        $extension = $allowedTypes[$fileType];

        $imageName = uniqid("product_", true) . "." . $extension;

        $uploadPath = __DIR__ . "/uploads/" . $imageName;

        if (!move_uploaded_file(
            $_FILES["image"]["tmp_name"],
            $uploadPath
        )) {
            die("Failed to save image.");
        }
    }

    $sql = "INSERT INTO products
            (
                category_id,
                name,
                description,
                price,
                stock_quantity,
                image
            )
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "issdis",
        $category_id,
        $name,
        $description,
        $price,
        $stock_quantity,
        $imageName
    );

    if ($stmt->execute()) {

        $stmt->close();

        header("Location: index.php?message=created");
        exit;

    } else {

        if ($imageName !== null) {

            $uploadPath = __DIR__ . "/uploads/" . $imageName;

            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
        }

        die("Insert failed: " . $stmt->error);
    }

    $stmt->close();
}

$category_sql = "SELECT id, name FROM categories ORDER BY name ASC";

$category_result = $conn->query($category_sql);

if (!$category_result) {
    die("Category query failed: " . $conn->error);
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

    <title>Add Product</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content">

<div class="page-container">

    <div class="page-header">

        <div>

            <h1>Add Product</h1>

            <p>
                Create a new product in your inventory.
            </p>

        </div>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Back to Products
        </a>

    </div>


    <div class="form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label for="name">
                    Product Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter product name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <?php while (
                        $category = $category_result->fetch_assoc()
                    ): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                        >
                            <?php
                            echo htmlspecialchars(
                                $category["name"]
                            );
                            ?>
                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Enter product description"
                ></textarea>

            </div>


            <div class="form-group">

                <label for="price">
                    Price
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    required
                >

            </div>


            <div class="form-group">

                <label for="stock_quantity">
                    Stock Quantity
                </label>

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    min="0"
                    placeholder="0"
                    required
                >

            </div>


            <div class="form-group">

                <label for="image">
                    Product Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                >

                <small>
                    JPG, PNG, GIF, or WEBP. Maximum 5 MB.
                </small>

            </div>


            <div class="form-actions">

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Product
                </button>

            </div>

        </form>

    </div>

</div>

    </main>
</div>
</body>

</html>