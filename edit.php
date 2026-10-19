<?php

require_once "auth.php";
require_once "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Product ID is required.");
}

$id = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| UPDATE PRODUCT
|--------------------------------------------------------------------------
*/

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


    /*
    |--------------------------------------------------------------------------
    | GET OLD IMAGE
    |--------------------------------------------------------------------------
    */

    $oldSql = "SELECT image
               FROM products
               WHERE id = ?";

    $oldStmt = $conn->prepare($oldSql);

    $oldStmt->bind_param(
        "i",
        $id
    );

    $oldStmt->execute();

    $oldResult = $oldStmt->get_result();

    $oldProduct = $oldResult->fetch_assoc();

    $oldStmt->close();

    if (!$oldProduct) {
        die("Product not found.");
    }

    $oldImage = $oldProduct["image"];

    $imageName = $oldImage;


    /*
    |--------------------------------------------------------------------------
    | NEW IMAGE
    |--------------------------------------------------------------------------
    */

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

        $imageName = uniqid(
            "product_",
            true
        ) . "." . $extension;

        $uploadPath = __DIR__ . "/uploads/" . $imageName;

        if (!move_uploaded_file(
            $_FILES["image"]["tmp_name"],
            $uploadPath
        )) {
            die("Failed to save image.");
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */

    $sql = "UPDATE products
            SET
                category_id = ?,
                name = ?,
                description = ?,
                price = ?,
                stock_quantity = ?,
                image = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "issdisi",
        $category_id,
        $name,
        $description,
        $price,
        $stock_quantity,
        $imageName,
        $id
    );

    if ($stmt->execute()) {

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE OLD IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            $oldImage &&
            $oldImage !== $imageName
        ) {

            $oldPath = __DIR__ . "/uploads/" . $oldImage;

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        header(
            "Location: index.php?message=updated"
        );

        exit;

    } else {

        /*
        |--------------------------------------------------------------------------
        | REMOVE NEW IMAGE IF UPDATE FAILS
        |--------------------------------------------------------------------------
        */

        if (
            $imageName !== $oldImage &&
            $imageName !== null
        ) {

            $newPath = __DIR__ . "/uploads/" . $imageName;

            if (file_exists($newPath)) {
                unlink($newPath);
            }
        }

        die(
            "Update failed: " .
            $stmt->error
        );
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| GET PRODUCT
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            id,
            category_id,
            name,
            description,
            price,
            stock_quantity,
            image
        FROM products
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$stmt->close();

if (!$product) {
    die("Product not found.");
}


/*
|--------------------------------------------------------------------------
| GET CATEGORIES
|--------------------------------------------------------------------------
*/

$categorySql = "SELECT id, name
                FROM categories
                ORDER BY name ASC";

$categoryResult = $conn->query(
    $categorySql
);

if (!$categoryResult) {
    die(
        "Category query failed: " .
        $conn->error
    );
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

    <title>Edit Product</title>

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

            <h1>Edit Product</h1>

            <p>
                Update product information.
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

            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Product Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($product["name"]); ?>"
                    required
                >

            </div>


            <!-- CATEGORY -->

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
                        $category = $categoryResult->fetch_assoc()
                    ): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                            <?php
                            if (
                                $category["id"] ==
                                $product["category_id"]
                            ) {
                                echo "selected";
                            }
                            ?>
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


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                ><?php
                    echo htmlspecialchars(
                        $product["description"]
                    );
                ?></textarea>

            </div>


            <!-- PRICE -->

            <div class="form-group">

                <label for="price">
                    Price
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    value="<?php echo $product["price"]; ?>"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <!-- STOCK -->

            <div class="form-group">

                <label for="stock_quantity">
                    Stock Quantity
                </label>

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    value="<?php echo $product["stock_quantity"]; ?>"
                    min="0"
                    required
                >

            </div>


            <!-- CURRENT IMAGE -->

            <?php if (
                !empty($product["image"]) &&
                file_exists(
                    __DIR__ . "/uploads/" . $product["image"]
                )
            ): ?>

                <div class="current-image">

                    <label>
                        Current Image
                    </label>

                    <img
                        src="uploads/<?php echo htmlspecialchars($product["image"]); ?>"
                        alt="Current product image"
                    >

                </div>

            <?php endif; ?>


            <!-- NEW IMAGE -->

            <div class="form-group">

                <label for="image">
                    Replace Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                >

                <small>
                    Leave empty to keep the current image.
                    Maximum 5 MB.
                </small>

            </div>


            <!-- BUTTONS -->

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
                    Update Product
                </button>

            </div>

        </form>

    </div>

</div>

    </main>
</div>
</body>

</html>