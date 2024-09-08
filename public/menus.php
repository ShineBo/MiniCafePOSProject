<?php
session_start();
require '../config/config.php';
require '../config/auth.php';


$sql = "SELECT * FROM menu_items ORDER BY created_at ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menus - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Our Menu</h2>
            <a href="../actions/add_menu.php" class="btn btn-outline-primary btn-lg rounded-pill px-3">Add New Menu</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <span id="countdown">5</span> seconds remaining.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 primary-color">
                        <img src="../public/assets/images/<?php echo htmlspecialchars($row['photo_url']); ?>" class="card-img-top" alt="Menu Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text"><strong>Price: MMK <?php echo number_format($row['price']); ?></strong></p>
                            <p class="badge bg-warning-subtle text-dark"><?php echo htmlspecialchars($row['category']); ?></p>
                        </div>
                        <div class="card-footer primary-color d-flex justify-content-between">
                            <td>
                                <a href="../actions/edit_menu.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn rounded-pill px-3">Edit</a>
                                <button class="btn btn-outline-danger btn rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $row['id']; ?>">Delete</button>
                            </td>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirmDeleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="confirmDeleteLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="confirmDeleteLabel<?php echo $row['id']; ?>">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete <strong><?php echo htmlspecialchars($row['name']); ?></strong>? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="../actions/delete_menu.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                const alertBox = document.querySelector('.alert');
                if (alertBox) {
                    alertBox.classList.remove('show');
                    alertBox.classList.add('fade');
                    setTimeout(() => {
                        alertBox.remove();
                    }, 500);
                }
            }
        }, 1000);
    </script>
</body>
</html>