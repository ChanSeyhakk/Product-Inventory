<?php

require_once "auth.php";
require_once "db.php";

$message = $_GET["message"] ?? "";
$searchInput = $_GET["search"] ?? "";
$search = is_string($searchInput) ? trim($searchInput) : "";
$minPriceInput = $_GET["min_price"] ?? "";
$maxPriceInput = $_GET["max_price"] ?? "";
$minPriceInput = is_string($minPriceInput) ? trim($minPriceInput) : "";
$maxPriceInput = is_string($maxPriceInput) ? trim($maxPriceInput) : "";
$minPrice = null;
$maxPrice = null;
$priceFilterError = "";

foreach (["min" => $minPriceInput, "max" => $maxPriceInput] as $bound => $value) {
    if ($value === "") {
        continue;
    }

    if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
        $priceFilterError = "Enter a valid non-negative price range.";
        break;
    }

    if ($bound === "min") {
        $minPrice = (float) $value;
    } else {
        $maxPrice = (float) $value;
    }
}

if (
    $priceFilterError === "" &&
    $minPrice !== null &&
    $maxPrice !== null &&
    $minPrice > $maxPrice
) {
    $priceFilterError = "The minimum price must not be greater than the maximum price.";
}

$priceFilterValid = $priceFilterError === "" ? 1 : 0;
$hasMinPrice = $minPrice !== null ? 1 : 0;
$hasMaxPrice = $maxPrice !== null ? 1 : 0;
$minPrice ??= 0.0;
$maxPrice ??= 0.0;

$sql = "SELECT
            products.id,
            products.name,
            products.description,
            products.price,
            products.stock_quantity,
            products.image,
            categories.name AS category_name
        FROM products
        JOIN categories
            ON products.category_id = categories.id
        WHERE (? = '' OR products.name LIKE ? OR categories.name LIKE ?)
          AND ? = 1
          AND (? = 0 OR products.price >= ?)
          AND (? = 0 OR products.price <= ?)
        ORDER BY products.id DESC";

$stmt = $conn->prepare($sql);

$searchTerm = "%" . $search . "%";

$stmt->bind_param(
    "sssiidid",
    $search,
    $searchTerm,
    $searchTerm,
    $priceFilterValid,
    $hasMinPrice,
    $minPrice,
    $hasMaxPrice,
    $maxPrice
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Product Inventory</title>

    <link
        rel="stylesheet"
        href="css/style.css?v=20261006-1102"
    >

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content">

<div class="page-container">

    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>Product Inventory</h1>

            <p>
                Manage your products and inventory.
            </p>

        </div>

        <a
            href="add.php"
            class="btn btn-primary"
        >
            <?php echo app_icon("plus"); ?>
            Add Product
        </a>

    </div>


    <!-- MESSAGES -->

    <?php if ($message === "created"): ?>

        <div class="alert alert-success">
            Product added successfully!
        </div>

    <?php elseif ($message === "updated"): ?>

        <div class="alert alert-success">
            Product updated successfully!
        </div>

    <?php elseif ($message === "deleted"): ?>

        <div class="alert alert-success">
            Product deleted successfully!
        </div>

    <?php endif; ?>


    <!-- SEARCH -->

    <div class="search-card">

        <form method="GET" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Search product or category..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <label class="price-range-field">
                <span>Price from</span>
                <input
                    type="number"
                    name="min_price"
                    min="0"
                    step="any"
                    placeholder="50"
                    value="<?php echo htmlspecialchars($minPriceInput); ?>"
                >
            </label>

            <label class="price-range-field">
                <span>Price to</span>
                <input
                    type="number"
                    name="max_price"
                    min="0"
                    step="any"
                    placeholder="100"
                    value="<?php echo htmlspecialchars($maxPriceInput); ?>"
                >
            </label>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Search
            </button>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Clear
            </a>

        </form>

        <?php if ($priceFilterError !== ""): ?>
            <p class="price-filter-error" role="alert">
                <?php echo htmlspecialchars($priceFilterError); ?>
            </p>
        <?php else: ?>
            <p class="search-result-count" aria-live="polite">
                <?php
                echo $result->num_rows . " product" .
                    ($result->num_rows === 1 ? "" : "s") . " found";
                if ($hasMinPrice || $hasMaxPrice) {
                    echo " in the price range ";
                    echo $hasMinPrice ? "$" . number_format($minPrice, 2) : "any";
                    echo " to ";
                    echo $hasMaxPrice ? "$" . number_format($maxPrice, 2) : "any";
                }
                ?>
            </p>
        <?php endif; ?>

    </div>


    <!-- PRODUCTS -->

    <div class="table-card">

        <?php if ($result->num_rows === 0): ?>

            <div class="empty-state">

                <h3>No products found</h3>

                <p>
                    Try another search or add a new product.
                </p>

            </div>

        <?php else: ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Image</th>

                            <th>ID</th>

                            <th>Product Name</th>

                            <th>Category</th>

                            <th>Price</th>

                            <th>Stock</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <!-- IMAGE -->

                            <td>

                                <?php if (
                                    !empty($row["image"]) &&
                                    file_exists(
                                        __DIR__ . "/uploads/" . $row["image"]
                                    )
                                ): ?>

                                    <img
                                        src="uploads/<?php echo htmlspecialchars($row["image"]); ?>"
                                        alt="<?php echo htmlspecialchars($row["name"]); ?>"
                                        class="product-thumbnail"
                                    >

                                <?php else: ?>

                                    <div class="no-image">
                                        No Image
                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- ID -->

                            <td>
                                <?php echo $row["id"]; ?>
                            </td>


                            <!-- NAME -->

                            <td>

                                <a
                                    href="view.php?id=<?php echo $row["id"]; ?>"
                                    class="product-link"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $row["name"]
                                    );
                                    ?>
                                </a>

                            </td>


                            <!-- CATEGORY -->

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["category_name"]
                                );
                                ?>
                            </td>


                            <!-- PRICE -->

                            <td>
                                $<?php
                                echo number_format(
                                    $row["price"],
                                    2
                                );
                                ?>
                            </td>


                            <!-- STOCK -->

                            <td>

                                <?php if ($row["stock_quantity"] <= 5): ?>

                                    <span class="stock-low">
                                        <?php
                                        echo $row["stock_quantity"];
                                        ?>
                                    </span>

                                <?php else: ?>

                                    <span class="stock-normal">
                                        <?php
                                        echo $row["stock_quantity"];
                                        ?>
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ACTION -->

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="view.php?id=<?php echo $row["id"]; ?>"
                                        class="btn btn-small btn-view"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="edit.php?id=<?php echo $row["id"]; ?>"
                                        class="btn btn-small btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="delete.php?id=<?php echo $row["id"]; ?>"
                                        class="btn btn-small btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this product?');"
                                    >
                                        Delete
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>


    <!-- FOOTER LINKS -->

    <div class="page-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        |

        <a href="categories.php">
            Manage Categories
        </a>

        |

        <a href="stock.php">
            Stock
        </a>

        |

        <a href="low-stock.php">
            Low Stock
        </a>

    </div>

</div>

    </main>
</div>
</body>

</html>

<?php

$stmt->close();

?>