<?php
session_start();
require '../config/config.php';

$query = "SELECT * FROM orders ORDER BY order_date ASC";
$ordersResult = $conn->query($query);

function getOrderItems($orderId, $conn) {
    $itemsQuery = "SELECT oi.*, mi.name 
                   FROM order_items oi 
                   JOIN menu_items mi ON oi.menu_item_id = mi.id 
                   WHERE oi.order_id = ?";
    $stmt = $conn->prepare($itemsQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    $updateQuery = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-info-subtle">
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5">
        <h2 class="mb-4">Orders</h2>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-warning">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $ordersResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>MMK <?php echo number_format($order['total_amount']); ?></td>
                        <td>
                            <span class="badge <?php 
                                echo $order['status'] == 'completed' ? 'bg-success-subtle text-dark' : 
                                     ($order['status'] == 'pending' ? 'bg-warning text-dark' : 
                                     'bg-danger-subtle text-dark'); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <?php if ($order['status'] != 'completed' and $order['status'] != 'canceled') { ?>
                                <form method="POST" class="d-inline" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select form-select-sm mt-1" onchange="this.form.submit()">
                                        <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="completed" <?php if ($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                        <option value="canceled" <?php if ($order['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            <?php } ?>
                        </td>
                        <td><?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></td>
                        <td>
                            <?php if ($order['status'] != 'canceled' and $order['status'] != 'pending') { ?>
                                <a href="print_receipt.php?order_id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">Print Receipt</a>
                            <?php } ?>
                            <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#orderItems<?php echo $order['id']; ?>" aria-expanded="false">
                                Show Items
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="collapse" id="orderItems<?php echo $order['id']; ?>">
                                <strong>Items:</strong>
                                <ul class="list-group mt-2">
                                    <?php
                                    $orderItems = getOrderItems($order['id'], $conn);
                                    while ($item = $orderItems->fetch_assoc()) {
                                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>{$item['name']} - {$item['quantity']} pcs <span>$".number_format($item['price'], 2)."</span></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>