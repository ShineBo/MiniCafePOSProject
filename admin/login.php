<?php
ob_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/config.php';
echo "Config included<br>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "Form submitted<br>";
    $username = $_POST['username'];
    $password = $_POST['password'];

    echo "Username: $username<br>";
    echo "Password: $password<br>";

    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    echo "Number of rows: " . $stmt->num_rows . "<br>";

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $stored_password);
        $stmt->fetch();

        echo "Stored password: $stored_password<br>";

        if ($password === $stored_password) {
            echo "Password verified<br>";
            $_SESSION['admin_id'] = $id;
            $_SESSION['username'] = $username;
            
            echo "Redirect would happen now<br>";
            header("Location: ../public/menus.php");
            exit();
        } else {
            echo "Invalid password<br>";
        }
    } else {
        echo "Invalid username<br>";
    }

    $stmt->close();
}

ob_end_flush();
?>