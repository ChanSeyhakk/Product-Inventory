<?php

require_once "auth.php";
require_once "db.php";

$id = $_GET["id"] ?? "";

if (!is_numeric($id) || (int) $id < 1) {
    http_response_code(400);
    die("Invalid category ID.");
}

$id = (int) $id;
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    die("Delete failed: " . $error);
}

$stmt->close();
header("Location: categories.php?message=deleted");
exit;
