<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: index.php"); 
    exit;
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'barangay' => '',
    'street' => '',
    'postal_code' => '',
    'municipality' => '',
    'province' => '',
    'mobile' => ''
];

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';

$products_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $products_per_page;

$category_filter = isset($_GET['category']) && $_GET['category'] != "" ? $_GET['category'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>PC Build Dashboard</title>
    <style>
        :root {
            --bg: #0d0d0d;
            --panel: #1a1a1a;
            --muted: #2c2c2c;
            --accent: #0072ff;
            --accent2: #00c6ff;
            --text: #f5f5f5;
            --text-dark: #121212;
        }
        body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    color: var(--text);
    position: relative;
    min-height: 100vh;
    overflow-x: hidden;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: url('images/dashbg.jpg') no-repeat center center fixed;
    background-size: cover;

    filter: blur(4px);   /* Adjust blur level (2px = soft, 8px = strong) */
    transform: scale(1.05); /* Prevent edges from showing when blurred */
    z-index: -2;
}


body::after {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    z-index: -1;
}

        header {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            padding: 18px 30px;
            font-size: 28px;
            font-weight: bold;
            color: var(--text-dark);
            letter-spacing: 1px;
            text-align: left;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        nav {
            background: var(--panel);
            padding: 12px 30px;
            display: flex;
            justify-content: right;
            gap: 30px;
            border-bottom: 1px solid var(--muted);
        }
        nav a {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: var(--text-dark);
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            padding: 10px 18px;
            border-radius: 25px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        nav a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,114,255,0.4);
        }
        .container {
            padding: 30px;
            max-width: 1400px;
            margin: auto;
        }
        .success-message {
            background: #2e7d32;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
            justify-content: center;
        }
        .category-tabs a {
            padding: 10px 20px;
            background: var(--panel);
            border: 2px solid var(--muted);
            border-radius: 25px;
            color: var(--text);
            text-decoration: none;
            font-weight: bold;
            transition: all 0.25s ease;
        }
        .category-tabs a:hover {
            border-color: var(--accent2);
            background: #2a2a2a;
            transform: translateY(-3px);
        }
        .category-tabs a.active {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-color: var(--accent);
            color: var(--text-dark);
            transform: scale(1.05);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }
        .product-card {
            background: var(--panel);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            overflow: hidden;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 18px rgba(0,114,255,0.4);
        }
        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .product-card h4 {
            margin: 15px 0 5px;
            font-size: 19px;
        }
        .product-card p {
            color: var(--accent2);
            font-weight: bold;
            font-size: 20px;
            margin: 5px 0 15px;
        }
        .product-card form {
            margin-top: auto;
        }
        .product-card button {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: white;
            padding: 12px;
            border: none;
            border-radius: 0 0 12px 12px;
            cursor: pointer;
            font-size: 16px;
            transition: opacity 0.2s ease;
            width: 100%;
        }
        .product-card button:hover {
            opacity: 0.9;
        }
        table.cart {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: var(--panel);
            border-radius: 8px;
            overflow: hidden;
        }
        table.cart th, table.cart td {
            padding: 12px;
            border-bottom: 1px solid var(--muted);
            text-align: center;
        }
        table.cart th {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: var(--text-dark);
        }
        table.cart tr.total-row td {
            font-weight: bold;
            background: #2a2a2a;
        }
        .pagination {
            margin-top: 30px;
            text-align: center;
            font-size: 18px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .pagination a {
            background: var(--panel);
            color: var(--text);
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            border: 2px solid var(--muted);
            transition: all 0.25s ease;
        }
        .pagination a:hover {
            background: var(--accent2);
            border-color: var(--accent2);
            color: var(--text-dark);
        }
        .pagination a.active {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-color: var(--accent);
            color: var(--text-dark);
            transform: scale(1.05);
        }
        .account-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .profile-panel {
            flex: 0 0 280px;
            background: var(--panel);
            border-radius: 12px;
            padding: 25px;
            text-align: left;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--accent);
        }
        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-panel h2 {
            margin: 10px 0 5px;
            font-size: 22px;
        }
        .profile-panel p {
            color: var(--accent2);
            font-size: 16px;
        }
        .account-form {
            flex: 1;
            background: var(--panel);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            font-size: 16px;
        }
        .account-form h3 {
            margin-top: 0;
            color: var(--accent2);
            border-bottom: 1px solid var(--muted);
            padding-bottom: 6px;
            margin-bottom: 15px;
        }
        .account-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
        }
        .account-form input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid var(--muted);
            background: #2c2c2c;
            color: white;
            transition: all 0.2s ease;
        }
        .account-form input:focus {
            border-color: var(--accent);
            outline: none;
            background: #1a1a1a;
        }
        .account-form button {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: white;
            padding: 14px 20px;
            font-size: 17px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: opacity 0.25s ease;
        }
        .account-form button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<header>MASTER PC BUILD</header>

<nav>
    <a href="?tab=home">Home</a>
    <a href="master_build.php">Master Build</a>
    <a href="?tab=products">Products</a>
    <a href="?tab=cart">Cart</a>
    <a href="?tab=account">Account</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
    <div class="success-message">✅ Account updated successfully!</div>
<?php endif; ?>

<?php if ($tab == 'home'): ?>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Explore our PC components and start building your dream rig!</p>
    <p>Picture of every product items coming soon! </p>

