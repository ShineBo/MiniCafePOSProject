<?php
session_start();
require '../config/config.php';

if (isset($_GET['id'])) {
    $menuId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $menuId);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu = $result->fetch_assoc();

    if (!$menu) {
        echo "No menu item found with ID $menuId.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $photo_url = $menu['photo_url']; // Default to existing photo URL

        // Check if a new file was uploaded
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            // Define the directory to save uploaded files
            $target_dir = "../public/assets/images/";
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);

            // Verify directory existence and permissions
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
                echo "File uploaded successfully: " . $photo_url;
            } else {
                echo "Error uploading the file.";
            }
        }

        // Debugging: print out the parameters
        echo "Parameters: Name = $name, Description = $description, Price = $price, Category = $category, Photo URL = $photo_url, Menu ID = $menuId";

        $updateStmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, category = ?, photo_url = ? WHERE id = ?");
        $updateStmt->bind_param("ssdssi", $name, $description, $price, $category, $photo_url, $menuId);

        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Menu item updated successfully!";
            header("Location: ../public/menus.php");
            exit();
        } else {
            $_SESSION['message'] = "Error updating menu item: " . $updateStmt->error;
            header("Location: ../public/menus.php");
            exit();
        }
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/style.css" rel="stylesheet">
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
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body class="primary-color">
    <?php include '../public/navbar.php'; ?>

    <div class="container mt-4">
        <div class="form-container">
            <h2 class="mb-4 text-center">Edit Menu Item</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Menu Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($menu['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($menu['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($menu['price']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="drinks" <?php if ($menu['category'] == 'drinks') echo 'selected'; ?>>Drinks</option>
                        <option value="desserts" <?php if ($menu['category'] == 'desserts') echo 'selected'; ?>>Desserts</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control">
                    <small class="text-muted">Current Photo: <?php echo htmlspecialchars($menu['photo_url']); ?></small>
                </div>
                <button type="submit" class="btn btn-outline-primary w-100">Update Menu</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>