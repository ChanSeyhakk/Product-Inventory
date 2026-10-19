<?php

session_start();

require_once "db.php";
require_once __DIR__ . "/_icons.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

        $sql = "SELECT id, username, password
                FROM users
                WHERE username = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        $stmt->close();


        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];

            $_SESSION["username"] = $user["username"];

            header("Location: dashboard.php");

            exit;

        } else {

            $error = "Invalid username or password.";

        }
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

    <title>Login - Inventory</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body class="login-page">


<main class="login-container">

    <section class="login-brand-panel">

        <div class="login-brand-content">

            <div class="login-logo">

                <div class="login-logo-icon">
                    <?php echo app_icon("package"); ?>
                </div>

                <h1>
                    Inventory
                </h1>

                <p>
                    Management System
                </p>

            </div>

            <div class="login-brand-copy">
                <span>INVENTORY MANAGEMENT</span>
                <h2>Everything in stock.<br>Everything in control.</h2>
                <p>
                    A clearer way to manage products, track stock, and keep your
                    business moving.
                </p>
            </div>

        </div>

        <p class="login-brand-footer">PRODUCTS <span>·</span> CATEGORIES <span>·</span> STOCK</p>

    </section>


    <section class="login-form-panel">

        <div class="login-form-content">
            <div class="login-card">

                <h2>
                    Welcome back
                </h2>

                <p class="login-subtitle">
                    Sign in to continue to your inventory.
                </p>

                <?php if ($error !== ""): ?>

                    <div class="login-error" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>

                <form method="POST">

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="login-button"
                    >
                        Sign in
                    </button>

                </form>

            </div>

            <p class="login-footer">
                Product Inventory Management System
            </p>
        </div>

    </section>

</main>


</body>

</html>