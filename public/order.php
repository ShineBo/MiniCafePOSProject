<?php
session_start();
require '../config/config.php';
require '../config/auth.php';


$query = "SELECT * FROM menu_items ORDER BY name ASC";
$menuItems = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Place Your Order</h2>
        <form id="orderForm">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>

            <div class="mb-3">
                <label for="menu_items" class="form-label">Select Items</label>
                <div id="menuItemsContainer">
                    <?php while ($item = $menuItems->fetch_assoc()) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?php echo $item['id']; ?>" id="item_<?php echo $item['id']; ?>" name="menu_items[]">
                            <label class="form-check-label" for="item_<?php echo $item['id']; ?>">
                                <?php echo $item['name']; ?> - $<?php echo number_format($item['price'], 2); ?>
                            </label>
                            <input type="number" class="form-control mt-1" id="quantity_<?php echo $item['id']; ?>" name="quantities[<?php echo $item['id']; ?>]" placeholder="Quantity" min="1">
                        </div>
                    <?php } ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit Order</button>
        </form>

        <div id="orderResponse" class="mt-4"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#orderForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            $.post('../actions/submit_order.php', formData, function(response) {
                console.log(response);
                window.location.href = '../public/thankyou.php';
            });
        });
    </script>
</body>
</html>