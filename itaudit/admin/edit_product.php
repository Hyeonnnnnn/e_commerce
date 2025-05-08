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
    
    // Handle file upload
    $product_picture = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $temp_name = $_FILES['product_image']['tmp_name'];
        $filename = $_FILES['product_image']['name'];
        
        // Move the uploaded file to the images directory
        if (move_uploaded_file($temp_name, "../images/$filename")) {
            $product_picture = $filename;
        }
    }

    if (updateProduct($id, $name, $category, $description, $price, $stock, $tax_rate, $product_picture)) {
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
            <div class="message" id="message-notification"><?php echo htmlspecialchars($message); ?></div>
            <script>
                // Auto-hide message after 3 seconds
                setTimeout(function() {
                    var messageElement = document.getElementById('message-notification');
                    if (messageElement) {
                        messageElement.style.opacity = '1';
                        // Fade out effect
                        var fadeEffect = setInterval(function() {
                            if (messageElement.style.opacity > 0) {
                                messageElement.style.opacity -= 0.1;
                            } else {
                                clearInterval(fadeEffect);
                                messageElement.style.display = 'none';
                            }
                        }, 50);
                    }
                }, 3000);
            </script>
        <?php endif; ?>

        <section class="edit-product">
            <h2>Edit Product</h2>
            <form method="POST" action="" class="add-product-form" enctype="multipart/form-data">
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

                <div class="form-group">
                    <label for="product_image">Product Image:</label>
                    <?php if (!empty($product['product_picture'])): ?>
                    <div class="current-image">
                        <p>Current image: <?php echo htmlspecialchars($product['product_picture']); ?></p>
                        <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 200px; max-height: 200px; margin: 10px 0;">
                    </div>
                    <?php endif; ?>
                    <input type="file" id="product_image" name="product_image" accept="image/*">
                    <p class="help-text">Leave empty to keep the current image</p>
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