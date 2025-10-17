<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}

$username = $_SESSION['username'];

// Default categories
$all_categories = ['CPU','Motherboard','RAM','Storage','PSU','GPU','CPU Cooler','Case','Fan'];

// Handle POST
$budget = isset($_POST['budget']) ? (float)$_POST['budget'] : 0;
$selected_categories = isset($_POST['categories']) ? $_POST['categories'] : $all_categories;
$cpu_brand = isset($_POST['cpu_brand']) ? $_POST['cpu_brand'] : '';
$gpu_brand = isset($_POST['gpu_brand']) ? $_POST['gpu_brand'] : '';

// --- Budget calculator functions ---
function getMinBudget($conn, $categories) {
    $total = 0;
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("SELECT MIN(price) as min_price FROM products WHERE category = ?");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['min_price'] !== null) $total += $res['min_price'];
    }
    return $total;
}

function getMaxBudget($conn, $categories) {
    $total = 0;
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("SELECT MAX(price) as max_price FROM products WHERE category = ?");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['max_price'] !== null) $total += $res['max_price'];
    }
    return $total;
}

// Helper: fetch a compatible product
function getCompatibleProduct($conn, $category, $max_price = 0, $compat = [], $cheapest = false, $brand = '') {
    $sql = "SELECT * FROM products WHERE category = ?";

    // Brand filter
    if (!empty($brand)) {
        $sql .= " AND brand = '" . $conn->real_escape_string($brand) . "'";
    }

    if ($category === 'Motherboard' && isset($compat['cpu_socket'])) {
        $sql .= " AND socket = '" . $conn->real_escape_string($compat['cpu_socket']) . "'";
    }
    if ($category === 'RAM' && isset($compat['mb_ram_type'])) {
        $sql .= " AND ram_type = '" . $conn->real_escape_string($compat['mb_ram_type']) . "'";
    }
    if ($category === 'PSU' && isset($compat['required_power'])) {
        $sql .= " AND wattage >= " . intval($compat['required_power']);
    }

    if ($cheapest) {
        if ($max_price > 0) {
            $sql .= " AND price <= ? ORDER BY price ASC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sd", $category, $max_price);
        } else {
            $sql .= " ORDER BY price ASC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
        }
    } else {
        if ($max_price > 0) {
            $sql .= " AND price <= ? ORDER BY price DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sd", $category, $max_price);
        } else {
            $sql .= " ORDER BY price DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row && $category === 'Fan') {
        $fallback = $conn->query("SELECT * FROM products WHERE category = 'Fan' ORDER BY price ASC LIMIT 1");
        if ($fallback) $row = $fallback->fetch_assoc();
    }

    return $row;
}

// AJAX response
if (isset($_POST['ajax']) && $_POST['ajax'] == "1") {
    $min = getMinBudget($conn, $selected_categories);
    $max = getMaxBudget($conn, $selected_categories);
    echo json_encode(["min" => $min, "max" => $max]);
    exit;
}

$min_budget = getMinBudget($conn, $selected_categories);
$max_budget = getMaxBudget($conn, $selected_categories);

// --------------------------------------
// Build suggestion logic
// --------------------------------------
$suggested_build = [];
$total_price = 0;
$compat_data = [];

// Priority order for CPU -> Motherboard dependencies
$priority_order = ['CPU','Motherboard','RAM','Storage','PSU','GPU','CPU Cooler','Case','Fan'];

