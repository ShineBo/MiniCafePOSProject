<?php
session_start();
require '../config/config.php';
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $customerName = $_POST['customer_name'];
    $menuItems = json_decode($_POST['menu_items'], true); // Decode JSON data
    $quantities = json_decode($_POST['quantities'], true); // Decode JSON data

    $totalAmount = 0;

    // Calculate total amount
    foreach ($menuItems as $itemId) {
        if (isset($quantities[$itemId])) {
            $quantity = intval($quantities[$itemId]);

            $stmtItem = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
            $stmtItem->bind_param("i", $itemId);
            $stmtItem->execute();
            $resultItem = $stmtItem->get_result();
            $price = $resultItem->fetch_assoc()['price'];
            $totalAmount += $price * $quantity;

            // Close item statement
            $stmtItem->close();
        }
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, total_amount) VALUES (?, ?)");
    $stmt->bind_param("sd", $customerName, $totalAmount);
    if (!$stmt->execute()) {
        echo "Error inserting order: " . $stmt->error;
        exit();
    }
    $orderId = $stmt->insert_id;

    // Insert order items
    foreach ($menuItems as $itemId) {
        if (isset($quantities[$itemId])) {
            $quantity = intval($quantities[$itemId]);

            // Fetch price for the order item
            $stmtItem = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
            $stmtItem->bind_param("i", $itemId);
            $stmtItem->execute();
            $resultItem = $stmtItem->get_result();
            $price = $resultItem->fetch_assoc()['price'];

            // Insert item into order_items table
            $stmtOrderItem = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $priceTotal = $price * $quantity; // Calculate total price for this item
            $stmtOrderItem->bind_param("iiid", $orderId, $itemId, $quantity, $priceTotal);
            if (!$stmtOrderItem->execute()) {
                echo "Error inserting order items: " . $stmtOrderItem->error;
                exit();
            }

            // Close statements
            $stmtItem->close();
            $stmtOrderItem->close();
        }
    }

    // Close connection
    $conn->close();

    // Redirect to thank you page
    header("Location: ../public/thankyou.php");
    exit();
} else {
    echo "Invalid request method.";
}

ob_end_flush();
?>