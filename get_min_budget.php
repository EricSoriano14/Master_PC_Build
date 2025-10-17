<?php
include 'db.php';

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);
$categories = $input['categories'] ?? [];

$total = 0;
foreach ($categories as $cat) {
    $stmt = $conn->prepare("SELECT MIN(price) as min_price FROM products WHERE category = ?");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && $result['min_price']) {
        $total += $result['min_price'];
    }
}

echo json_encode(['min_budget' => $total]);
