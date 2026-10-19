
<?php

require_once "auth.php";
require_once "db.php";


/*
|--------------------------------------------------------------------------
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Total Products
|--------------------------------------------------------------------------
*/

$product_sql = "SELECT COUNT(*) AS total FROM products";

$product_result = $conn->query($product_sql);

$product_data = $product_result->fetch_assoc();

$total_products = $product_data["total"];


/*
|--------------------------------------------------------------------------
| Total Categories
|--------------------------------------------------------------------------
*/

$category_sql = "SELECT COUNT(*) AS total FROM categories";

$category_result = $conn->query($category_sql);

$category_data = $category_result->fetch_assoc();

$total_categories = $category_data["total"];


/*
|--------------------------------------------------------------------------
| Total Stock
|--------------------------------------------------------------------------
*/

$stock_sql = "
    SELECT COALESCE(
        SUM(stock_quantity),
        0
    ) AS total
    FROM products
";

$stock_result = $conn->query($stock_sql);

$stock_data = $stock_result->fetch_assoc();

$total_stock = $stock_data["total"];


/*
|--------------------------------------------------------------------------
| Low Stock
|--------------------------------------------------------------------------
*/

$low_stock_sql = "
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity <= 5
";

$low_stock_result = $conn->query($low_stock_sql);

$low_stock_data = $low_stock_result->fetch_assoc();

$low_stock = $low_stock_data["total"];


/*
|--------------------------------------------------------------------------
| Out of Stock
|--------------------------------------------------------------------------
*/

$out_of_stock_sql = "
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity = 0
";

$out_of_stock_result = $conn->query($out_of_stock_sql);

$out_of_stock_data = $out_of_stock_result->fetch_assoc();

$out_of_stock = $out_of_stock_data["total"];


/*
|--------------------------------------------------------------------------
| Total Inventory Value
|--------------------------------------------------------------------------
*/

$inventory_value_sql = "
    SELECT COALESCE(
        SUM(price * stock_quantity),
        0
    ) AS total
    FROM products
";

$inventory_value_result = $conn->query(
    $inventory_value_sql
);

$inventory_value_data =
    $inventory_value_result->fetch_assoc();

$total_inventory_value =
    $inventory_value_data["total"];


/*
|--------------------------------------------------------------------------
| Recent Products
|--------------------------------------------------------------------------
*/

$recent_products_sql = "
    SELECT
        products.id,
        products.name,
        products.price,
        products.stock_quantity,
        categories.name AS category_name
    FROM products
    JOIN categories
        ON products.category_id = categories.id
    ORDER BY products.id DESC
    LIMIT 5
";

$recent_products_result =
    $conn->query($recent_products_sql);


/*
|--------------------------------------------------------------------------
| Low Stock Products
|--------------------------------------------------------------------------
*/

$low_stock_products_sql = "
    SELECT
        products.id,
        products.name,
        products.stock_quantity,
        categories.name AS category_name
    FROM products
    JOIN categories
        ON products.category_id = categories.id
    WHERE products.stock_quantity <= 5
    ORDER BY products.stock_quantity ASC
    LIMIT 5
";

