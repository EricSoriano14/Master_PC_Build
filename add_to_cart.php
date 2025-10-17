<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!empty($_POST['product_id'])) {
    $user_id   = (int)$_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];

    // Check if already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // Auto increment quantity
        $newQty = (int)$row['quantity'] + 1;
        $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
        $upd->bind_param("iii", $newQty, $row['id'], $user_id);
        $upd->execute();
    } else {
        // New item → qty = 1
        $qty = 1;
        $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $ins->bind_param("iii", $user_id, $product_id, $qty);
        $ins->execute();
    }
}

header("Location: dashboard.php?tab=cart");
exit;
