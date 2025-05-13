<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$cart = $_SESSION['cart'] ?? [];
$selectedCategory = $_GET['category'] ?? '';
$searchQuery = $_GET['search'] ?? '';

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
    <title>Computer Parts Shop - Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c5282;
            --primary-light: #4299e1;
            --primary-dark: #2a4365;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --gradient-primary: linear-gradient(135deg, #2c5282 0%, #4299e1 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: var(--dark-gray);
            background-color: #f5f7fa;
            background-image: linear-gradient(rgba(245, 247, 250, 0.95), rgba(245, 247, 250, 0.95)), 
                              url('https://images.unsplash.com/photo-1587202372616-b43abea06c2a?ixlib=rb-4.0.3');
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        /* Header/Navbar */
        .dashboard-navbar {
            background: var(--gradient-primary);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--white);
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            padding: 0;
        }
        
        .navbar-brand i {
            margin-right: 10px;
        }
        
        .navbar-nav .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            padding: 0.7rem 1.2rem;
            margin: 0 0.2rem;
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-nav .nav-link i {
            margin-right: 5px;
        }
        
        .cart-badge {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        
        .cart-badge .badge {
            position: relative;
            top: -10px;
            left: -5px;
            padding: 0.35em 0.65em;
            border-radius: 50%;
            background-color: #e53e3e;
            color: white;
            font-size: 0.75rem;
        }
        
        /* Main Content Area */
        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Alert Messages */
        .alert-custom {
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            animation: fadeInDown 0.5s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-custom i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .alert-success {
            background-color: #f0fff4;
            border-color: #48bb78;
            color: #2f855a;
        }
        
        /* Filter Section */
        .filter-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .filter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        .filter-card h5 {
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0 0 1rem;
        }
        
        .form-select {
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            border: 1px solid #cbd5e0;
            transition: all 0.2s ease;
        }
        
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }
        
        /* Product Grid */
        .products-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--primary-dark);
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 3px;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            grid-gap: 1.5rem;
        }
        
        .product-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-category {
            display: inline-block;
            background-color: rgba(66, 153, 225, 0.1);
            color: var(--primary);
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 0.75rem;
            font-weight: 500;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark-gray);
        }
        
        .product-description {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            flex-grow: 1;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .product-stock {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 1rem;
        }
        
        .quantity-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .button-group {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            color: var(--primary);
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-primary:hover, .btn-outline:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary:hover {
            box-shadow: 0 5px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-outline:hover {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 0;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
        }
        
        .empty-state h3 {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #718096;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-microchip fa-lg"></i>
                Computer Parts Shop
            </a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if (array_sum($cart) > 0): ?>
                                <span class="badge"><?php echo array_sum($cart); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <?php if ($message): ?>
            <div class="alert-custom alert-success fade show" id="message-notification">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
            <script>
                // Auto-hide message after 3 seconds
                setTimeout(function() {
                    document.getElementById('message-notification').classList.add('fade');
                    setTimeout(function() {
                        document.getElementById('message-notification').style.display = 'none';
                    }, 500);
                }, 3000);
            </script>
        <?php endif; ?>

        <div class="row">
            <!-- Filter Section -->
            <div class="col-md-3">
                <div class="filter-card">
                    <h5><i class="fas fa-filter me-2"></i>Filter Products</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search Products</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                    placeholder="Search by name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                                <span class="input-group-text" style="background: var(--gradient-primary); border: none;">
                                    <i class="fas fa-search text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-select">
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
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Products Section -->
            <div class="col-md-9">
                <div class="products-section">
                    <h2 class="section-title">
                        <?php 
                        if ($searchQuery && $selectedCategory) {
                            echo 'Search results for "' . htmlspecialchars($searchQuery) . '" in ' . htmlspecialchars($selectedCategory);
                        } elseif ($searchQuery) {
                            echo 'Search results for "' . htmlspecialchars($searchQuery) . '"';
                        } elseif ($selectedCategory) {
                            echo htmlspecialchars($selectedCategory) . ' Products';
                        } else {
                            echo 'All Products';
                        }
                        ?>
                    </h2>
                    
                    <div class="product-grid">
                        <?php
                        $products = getAllProducts('name', $selectedCategory, $searchQuery);
                        if ($products):
                            foreach ($products as $product):
                        ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['product_picture'])): ?>
                                        <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No image available">
                                    <?php endif; ?>
                                </div>
                                <div class="product-body">
                                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>                                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <div class="product-price">
                                        <?php
                                            $tax = $product['price'] * ($product['tax_rate']/100);
                                            $total_price = $product['price'] + $tax;
                                            echo "₱" . number_format($total_price, 2);
                                        ?>
                                    </div>
                                    <div class="product-stock">
                                        Stock: 
                                        <?php if ($product['stock_quantity'] <= 5): ?>
                                            <span class="text-danger"><?php echo $product['stock_quantity']; ?> units</span>
                                        <?php elseif ($product['stock_quantity'] <= 10): ?>
                                            <span class="text-warning"><?php echo $product['stock_quantity']; ?> units</span>
                                        <?php else: ?>
                                            <span class="text-success"><?php echo $product['stock_quantity']; ?> units</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <input type="number" class="form-control quantity-input" value="1" min="1" 
                                        max="<?php echo $product['stock_quantity']; ?>" 
                                        id="quantity_<?php echo $product['id']; ?>">
                                        
                                    <div class="button-group">
                                        <form method="POST" action="" style="flex: 1; width: 100%;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1" 
                                                id="quantity_hidden_<?php echo $product['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        </form>
                                        <form method="POST" action="" style="flex: 1; width: 100%;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1" 
                                                id="quantity_checkout_<?php echo $product['id']; ?>">
                                            <button type="submit" name="checkout" class="btn btn-outline">
                                                <i class="fas fa-bolt"></i> Buy Now
                                            </button>
                                        </form>
                                    </div>
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
                            <div class="col-12 empty-state">
                                <i class="fas fa-box-open"></i>
                                <h3>No products available</h3>
                                <p>There are no products available in this category.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling for filter changes
        document.querySelector('.filter-form select').addEventListener('change', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>