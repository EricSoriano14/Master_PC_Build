<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background:#121212; color:#fff; }
        table { width:100%; border-collapse: collapse; margin:20px auto; background:#1e1e1e; border-radius:10px; overflow:hidden; }
        th, td { padding:12px; text-align:center; border-bottom:1px solid #333; }
        th { background:#ff5722; }
        .total-row { font-weight:bold; background:#2a2a2a; }
    </style>
</head>
<body>
<h2 style="text-align:center;">🛒 Your Cart</h2>
<table>
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $total += $row['subtotal']; ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>₱<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td>₱<?php echo number_format($row['subtotal'], 2); ?></td>
        </tr>
    <?php endwhile; ?>
    <tr class="total-row">
        <td colspan="3">TOTAL</td>
        <td>₱<?php echo number_format($total, 2); ?></td>
    </tr>
</table>
</body>
</html>
