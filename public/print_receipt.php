<?php
require '../config/config.php';

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        $orderItems = $conn->prepare("SELECT oi.*, mi.name 
                                      FROM order_items oi 
                                      JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                      WHERE oi.order_id = ?");
        $orderItems->bind_param("i", $orderId);
        $orderItems->execute();
        $itemsResult = $orderItems->get_result();
    }
} else {
    echo "Invalid order.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .receipt-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* background-color: #fff; */
        }
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        .receipt-header h2, .receipt-footer p {
            margin: 0;
        }
        .order-summary th, .order-summary td {
            padding: 10px;
            text-align: left;
        }
        .order-summary th {
            background-color: #f8f9fa;
        }
        .btn-print {
            display: block;
            margin: 20px auto;
        }
        .receipt-footer {
            margin-top: 20px;
        }
    </style>
</head>
<body onload="window.print()" class="bg-info-subtle">

<div class="receipt-container">
    <div class="receipt-header">
        <h2>Mini Cafe POS</h2>
        <p>Receipt for Order #<?php echo $order['id']; ?></p>
    </div>

    <hr>

    <table class="table table-bordered order-summary">
        <tr>
            <th>Customer Name</th>
            <td><?php echo $order['customer_name']; ?></td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td><?php echo $order['order_date']; ?></td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo ucfirst($order['status']); ?></td>
        </tr>
    </table>

    <h4>Order Items</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $itemsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="receipt-footer">
        <p>Thank you for your purchase!</p>
        <p>Visit us again soon.</p>
    </div>

    <button class="btn btn-primary btn-print" onclick="window.print()">Print</button>
</div>

</body>
</html>