<?php elseif ($tab == 'products'): ?>
    <h1>Products</h1>

    <div class="category-tabs">
        <a href="?tab=products" class="<?php echo !$category_filter ? 'active' : ''; ?>">All</a>
        <?php
        $categories_result = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
        while ($cat = $categories_result->fetch_assoc()):
            $isActive = ($category_filter == $cat['category']) ? 'active' : '';
        ?>
            <a href="?tab=products&category=<?php echo urlencode($cat['category']); ?>" class="<?php echo $isActive; ?>">
                <?php echo htmlspecialchars($cat['category']); ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="product-grid">
        <?php
        if ($category_filter) {
            $products_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? LIMIT ? OFFSET ?");
            $products_stmt->bind_param("sii", $category_filter, $products_per_page, $offset);
        } else {
            $products_stmt = $conn->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
            $products_stmt->bind_param("ii", $products_per_page, $offset);
        }
        $products_stmt->execute();
        $products_result = $products_stmt->get_result();

        if ($products_result->num_rows > 0):
            while ($product = $products_result->fetch_assoc()): ?>
                <div class="product-card">
                   <img src="<?php echo !empty($item['image']) ? $item['image'] : 'images/placeholder.png'; ?>" alt="Product">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>₱<?php echo number_format($product['price'], 2); ?></p>
                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile;
        else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </div>

    <div class="pagination">
    <?php
    if ($category_filter) {
        $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category = ?");
        $count_stmt->bind_param("s", $category_filter);
        $count_stmt->execute();
        $total_products_result = $count_stmt->get_result();
    } else {
        $total_products_result = $conn->query("SELECT COUNT(*) AS total FROM products");
    }
    $total_products = $total_products_result->fetch_assoc()['total'];
    $total_pages = ceil($total_products / $products_per_page);

    $max_links = 10;
    $start = floor(($page - 1) / $max_links) * $max_links + 1;
    $end = min($start + $max_links - 1, $total_pages);

    if ($start > 1): ?>
        <a href="?tab=products&page=<?php echo $start - 1; ?>&category=<?php echo urlencode($category_filter); ?>">« Prev</a>
    <?php endif;

    for ($i = $start; $i <= $end; $i++): ?>
        <a href="?tab=products&page=<?php echo $i; ?>&category=<?php echo urlencode($category_filter); ?>" 
           class="<?php echo ($i == $page) ? 'active' : ''; ?>">
           <?php echo $i; ?>
        </a>
    <?php endfor;

    if ($end < $total_pages): ?>
        <a href="?tab=products&page=<?php echo $end + 1; ?>&category=<?php echo urlencode($category_filter); ?>">Next »</a>
    <?php endif; ?>
    </div>

<?php elseif ($tab == 'cart'): ?>
    <h1>Your Cart</h1>
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo "<p>Please log in to view your cart.</p>";
    } else {
        $user_id = (int)$_SESSION['user_id'];
        $sql = "SELECT c.id AS cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $total = 0;
            echo "<table class='cart'>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                $total += $row['subtotal'];
                echo "<tr>
                        <td>".htmlspecialchars($row['name'])."</td>
                        <td>₱".number_format($row['price'],2)."</td>
                        <td>".$row['quantity']."</td>
                        <td>₱".number_format($row['subtotal'],2)."</td>
                        <td>
                            <form method='POST' action='remove_from_cart.php' style='margin:0;'>
                                <input type='hidden' name='cart_id' value='".$row['cart_id']."'>
                                <button type='submit' style='background:#e64a19;color:#fff;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;'>Remove</button>
                            </form>
                        </td>
                      </tr>";
            }
            echo "<tr class='total-row'>
                    <td colspan='3'>TOTAL</td>
                    <td colspan='2'>₱".number_format($total,2)."</td>
                  </tr>";
            echo "</table>";

            echo "<div style='margin-top:20px; text-align:right;'>
                    <a href='?tab=products' style='background:var(--accent2);color:#121212;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;margin-right:10px;'> Continue Shopping</a>
                    <a href='checkout.php' style='background:#4caf50;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;'>Proceed to Checkout </a>
                  </div>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
    }
    ?>

<?php elseif ($tab == 'account'): ?>
    <h1>Account Settings</h1>
    <div class="account-container">

        <!-- Profile Section -->
        <div class="profile-panel">
            <div class="profile-pic">
                <img src="uploads/<?php echo !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'profile_placeholder.png'; ?>" alt="Profile Picture">
            </div>
            <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
            <p>@<?php echo htmlspecialchars($username); ?></p>

            <hr style="border: 1px solid var(--muted); margin: 15px 0;">

            <h3 style="margin:10px 0; color: var(--accent2);">Delivery Information</h3>
            <p><strong>Address:</strong><br>
                <?php echo htmlspecialchars($user['street'] . ', ' . $user['barangay'] . ', ' . $user['municipality'] . ', ' . $user['province'] . ', ' . $user['postal_code']); ?>
            </p>
            <p><strong>Mobile Number:</strong><br>
                <?php echo htmlspecialchars($user['mobile']); ?>
            </p>
        </div>

        <form class="account-form" action="update_account.php" method="POST">
            <h3>Personal Information</h3>
            <label>First Name</label>
            <input type="text" name="first_name" value="">
            <label>Middle Name</label>
            <input type="text" name="middle_name" value="">
            <label>Last Name</label>
            <input type="text" name="last_name" value="">

            <h3>Address Information</h3>
            <label>Street</label>
            <input type="text" name="street" value="">
            <label>Barangay</label>
            <input type="text" name="barangay" value="">
            <label>Municipality</label>
            <input type="text" name="municipality" value="">
            <label>Province</label>
            <input type="text" name="province" value="">
            <label>Postal Code</label>
            <input type="text" name="postal_code" value="">

            <h3>Contact</h3>
            <label>Mobile Number</label>
            <input type="text" name="mobile" value="">

            <button type="submit"> Save Changes</button>
        </form>
    </div>
<?php endif; ?>

</div>
</body>
</html>

