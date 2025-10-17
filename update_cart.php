<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $cart_id => $quantity) {
        $cart_id = (int)$cart_id;
        $qty     = (int)$quantity;

        if ($qty >= 1) {
            $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $stmt->bind_param("iii", $qty, $cart_id, $user_id);
            $stmt->execute();
        } else {
            // If qty < 1, remove the item
            $del = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
            $del->bind_param("ii", $cart_id, $user_id);
            $del->execute();
        }
    }
}

header("Location: dashboard.php?tab=cart");
exit;
