<?php
session_start();
include 'db.php';

// ✅ Allow only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

/* ---------- USERS ---------- */
if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $municipality = $_POST['municipality'];
    $province = $_POST['province'];
    $postal_code = $_POST['postal_code'];
    $mobile = $_POST['mobile'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users 
        (username, email, password, first_name, middle_name, last_name, street, barangay, municipality, province, postal_code, mobile, role) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", 
        $username, $email, $password, $first_name, $middle_name, $last_name, 
        $street, $barangay, $municipality, $province, $postal_code, $mobile, $role);
    $stmt->execute();
}

if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* ---------- PRODUCTS ---------- */
if (isset($_POST['insert_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $socket = $_POST['socket'];
    $price = $_POST['price'];
    $ram_type = $_POST['ram_type'];
    $wattage = $_POST['wattage'];
    $tdp = $_POST['tdp'];

    $stmt = $conn->prepare("INSERT INTO products (name, category, brand, socket, price, ram_type, wattage, tdp) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsii", $name, $category, $brand, $socket, $price, $ram_type, $wattage, $tdp);
    $stmt->execute();
}

if (isset($_POST['update_price'])) {
    $id = $_POST['product_id'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE products SET price=? WHERE id=?");
    $stmt->bind_param("di", $price, $id);
    $stmt->execute();
}

if (isset($_POST['delete_product'])) {
    $id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* ---------- PAGINATION ---------- */
$limit = 5; // per page

// Users
$user_page = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
$user_offset = ($user_page - 1) * $limit;
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$user_pages = ceil($total_users / $limit);
$users = $conn->query("SELECT id, username, email, role FROM users LIMIT $limit OFFSET $user_offset");

// Products
$product_page = isset($_GET['product_page']) ? (int)$_GET['product_page'] : 1;
$product_offset = ($product_page - 1) * $limit;
$total_products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$product_pages = ceil($total_products / $limit);
$products = $conn->query("SELECT id, name, category, brand, price FROM products LIMIT $limit OFFSET $product_offset");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width: 250px; height: 100vh; display: flex; flex-direction: column;">
        <h4>Admin Panel</h4>
        <ul class="nav flex-column flex-grow-1">
            <li class="nav-item"><a href="#users" class="nav-link text-white">Manage Users</a></li>
            <li class="nav-item"><a href="#products" class="nav-link text-white">Manage Products</a></li>
        </ul>
        <a href="logout.php" class="btn btn-outline-light mt-auto">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container-fluid p-4">

        <!-- Manage Users -->
        <section id="users">
            <h3>Manage Users</h3>
            <button class="btn btn-primary mb-2" data-bs-toggle="collapse" data-bs-target="#createUserForm">Create New User</button>
            <div class="collapse" id="createUserForm">
                <form method="POST" class="row g-2 mb-3">
                    <input type="text" name="username" placeholder="Username" required class="form-control">
                    <input type="email" name="email" placeholder="Email" required class="form-control">
                    <input type="password" name="password" placeholder="Password" required class="form-control">
                    <input type="text" name="first_name" placeholder="First Name" class="form-control">
                    <input type="text" name="middle_name" placeholder="Middle Name" class="form-control">
                    <input type="text" name="last_name" placeholder="Last Name" class="form-control">
                    <input type="text" name="street" placeholder="Street" class="form-control">
                    <input type="text" name="barangay" placeholder="Barangay" class="form-control">
                    <input type="text" name="municipality" placeholder="Municipality" class="form-control">
                    <input type="text" name="province" placeholder="Province" class="form-control">
                    <input type="text" name="postal_code" placeholder="Postal Code" class="form-control">
                    <input type="text" name="mobile" placeholder="Mobile" class="form-control">
                    <select name="role" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="create_user" class="btn btn-success">Create</button>
                </form>
            </div>

            <table class="table table-striped table-bordered">
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Action</th></tr>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['role'] ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <!-- Pagination for Users -->
<nav>
  <ul class="pagination justify-content-center">
    <!-- Previous Button -->
    <?php if ($user_page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?user_page=<?= $user_page - 1 ?>&product_page=<?= $product_page ?>">&laquo;</a>
      </li>
    <?php endif; ?>

    <?php
    // Window size = 5 pages
    $start = max(1, $user_page - 2);
    $end = min($user_pages, $user_page + 2);

    if ($end - $start < 4) {
        if ($start == 1) {
            $end = min(5, $user_pages);
        } else {
            $start = max(1, $end - 4);
        }
    }
    ?>

    <!-- Page Numbers -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
      <li class="page-item <?= $i == $user_page ? 'active' : '' ?>">
        <a class="page-link" href="?user_page=<?= $i ?>&product_page=<?= $product_page ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <!-- Next Button -->
    <?php if ($user_page < $user_pages): ?>
      <li class="page-item">
        <a class="page-link" href="?user_page=<?= $user_page + 1 ?>&product_page=<?= $product_page ?>">&raquo;</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>


        </section>

        <hr>

        <!-- Manage Products -->
        <section id="products">
            <h3>Manage Products</h3>
            <button class="btn btn-success mb-2" data-bs-toggle="collapse" data-bs-target="#insertProductForm">Insert Product</button>
            <div class="collapse" id="insertProductForm">
                <form method="POST" class="row g-2 mb-3">
                    <input type="text" name="name" placeholder="Product Name" required class="form-control">
                    <input type="text" name="category" placeholder="Category" required class="form-control">
                    <input type="text" name="brand" placeholder="Brand" class="form-control">
                    <input type="text" name="socket" placeholder="Socket" class="form-control">
                    <input type="number" step="0.01" name="price" placeholder="Price" required class="form-control">
                    <input type="text" name="ram_type" placeholder="RAM Type" class="form-control">
                    <input type="number" name="wattage" placeholder="Wattage" class="form-control">
                    <input type="number" name="tdp" placeholder="TDP" class="form-control">
                    <button type="submit" name="insert_product" class="btn btn-primary">Insert</button>
                </form>
            </div>

            <table class="table table-striped table-bordered">
                <tr><th>ID</th><th>Name</th><th>Category</th><th>Brand</th><th>Price</th><th>Action</th></tr>
                <?php while($row = $products->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= $row['brand'] ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <!-- Update Price -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <input type="number" step="0.01" name="price" placeholder="New Price" required>
                            <button type="submit" name="update_price" class="btn btn-warning btn-sm">Update</button>
                        </form>
                        <!-- Delete -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <!-- Pagination for Products -->
<nav>
  <ul class="pagination justify-content-center">
    <!-- Previous Button -->
    <?php if ($product_page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?product_page=<?= $product_page - 1 ?>&user_page=<?= $user_page ?>">&laquo;</a>
      </li>
    <?php endif; ?>

    <?php
    // Window size = 5 pages
    $start = max(1, $product_page - 2);
    $end = min($product_pages, $product_page + 2);

    if ($end - $start < 4) {
        if ($start == 1) {
            $end = min(5, $product_pages);
        } else {
            $start = max(1, $end - 4);
        }
    }
    ?>

    <!-- Page Numbers -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
      <li class="page-item <?= $i == $product_page ? 'active' : '' ?>">
        <a class="page-link" href="?product_page=<?= $i ?>&user_page=<?= $user_page ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <!-- Next Button -->
    <?php if ($product_page < $product_pages): ?>
      <li class="page-item">
        <a class="page-link" href="?product_page=<?= $product_page + 1 ?>&user_page=<?= $user_page ?>">&raquo;</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>

        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
