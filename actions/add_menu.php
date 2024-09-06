<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $photo_url = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../public/assets/images/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);

        if (!is_dir($target_dir)) {
            echo "Target directory does not exist.";
            exit();
        }
        if (!is_writable($target_dir)) {
            echo "Target directory is not writable.";
            exit();
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Update the photo URL to the new uploaded file's name
            $photo_url = basename($_FILES["photo"]["name"]);
        } else {
            $_SESSION['message'] = "Error uploading the file.";
            header("Location: ../public/menus.php");
            exit();
        }
    }

    $insertStmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category, photo_url) VALUES (?, ?, ?, ?, ?)");
    $insertStmt->bind_param("ssdss", $name, $description, $price, $category, $photo_url);

    if ($insertStmt->execute()) {
        $_SESSION['message'] = "Menu item added successfully!";
        header("Location: ../public/menus.php");
        exit();
    } else {
        $_SESSION['message'] = "SQL Error: " . $insertStmt->error;
        header("Location: ../public/menus.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }
    </style>
</head>
<body class="bg-info-subtle">
    <!-- Navbar -->
    <?php include '../public/navbar.php'; ?>

    <div class="container mt-4">
        <div class="form-container">
            <h2 class="mb-4 text-center">Add New Menu Item</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Menu Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter menu name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" placeholder="Enter price" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="drinks">Drinks</option>
                        <option value="desserts">Desserts</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <button type="submit" class="btn btn-outline-primary w-100">Add Menu Item</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>