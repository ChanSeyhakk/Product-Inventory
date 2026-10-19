<?php
require_once __DIR__ . "/_icons.php";

$currentPage = basename($_SERVER["PHP_SELF"]);
$productPages = ["index.php", "add.php", "edit.php", "view.php", "delete.php"];
$categoryPages = ["categories.php", "add_category.php", "edit_category.php", "delete_category.php"];
?>
<aside class="sidebar">
    <a class="sidebar-brand" href="dashboard.php">
        <span class="brand-icon"><?php echo app_icon("package"); ?></span>
        <span class="brand-name">
            <strong>Inventory</strong>
            <small>Management System</small>
        </span>
    </a>

    <nav class="sidebar-nav" aria-label="Main navigation">
        <a href="dashboard.php" class="<?php echo $currentPage === "dashboard.php" ? "active" : ""; ?>">
            <span><?php echo app_icon("grid"); ?></span>Dashboard
        </a>
        <a href="index.php" class="<?php echo in_array($currentPage, $productPages, true) ? "active" : ""; ?>">
            <span><?php echo app_icon("package"); ?></span>Inventory
        </a>
        <a href="categories.php" class="<?php echo in_array($currentPage, $categoryPages, true) ? "active" : ""; ?>">
            <span><?php echo app_icon("folder"); ?></span>Categories
        </a>
        <a href="stock.php" class="<?php echo $currentPage === "stock.php" ? "active" : ""; ?>">
            <span><?php echo app_icon("arrows"); ?></span>Stock
        </a>
        <a href="low-stock.php" class="<?php echo $currentPage === "low-stock.php" ? "active" : ""; ?>">
            <span><?php echo app_icon("warning"); ?></span>Low Stock
        </a>
    </nav>

    <div class="sidebar-account">
        <span class="account-avatar"><?php echo app_icon("user"); ?></span>
        <span class="account-name">
            <strong><?php echo htmlspecialchars($_SESSION["username"] ?? "User"); ?></strong>
            <small>Administrator</small>
        </span>
    </div>

    <div class="sidebar-bottom">
        <a href="logout.php"><span><?php echo app_icon("logout"); ?></span>Logout</a>
    </div>
</aside>
