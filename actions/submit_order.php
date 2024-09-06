<?php
session_start();
require '../config/config.php';
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = $_POST['customer_name'];
    $menuItems = $_POST['menu_items'];
    $quantities = $_POST['quantities'];

    $totalAmount = 0;
    foreach ($menuItems as $itemId) {
        if (isset($quantities[$itemId])) {
            $quantity = intval($quantities[$itemId]);
            $stmtItem = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
            $stmtItem->bind_param("i", $itemId);
            $stmtItem->execute();
            $resultItem = $stmtItem->get_result();
            $price = $resultItem->fetch_assoc()['price'];
            $totalAmount += $price * $quantity;
        }
    }

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, total_amount) VALUES (?, ?)");
    $stmt->bind_param("sd", $customerName, $totalAmount);
    if (!$stmt->execute()) {
        echo "Error inserting order: " . $stmt->error;
        exit();
    }
    $orderId = $stmt->insert_id;

    foreach ($menuItems as $itemId) {
        if (isset($quantities[$itemId])) {
            $quantity = intval($quantities[$itemId]);
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $price = $quantity * $price;
            $stmtItem->bind_param("iiid", $orderId, $itemId, $quantity, $price);
            if (!$stmtItem->execute()) {
                echo "Error inserting order items: " . $stmtItem->error;
                exit();
            }
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: ../public/thankyou.php");
    exit();
} else {
    echo "Invalid request method.";
}

ob_end_flush();
?>