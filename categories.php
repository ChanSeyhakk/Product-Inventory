<?php

require_once "auth.php";
require_once "db.php";


/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

$message = $_GET["message"] ?? "";


/*
|--------------------------------------------------------------------------
| Get Categories
|--------------------------------------------------------------------------
*/

$sql = "SELECT id, name FROM categories";

$result = $conn->query($sql);

if (!$result) {

    die("Query failed: " . $conn->error);

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

    <title>Categories</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="app">
    <?php require "_sidebar.php"; ?>
    <main class="main-content standalone-page">

    <h1>Category Management</h1>


    <!-- =========================================================
         SUCCESS MESSAGES
    ========================================================== -->

    <?php if ($message === "created"): ?>

        <p>
            Category added successfully!
        </p>

    <?php elseif ($message === "updated"): ?>

        <p>
            Category updated successfully!
        </p>

    <?php elseif ($message === "deleted"): ?>

        <p>
            Category deleted successfully!
        </p>

    <?php endif; ?>


    <!-- =========================================================
         NAVIGATION
    ========================================================== -->

    <a href="index.php">
        Back to Products
    </a>

    <a href="add_category.php">
        Add Category
    </a>

    <br><br>


    <!-- =========================================================
         CATEGORY LIST
    ========================================================== -->

    <?php if ($result->num_rows === 0): ?>

        <p>
            No categories found.
        </p>

    <?php else: ?>

        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Category Name
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
                            <?php echo $row["id"]; ?>
                        </td>


                        <!-- Category Name -->

                        <td>
                            <?php echo htmlspecialchars($row["name"]); ?>
                        </td>


                        <!-- Actions -->

                        <td>

                            <a
                                href="edit_category.php?id=<?php echo $row["id"]; ?>"
                            >
                                Edit
                            </a>

                            |

                            <a
                                href="delete_category.php?id=<?php echo $row["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this category?');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    <?php endif; ?>


    </main>
</div>
</body>

</html>

<?php

$conn->close();

?>