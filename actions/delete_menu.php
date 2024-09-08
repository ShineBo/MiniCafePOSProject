<?php
session_start();
require '../config/config.php';
require '../config/auth.php';

if (isset($_GET['id'])) {
    $menuId = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $menuId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Menu item deleted successfully!";
        header("Location: ../public/menus.php");
        exit();
    } else {
        $_SESSION['message'] = "Error deleting menu item: " . $stmt->error;
        header("Location: ../public/menus.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request.";
    header("Location: ../public/menus.php");
    exit();
}
?>