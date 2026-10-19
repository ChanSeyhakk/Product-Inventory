<?php

require_once "auth.php";
require_once "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Product ID is required.");
}

$id = (int) $_GET["id"];

$sql = "SELECT
            products.id,
            products.name,
            products.description,
            products.price,
            products.stock_quantity,
            products.image,
            products.created_at,
            products.updated_at,
            categories.name AS category_name
        FROM products
        JOIN categories
            ON products.category_id = categories.id
        WHERE products.id = ?";

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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($product["name"]); ?>
    </title>

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

            <h1>Product Details</h1>

            <p>
                View product information.
            </p>

        </div>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Back to Products
        </a>

    </div>


    <div class="product-details-card">

        <!-- IMAGE -->

        <div class="product-details-image">

            <?php if (
                !empty($product["image"]) &&
                file_exists(
                    __DIR__ . "/uploads/" . $product["image"]
                )
            ): ?>

                <img
                    src="uploads/<?php echo htmlspecialchars($product["image"]); ?>"
                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                >

            <?php else: ?>

                <div class="large-no-image">
                    No Image Available
                </div>

            <?php endif; ?>

        </div>


        <!-- INFORMATION -->

        <div class="product-details-info">

            <h2>
                <?php
                echo htmlspecialchars(
                    $product["name"]
                );
                ?>
            </h2>


            <div class="detail-row">

                <strong>Category:</strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $product["category_name"]
                    );
                    ?>
                </span>

            </div>


            <div class="detail-row">

                <strong>Price:</strong>

                <span class="product-price">
                    $<?php
                    echo number_format(
                        $product["price"],
                        2
                    );
                    ?>
                </span>

            </div>


            <div class="detail-row">

                <strong>Stock:</strong>

                <span>

                    <?php if (
                        $product["stock_quantity"] <= 5
                    ): ?>

                        <span class="stock-low">
                            <?php
                            echo $product["stock_quantity"];
                            ?>
                        </span>

                    <?php else: ?>

                        <span class="stock-normal">
                            <?php
                            echo $product["stock_quantity"];
                            ?>
                        </span>

                    <?php endif; ?>

                </span>

            </div>


            <div class="description-box">

                <strong>Description</strong>

                <p>

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $product["description"] ?? ""
                        )
                    );
                    ?>

                </p>

            </div>


            <div class="detail-actions">

                <a
                    href="edit.php?id=<?php echo $product["id"]; ?>"
                    class="btn btn-primary"
                >
                    Edit Product
                </a>

                <a
                    href="delete.php?id=<?php echo $product["id"]; ?>"
                    class="btn btn-delete"
                    onclick="return confirm('Are you sure you want to delete this product?');"
                >
                    Delete Product
                </a>

            </div>

        </div>

    </div>

</div>

    </main>
</div>
</body>

</html>