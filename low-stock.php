<?php

require_once "auth.php";
require_once "db.php";


/*
|--------------------------------------------------------------------------
| LOW STOCK THRESHOLD
|--------------------------------------------------------------------------
*/

$low_stock_threshold = 5;


/*
|--------------------------------------------------------------------------
| GET LOW STOCK PRODUCTS
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            products.id,
            products.name,
            categories.name AS category_name,
            products.price,
            products.stock_quantity

        FROM products

        JOIN categories
            ON products.category_id = categories.id

        WHERE products.stock_quantity <= ?

        ORDER BY products.stock_quantity ASC";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    die("Query preparation failed: " . $conn->error);

}


$stmt->bind_param(
    "i",
    $low_stock_threshold
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

    <title>Low Stock Products</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content standalone-page low-stock-page">

    <header class="page-header">
        <div>
            <span class="page-label">INVENTORY MANAGEMENT</span>
            <h1>Low Stock Products</h1>
            <p>
                Products with stock quantity less than or equal to
                <?php echo $low_stock_threshold; ?> are shown below.
            </p>
        </div>
        <nav class="standalone-actions" aria-label="Low stock actions">
            <a href="index.php" class="btn btn-secondary">Back to Products</a>
            <a href="stock.php" class="btn btn-primary">View All Stock</a>
        </nav>
    </header>

    <?php if ($result->num_rows === 0): ?>

        <p class="low-stock-empty">
            No low stock products found.
        </p>

    <?php else: ?>

        <div class="table-card low-stock-table-card">
            <div class="table-header">
                <div>
                    <h2>Products to Reorder</h2>
                    <p><?php echo $result->num_rows; ?> products need attention</p>
                </div>
            </div>
            <div class="table-wrapper">
                <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Product Name
                    </th>

                    <th>
                        Category
                    </th>

                    <th>
                        Price
                    </th>

                    <th>
                        Stock Quantity
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>


                        <!-- ID -->

                        <td>

                            <?php
                            echo $row["id"];
                            ?>

                        </td>


                        <!-- PRODUCT NAME -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row["name"]
                            );
                            ?>

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

                            <span class="stock-badge <?php echo (int)$row["stock_quantity"] <= 0 ? "out-of-stock" : "low-stock"; ?>">
                                <?php echo $row["stock_quantity"]; ?> units
                            </span>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <a
                                href="edit.php?id=<?php echo $row["id"]; ?>"
                                class="btn btn-small btn-edit"
                            >
                                Edit
                            </a>

                        </td>


                    </tr>

                <?php endwhile; ?>

            </tbody>

                </table>
            </div>
        </div>

    <?php endif; ?>


    </main>
</div>
</body>

</html>

<?php

$stmt->close();

$conn->close();

?>
