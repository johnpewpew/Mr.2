<?php
include 'config.php';

$item_id = $_GET['item_id'] ?? 0;

// Fetch add-ons for the given item ID
$query = $conn->prepare("SELECT * FROM add_ons WHERE item_id = ?");
$query->bind_param("i", $item_id);
$query->execute();
$result = $query->get_result();

$addons = [];
while ($row = $result->fetch_assoc()) {
    $addons[] = $row;
}

header('Content-Type: application/json');
echo json_encode($addons);

