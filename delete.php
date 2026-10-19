<?php

require_once "auth.php";
require_once "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    http_response_code(400);
    die("Product ID is required.");
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT image FROM products WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    die("Product not found.");
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    die("Delete failed: " . $error);
}

$stmt->close();

if (!empty($product["image"])) {
    $imagePath = __DIR__ . "/uploads/" . basename($product["image"]);
    if (is_file($imagePath) && !unlink($imagePath)) {
        error_log("Unable to remove product image: " . $imagePath);
    }
}

header("Location: index.php?message=deleted");
exit;
