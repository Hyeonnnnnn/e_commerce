<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$product = null;

if (isset($_GET['id'])) {
    $product = getProduct($_GET['id']);
    if (!$product) {
        header('Location: dashboard.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $tax_rate = $_POST['tax_rate'] ?? 12.00;

    if (updateProduct($id, $name, $category, $description, $price, $stock, $tax_rate)) {
        header('Location: dashboard.php?action=inventory&updated=1');
        exit();
    } else {
        $message = 'Failed to update product';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Computer Parts Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard">
    <nav>
        <div class="nav-brand">Computer Parts Shop - Admin</div>
        <div class="nav-items">
            <a href="dashboard.php?action=inventory">Back to Inventory</a>
            <a href="../includes/logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <section class="edit-product">
            <h2>Edit Product</h2>
            <form method="POST" action="" class="add-product-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <?php
                        $categories = ['CPU', 'GPU', 'RAM', 'Storage', 'Motherboard', 'PSU', 'Case', 'Peripheral'];
                        foreach ($categories as $cat):
                            $selected = ($cat === $product['category']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $cat; ?>" <?php echo $selected; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="tax_rate">Tax Rate (%):</label>
                    <input type="number" id="tax_rate" name="tax_rate" step="0.01" value="<?php echo htmlspecialchars($product['tax_rate']); ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit">Update Product</button>
                    <a href="dashboard.php?action=inventory" class="button">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>