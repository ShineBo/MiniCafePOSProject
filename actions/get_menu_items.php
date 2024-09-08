<?php
require '../config/config.php';
header("Access-Control-Allow-Origin: *");

$query = "SELECT id, name, price, category, photo_url FROM menu_items ORDER BY name ASC";
$result = $conn->query($query);

$menuItems = [];

while ($row = $result->fetch_assoc()) {
    $menuItems[] = $row;
}

header('Content-Type: application/json');
echo json_encode($menuItems);
?>