$low_stock_products_result =
    $conn->query($low_stock_products_sql);

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
        Dashboard - Inventory
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="dashboard-layout">


    <?php require "_sidebar.php"; ?>



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="dashboard-header">


            <div>

                <p class="page-label">
                    INVENTORY MANAGEMENT
                </p>

                <h1>
                    Dashboard
                </h1>

                <p class="page-description">
                    Overview of your product inventory.
                </p>

            </div>


            <a
                href="add.php"
                class="primary-button"
            >

                <?php echo app_icon("plus"); ?>
                Add Product

            </a>


        </header>



        <!-- =================================================
             MAIN STATISTICS
        ================================================== -->

        <section class="stats-grid">


            <!-- PRODUCTS -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon blue">
                        <?php echo app_icon("package"); ?>
                    </div>

                    <span class="stat-label">
                        PRODUCTS
                    </span>

                </div>


                <div class="stat-number">
                    <?php echo $total_products; ?>
                </div>


                <p class="stat-description">
                    Total products
                </p>


            </div>



            <!-- CATEGORIES -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon purple">
                        <?php echo app_icon("folder"); ?>
                    </div>

                    <span class="stat-label">
                        CATEGORIES
                    </span>

                </div>


                <div class="stat-number">
                    <?php echo $total_categories; ?>
                </div>


                <p class="stat-description">
                    Product categories
                </p>


            </div>



            <!-- TOTAL STOCK -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon green">
                        <?php echo app_icon("chart"); ?>
                    </div>

                    <span class="stat-label">
                        TOTAL STOCK
                    </span>

                </div>


                <div class="stat-number">
                    <?php echo $total_stock; ?>
                </div>


                <p class="stat-description">
                    Units in inventory
                </p>


            </div>



            <!-- LOW STOCK -->

            <div class="stat-card low-stock-card">

                <div class="stat-card-top">

                    <div class="stat-icon yellow">
                        <?php echo app_icon("warning"); ?>
                    </div>

                    <span class="stat-label">
                        LOW STOCK
                    </span>

                </div>


                <div class="stat-number">
                    <?php echo $low_stock; ?>
                </div>


                <p class="stat-description">
                    Products at or below 5 units
                </p>


            </div>


        </section>



        <!-- =================================================
             SECONDARY STATISTICS
        ================================================== -->

        <section class="secondary-stats">


            <!-- INVENTORY VALUE -->

            <div class="secondary-stat-card">

                <div class="secondary-stat-icon">
                    <?php echo app_icon("wallet"); ?>
                </div>

                <div>

                    <span>
                        Total Inventory Value
                    </span>

                    <strong>

                        $

                        <?php

                        echo number_format(
                            $total_inventory_value,
                            2
                        );

                        ?>

                    </strong>

                </div>

            </div>



            <!-- OUT OF STOCK -->

            <div class="secondary-stat-card">

                <div class="secondary-stat-icon">
                    <?php echo app_icon("ban"); ?>
                </div>

                <div>

                    <span>
                        Out of Stock
                    </span>

                    <strong>
                        <?php echo $out_of_stock; ?>
                    </strong>

                </div>

            </div>


        </section>



        <!-- =================================================
             QUICK ACTIONS
        ================================================== -->

        <div class="dashboard-content-grid">

        <section class="dashboard-section dashboard-quick-section">


            <div class="section-header">

                <div>
                    <h2>
                        Quick Actions
                    </h2>

                    <p>
                        Manage your inventory quickly.
                    </p>
                </div>

            </div>


            <div class="quick-actions">


                <!-- ADD PRODUCT -->

                <a
                    href="add.php"
                    class="quick-action-card"
                >

                    <div class="quick-action-icon">
                        <?php echo app_icon("plus"); ?>
                    </div>

                    <div>

                        <strong>
                            Add Product
                        </strong>

                        <span>
                            Create a new product
                        </span>

                    </div>

                </a>



                <!-- CATEGORIES -->

                <a
                    href="categories.php"
                    class="quick-action-card"
                >

                    <div class="quick-action-icon">
                        <?php echo app_icon("folder"); ?>
                    </div>

                    <div>

                        <strong>
                            Categories
                        </strong>

                        <span>
                            Manage categories
                        </span>

                    </div>

                </a>



                <!-- STOCK -->

                <a
                    href="stock.php"
                    class="quick-action-card"
                >

                    <div class="quick-action-icon">
                        <?php echo app_icon("arrows"); ?>
                    </div>

                    <div>

                        <strong>
                            View Stock
                        </strong>

                        <span>
                            Check inventory
                        </span>

                    </div>

                </a>



                <!-- LOW STOCK -->

                <a
                    href="low-stock.php"
                    class="quick-action-card"
                >

                    <div class="quick-action-icon">
                        <?php echo app_icon("warning"); ?>
                    </div>

                    <div>

                        <strong>
                            Low Stock
                        </strong>

                        <span>
                            Check low inventory
                        </span>

                    </div>

                </a>


            </div>


        </section>



        <!-- =================================================
             RECENT PRODUCTS
        ================================================== -->

        <section class="dashboard-section dashboard-recent-section">


            <div class="section-header">

                <div>

                    <h2>
                        Recent Products
                    </h2>

                    <p>
                        Recently added products.
                    </p>

                </div>


                <a
                    href="index.php"
                    class="section-link"
                >
                    View All
                </a>

            </div>



            <div class="table-card">


                <div class="table-responsive">

                    <table>


                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Stock
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            $recent_products_result->num_rows === 0
                        ):

                        ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty-table"
                                >

                                    No products found.

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php

                            while (
                                $row =
                                $recent_products_result->fetch_assoc()
                            ):

                            ?>


                                <?php

                                $stock =
                                    (int) $row["stock_quantity"];


                                if ($stock <= 0) {

                                    $status =
                                        "Out of Stock";

                                    $status_class =
                                        "out-of-stock";

                                } elseif ($stock <= 5) {

                                    $status =
                                        "Low Stock";

                                    $status_class =
                                        "low-stock";

                                } else {

                                    $status =
                                        "In Stock";

                                    $status_class =
                                        "in-stock";
                                }

                                ?>


                                <tr>


                                    <td>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $row["name"]
                                            );

                                            ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $row["category_name"]
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        $

                                        <?php

                                        echo number_format(
                                            $row["price"],
                                            2
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <?php echo $stock; ?>

                                        units

                                    </td>


                                    <td>

                                        <span
                                            class="stock-badge <?php echo $status_class; ?>"
                                        >

                                            <?php echo $status; ?>

                                        </span>

                                    </td>


                                    <td>

                                        <a
                                            href="view.php?id=<?php echo $row["id"]; ?>"
                                            class="table-action"
                                        >
                                            View
                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php endif; ?>


                        </tbody>


                    </table>

                </div>

            </div>


        </section>



        <!-- =================================================
             LOW STOCK PRODUCTS
        ================================================== -->

        <section class="dashboard-section dashboard-low-stock-section">


            <div class="section-header">

                <div>

                    <h2>
                        Low Stock Products
                    </h2>

                    <p>
                        Products that may need restocking.
                    </p>

                </div>


                <a
                    href="low-stock.php"
                    class="section-link"
                >
                    View All
                </a>

            </div>



            <div class="table-card">


                <div class="table-responsive">

                    <table>


                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Stock
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            $low_stock_products_result->num_rows === 0
                        ):

                        ?>


                            <tr>

                                <td
                                    colspan="5"
                                    class="empty-table"
                                >

                                    <div class="empty-state">

                                        <div class="empty-icon">
                                            <?php echo app_icon("check"); ?>
                                        </div>

                                        <strong>
                                            All Stock Levels Are Good
                                        </strong>

                                        <p>
                                            No products currently need restocking.
                                        </p>

                                    </div>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php

                            while (
                                $row =
                                $low_stock_products_result->fetch_assoc()
                            ):

                            ?>


                                <?php

                                $stock =
                                    (int) $row["stock_quantity"];


                                if ($stock === 0) {

                                    $status =
                                        "Out of Stock";

                                    $status_class =
                                        "out-of-stock";

                                } else {

                                    $status =
                                        "Low Stock";

                                    $status_class =
                                        "low-stock";
                                }

                                ?>


                                <tr>


                                    <td>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $row["name"]
                                            );

                                            ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $row["category_name"]
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <strong>
                                            <?php echo $stock; ?>
                                        </strong>

                                        units

                                    </td>


                                    <td>

                                        <span
                                            class="stock-badge <?php echo $status_class; ?>"
                                        >

                                            <?php echo $status; ?>

                                        </span>

                                    </td>


                                    <td>

                                        <a
                                            href="stock.php"
                                            class="table-action"
                                        >

                                            Manage Stock

                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php endif; ?>


                        </tbody>


                    </table>

                </div>

            </div>


        </section>



        <!-- =================================================
             INVENTORY ALERT
        ================================================== -->

        <section class="dashboard-section dashboard-alert-section">


            <div class="section-header">

                <div>
                    <h2>
                        Inventory Alert
                    </h2>

                    <p>
                        Current stock status
                    </p>
                </div>

            </div>


            <?php if ($low_stock > 0): ?>


                <div class="inventory-alert warning-alert">


                    <div class="alert-icon">
                        <?php echo app_icon("warning"); ?>
                    </div>


                    <div>

                        <strong>
                            Low Stock Alert
                        </strong>


                        <p>

                            You currently have

                            <strong>
                                <?php echo $low_stock; ?>
                            </strong>

                            product(s) with stock at or below 5 units.

                        </p>

                    </div>


                    <a
                        href="low-stock.php"
                        class="alert-button"
                    >
                        View Products
                    </a>


                </div>


            <?php else: ?>


                <div class="inventory-alert success-alert">


                    <div class="alert-icon">
                        <?php echo app_icon("check"); ?>
                    </div>


                    <div>

                        <strong>
                            Inventory Looks Good
                        </strong>


                        <p>
                            No products are currently low on stock.
                        </p>

                    </div>


                </div>


            <?php endif; ?>


        </section>

        </div>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <footer class="dashboard-footer">

            <p>
                Product Inventory Management System
            </p>

            <p>
                Built with PHP & MySQL
            </p>

        </footer>


    </main>


</div>


</body>

</html>
