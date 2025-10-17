<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['items']) && is_array($_POST['items'])) {
    $user_id = (int)$_SESSION['user_id'];
    $remove = isset($_POST['remove']) ? $_POST['remove'] : [];

    foreach ($_POST['items'] as $product_id) {
        $product_id = (int)$product_id;
        if (in_array($product_id, $remove)) continue;

        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=? LIMIT 1");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $newQty = (int)$row['quantity'] + 1;
            $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $upd->bind_param("iii", $newQty, $row['id'], $user_id);
            $upd->execute();
        } else {
            $qty = 1;
            $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $ins->bind_param("iii", $user_id, $product_id, $qty);
            $ins->execute();
        }
    }
}

header("Location: dashboard.php?tab=cart");
exit;
