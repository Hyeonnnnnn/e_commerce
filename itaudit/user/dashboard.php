<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$cart = $_SESSION['cart'] ?? [];
$selectedCategory = $_GET['category'] ?? '';

if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = (int)$_POST['quantity'] ?? 1;
    
    if (!isset($cart[$productId])) {
        $cart[$productId] = 0;
    }
    $cart[$productId] += $quantity;
    $_SESSION['cart'] = $cart;
    $message = 'Product added to cart';
}

if (isset($_POST['checkout'])) {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = (int)$_POST['quantity'] ?? 1;
    $product = getProduct($productId);
    
    if ($product && $product['stock_quantity'] >= $quantity) {
        $_SESSION['checkout_item'] = [
            'id' => $productId,
            'quantity' => $quantity,
            'price' => $product['price'],
            'name' => $product['name'],
            'tax_rate' => $product['tax_rate']
        ];
        header('Location: checkout.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Parts Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard">
    <nav>
        <div class="nav-brand">Computer Parts Shop</div>
        <div class="nav-items">
            <a href="cart.php">Cart (<?php echo array_sum($cart); ?>)</a>
            <a href="../includes/logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <section class="filter-controls">
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label for="category">Filter by Category:</label>
                    <select id="category" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php
                        $categories = ['CPU', 'GPU', 'RAM', 'Storage', 'Motherboard', 'PSU', 'Case', 'Peripheral'];
                        foreach ($categories as $category):
                            $selected = ($category === $selectedCategory) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $category; ?>" <?php echo $selected; ?>>
                                <?php echo $category; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </section>

        <section class="products">
            <h2><?php echo $selectedCategory ? htmlspecialchars($selectedCategory) : 'All Products'; ?></h2>
            <div class="product-grid">
                <?php
                $products = getAllProducts('name', $selectedCategory);
                if ($products):
                    foreach ($products as $product):
                ?>
                    <div class="product-card">
                        <?php if (!empty($product['product_picture'])): ?>
                            <div class="product-image">
                                <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price">₱<?php echo number_format($product['price'], 2); ?></p>
                        <p class="stock">Stock: <?php echo $product['stock_quantity']; ?> units</p>
                        <input type="number" class="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" 
                               id="quantity_<?php echo $product['id']; ?>">
                        <div class="button-group">
                            <form method="POST" action="" style="flex: 1;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1" 
                                       id="quantity_hidden_<?php echo $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                            <form method="POST" action="" style="flex: 1;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1" 
                                       id="quantity_checkout_<?php echo $product['id']; ?>">
                                <button type="submit" name="checkout" class="checkout">Buy Now</button>
                            </form>
                        </div>
                    </div>
                    <script>
                        document.getElementById('quantity_<?php echo $product['id']; ?>').addEventListener('change', function() {
                            document.getElementById('quantity_hidden_<?php echo $product['id']; ?>').value = this.value;
                            document.getElementById('quantity_checkout_<?php echo $product['id']; ?>').value = this.value;
                        });
                    </script>
                <?php 
                    endforeach;
                else:
                ?>
                    <p>No products available in this category.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // Add smooth scrolling for filter changes
        document.querySelector('.filter-form select').addEventListener('change', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>