if ($budget > 0 && !empty($selected_categories)) {
    // 1) Pick cheapest compatible products first
    foreach ($priority_order as $category) {
        if (!in_array($category, $selected_categories)) continue;

        $brandFilter = '';
        if ($category === 'CPU' && !empty($cpu_brand)) $brandFilter = $cpu_brand;
        if ($category === 'GPU' && !empty($gpu_brand)) $brandFilter = $gpu_brand;

        $product = getCompatibleProduct($conn, $category, $budget, $compat_data, true, $brandFilter);
        if ($product) {
            $suggested_build[$category] = $product;
            $total_price += $product['price'];

            if ($category === 'CPU') {
                if (isset($product['socket'])) $compat_data['cpu_socket'] = $product['socket'];
                if (isset($product['tdp'])) $compat_data['cpu_tdp'] = $product['tdp'];
            }
            if ($category === 'Motherboard') {
                if (isset($product['ram_type'])) $compat_data['mb_ram_type'] = $product['ram_type'];
            }
            if ($category === 'GPU') {
                if (isset($product['tdp'])) $compat_data['gpu_tdp'] = $product['tdp'];
            }
            if ($category === 'PSU' && isset($compat_data['cpu_tdp'], $compat_data['gpu_tdp'])) {
                $compat_data['required_power'] = intval($compat_data['cpu_tdp']) + intval($compat_data['gpu_tdp']) + 200;
            }
        }
    }

    if ($total_price <= $budget) {
        $leftover = $budget - $total_price;

        // 2) Upgrade some parts using leftover budget
        foreach ($priority_order as $category) {
            if ($leftover <= 0) break;
            if (!isset($suggested_build[$category])) continue;

            $current = $suggested_build[$category];
            $current_price = floatval($current['price']);

            $upgrade_limit = $current_price + ($leftover * 0.5);

            $brandFilter = '';
            if ($category === 'CPU' && !empty($cpu_brand)) $brandFilter = $cpu_brand;
            if ($category === 'GPU' && !empty($gpu_brand)) $brandFilter = $gpu_brand;

            $better = getCompatibleProduct($conn, $category, $upgrade_limit, $compat_data, false, $brandFilter);

            if ($better && $better['id'] != $current['id'] && floatval($better['price']) > $current_price) {
                $diff = floatval($better['price']) - $current_price;
                $suggested_build[$category] = $better;
                $total_price += $diff;
                $leftover -= $diff;

                if ($category === 'CPU') {
                    if (isset($better['socket'])) $compat_data['cpu_socket'] = $better['socket'];
                    if (isset($better['tdp'])) $compat_data['cpu_tdp'] = $better['tdp'];
                }
                if ($category === 'Motherboard') {
                    if (isset($better['ram_type'])) $compat_data['mb_ram_type'] = $better['ram_type'];
                }
                if ($category === 'GPU') {
                    if (isset($better['tdp'])) $compat_data['gpu_tdp'] = $better['tdp'];
                }
                if ($category === 'PSU' && isset($compat_data['cpu_tdp'], $compat_data['gpu_tdp'])) {
                    $compat_data['required_power'] = intval($compat_data['cpu_tdp']) + intval($compat_data['gpu_tdp']) + 200;
                }
            }
        }
    } else {
        $suggested_build = [];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Master PC Build</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* your original CSS untouched */
        :root{
            --bg:#0d0d0d;
            --panel:rgba(30,30,30,0.8);
            --muted:#444;
            --accent:#2196f3; 
            --accent-2:#1976d2;
            --text:#fff; 
            --text-dark:#121212;
        }
        :root{
            --bg:#121212; --panel:#1e1e1e; --muted:#333;
            --accent:#2196f3; --accent-2:#1976d2; --text:#fff; --text-dark:#121212;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: url('../images/mbuild.jpg') no-repeat center center fixed; 
            background-size: cover;
            color: var(--text);
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('images/dashbg.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(4px);
            transform: scale(1.05);
            z-index: -2;
        }
        body::after {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: -1;
        }
        nav {
            position: sticky; top: 0; z-index: 1000;
            background: var(--panel); padding: 14px 30px;
            display: flex; justify-content: space-between; align-items: center;
            backdrop-filter: blur(6px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        .nav-left { font-size: 22px; font-weight: bold; color: var(--accent); }
        .nav-right { display: flex; gap: 25px; }
        .nav-right a { color: var(--text); text-decoration: none; font-weight: 600; }
        h1 { margin: 20px; }
        form.main { margin: 0 20px 10px; }
        .controls { display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
        input[type="number"], select { padding: 10px 12px; font-size: 16px; border-radius: 8px; border: 1px solid var(--muted); background:#181818; color:var(--text); }
        button, .btn { padding: 10px 16px; background: var(--accent-2); color: white; border: none; cursor: pointer; border-radius: 8px; font-weight: bold; transition: 0.2s; }
        button:hover, .btn:hover { filter: brightness(1.2); }
        .btn.alt { background: var(--accent); color: var(--text-dark); }
        .panel { background: var(--panel); margin: 20px; padding:16px; border-radius:12px; border:1px solid var(--muted); backdrop-filter: blur(6px); }
        .category-box { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 10px; }
        .category-card { flex: 1 1 120px; display:flex; align-items:center; justify-content:center; background:#1a1a1a; border:2px solid var(--muted); border-radius:12px; padding:14px; cursor:pointer; font-weight:700; text-align:center; transition:0.2s; }
        .category-card:hover { border-color: var(--accent); }
        .category-card input { display:none; }
        .category-card span { pointer-events:none; }
        .category-card:has(input:checked) { background: var(--accent); color: var(--text-dark); }
        .budget-info { margin-top: 10px; padding: 12px; border-radius: 10px; background:#181818; border:1px solid var(--muted); }
        .budget-info p { margin:6px 0; font-size:15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: var(--panel); border-radius: 12px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid var(--muted); text-align: center; }
        th { background: var(--accent-2); }
        .total { font-weight: bold; background: #2a2a2a; }
    </style>
</head>
<body>

<nav>
    <div class="nav-left">MASTER PC BUILD</div>
    <div class="nav-right">
        <a href="dashboard.php?tab=home">Dashboard</a>
        <a href="dashboard.php?tab=products">Products</a>
        <a href="dashboard.php?tab=cart">Cart</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<h1>Master Build Suggestion</h1>

<form method="post" class="main">
    <div class="controls panel">
        <label>Enter your budget (₱): </label>
        <input type="number" name="budget" min="0" step="100" value="<?php echo htmlspecialchars($budget); ?>" required>

        <!-- NEW: CPU Brand -->
        <label>CPU Brand:</label>
        <select name="cpu_brand">
            <option value="">Any</option>
            <option value="Intel" <?php echo $cpu_brand==='Intel'?'selected':''; ?>>Intel</option>
            <option value="AMD" <?php echo $cpu_brand==='AMD'?'selected':''; ?>>AMD</option>
        </select>

        <!-- NEW: GPU Brand -->
        <label>GPU Brand:</label>
        <select name="gpu_brand">
            <option value="">Any</option>
            <option value="NVIDIA" <?php echo $gpu_brand==='NVIDIA'?'selected':''; ?>>NVIDIA</option>
            <option value="AMD" <?php echo $gpu_brand==='AMD'?'selected':''; ?>>AMD</option>
            <option value="Intel" <?php echo $gpu_brand==='Intel'?'selected':''; ?>>Intel</option>
        </select>

        <button type="submit">Suggest Build</button>
    </div>

    <div class="panel">
        <h3>Choose categories</h3>
        <div class="category-box">
            <label class="category-card">
                <input type="checkbox" id="selectAll">
                <span>All</span>
            </label>
            <?php foreach ($all_categories as $cat): ?>
                <label class="category-card">
                    <input type="checkbox" class="categoryCheck" name="categories[]" value="<?php echo $cat; ?>" <?php echo in_array($cat,$selected_categories)?'checked':''; ?>>
                    <span><?php echo $cat; ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="budget-info" id="budgetInfo">
            <p><strong>💡 Minimum required budget:</strong> ₱<?php echo number_format($min_budget,2); ?></p>
            <p><strong>⚡ High-specs budget:</strong> ₱<?php echo number_format($max_budget,2); ?></p>
        </div>
    </div>
</form>

<div class="panel">
<?php if ($budget > 0 && !empty($suggested_build)): ?>
    <h2>Suggested Compatible Build (₱<?php echo number_format($total_price,2); ?>)</h2>
    <form method="post" action="add_all_to_cart.php">
        <input type="hidden" name="budget" value="<?php echo $budget; ?>">
        <table>
            <tr>
                <th>Category</th><th>Product</th><th>Price</th><th>Remove</th>
            </tr>
            <?php foreach ($suggested_build as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?> (<?php echo htmlspecialchars($item['brand']); ?>)</td>
                <td>₱<?php echo number_format($item['price'],2); ?></td>
                <td><input type="checkbox" name="remove[]" value="<?php echo $item['id']; ?>"> remove</td>
            </tr>
            <input type="hidden" name="items[]" value="<?php echo $item['id']; ?>">
            <?php endforeach; ?>
            <tr class="total"><td colspan="2">TOTAL</td><td colspan="2">₱<?php echo number_format($total_price,2); ?></td></tr>
        </table>
        <div style="margin-top:10px">
            <button type="submit" class="btn">➕ Add All to Cart</button>
            <button type="button" class="btn alt" onclick="window.print()">🖨 Print Build</button>
        </div>
    </form>
<?php elseif ($budget > 0): ?>
    <p>Budget is not enough to suggest all selected categories. (Minimum required: ₱<?php echo number_format($min_budget,2); ?>)</p>
<?php endif; ?>
</div>

<script>
const selectAll = document.getElementById('selectAll');
const categoryChecks = document.querySelectorAll('.categoryCheck');

// Handle select all checkbox
selectAll.addEventListener('change', function() {
    categoryChecks.forEach(cb => cb.checked = this.checked);
    updateBudgets();
});

// Update select all if any individual checkbox changes
categoryChecks.forEach(cb => cb.addEventListener('change', function() {
    selectAll.checked = [...categoryChecks].every(c => c.checked);
    updateBudgets();
}));

// AJAX budget update function
function updateBudgets() {
    let formData = new FormData();
    document.querySelectorAll('.categoryCheck:checked').forEach(cb => formData.append('categories[]', cb.value));
    formData.append('ajax','1');

    fetch("", {method:"POST", body:formData})
        .then(res => res.json())
        .then(data => {
            document.getElementById('budgetInfo').innerHTML = `
                <p><strong>💡 Minimum required budget:</strong> ₱${Number(data.min).toLocaleString()}</p>
                <p><strong>⚡ High-specs budget:</strong> ₱${Number(data.max).toLocaleString()}</p>
            `;
        })
        .catch(err => console.error('Budget update error:', err));
}

// Initialize selectAll state on page load
window.addEventListener('load', () => {
    selectAll.checked = [...categoryChecks].every(c => c.checked);
});
</script>


</body>
</html>
