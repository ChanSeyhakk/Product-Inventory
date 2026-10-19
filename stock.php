<?php

require_once "auth.php";
require_once "db.php";


/*
|--------------------------------------------------------------------------
| Stock Action
|--------------------------------------------------------------------------
*/

$message = "";
$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_id = $_POST["product_id"] ?? "";
    $action = $_POST["action"] ?? "";
    $quantity = $_POST["quantity"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Validate Input
    |--------------------------------------------------------------------------
    */

    if (
        !is_numeric($product_id) ||
        !is_numeric($quantity) ||
        (int)$quantity <= 0
    ) {

        $error = "Please enter a valid quantity.";

    } elseif (
        $action !== "increase" &&
        $action !== "decrease"
    ) {

        $error = "Invalid stock action.";

    } else {

        $product_id = (int)$product_id;
        $quantity = (int)$quantity;


        /*
        |--------------------------------------------------------------------------
        | Get Current Stock
        |--------------------------------------------------------------------------
        */

        $sql = "SELECT stock_quantity
                FROM products
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $product_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $product = $result->fetch_assoc();

        $stmt->close();


        if (!$product) {

            $error = "Product not found.";

        } else {

            $current_stock = (int)$product["stock_quantity"];


            /*
            |--------------------------------------------------------------------------
            | Calculate New Stock
            |--------------------------------------------------------------------------
            */

            if ($action === "increase") {

                $new_stock = $current_stock + $quantity;

            } else {

                $new_stock = $current_stock - $quantity;
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Negative Stock
            |--------------------------------------------------------------------------
            */

            if ($new_stock < 0) {

                $error = "Stock cannot be negative.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Update Stock
                |--------------------------------------------------------------------------
                */

                $sql = "UPDATE products
                        SET stock_quantity = ?
                        WHERE id = ?";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "ii",
                    $new_stock,
                    $product_id
                );


                if ($stmt->execute()) {

                    if ($action === "increase") {

                        $message = "Stock increased successfully.";

                    } else {

                        $message = "Stock decreased successfully.";
                    }

                } else {

                    $error = "Failed to update stock.";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Get All Products
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
        ORDER BY products.id DESC";

$result = $conn->query($sql);

if (!$result) {

    die("Failed to load products: " . $conn->error);
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

    <title>Stock Management</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>


<div class="app">


    <?php require "_sidebar.php"; ?>



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <!-- Header -->

        <div class="page-header">

            <div>

                <span class="page-label">
                    INVENTORY MANAGEMENT
                </span>

                <h1>
                    Stock Management
                </h1>

                <p>
                    Increase or decrease product stock.
                </p>

            </div>

        </div>



        <!-- =================================================
             SUCCESS MESSAGE
        ================================================== -->

        <?php if (!empty($message)): ?>

            <div class="alert alert-success">

                <?php echo app_icon("check"); ?>

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             ERROR MESSAGE
        ================================================== -->

        <?php if (!empty($error)): ?>

            <div class="alert alert-error">

                <?php echo app_icon("warning"); ?>

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             STOCK TABLE
        ================================================== -->

        <div class="table-card">

            <div class="table-header">

                <div>

                    <h2>
                        Product Stock
                    </h2>

                    <p>
                        Manage your current inventory.
                    </p>

                </div>

            </div>


            <div class="table-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Product
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Current Stock
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Stock Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php

                        $stock = (int)$row["stock_quantity"];

                        if ($stock <= 0) {

                            $status = "Out of Stock";
                            $status_class = "out-of-stock";

                        } elseif ($stock <= 5) {

                            $status = "Low Stock";
                            $status_class = "low-stock";

                        } else {

                            $status = "In Stock";
                            $status_class = "in-stock";
                        }

                        ?>

                        <tr>

                            <td>
                                <?php echo $row["id"]; ?>
                            </td>


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

                                    <?php
                                    echo $stock;
                                    ?>

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

                                <form
                                    method="POST"
                                    class="stock-action-form"
                                >

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?php echo $row["id"]; ?>"
                                    >


                                    <input
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        placeholder="Qty"
                                        required
                                        class="stock-quantity-input"
                                    >


                                    <button
                                        type="submit"
                                        name="action"
                                        value="increase"
                                        class="btn btn-success"
                                    >
                                        <?php echo app_icon("plus"); ?>
                                        Add
                                    </button>


                                    <button
                                        type="submit"
                                        name="action"
                                        value="decrease"
                                        class="btn btn-danger"
                                    >
                                        <?php echo app_icon("minus"); ?>
                                        Remove
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>


    </main>

</div>


</body>

</html>