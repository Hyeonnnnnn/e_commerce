<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$cart = $_SESSION['cart'] ?? [];

if (isset($_POST['remove_item'])) {
    $productId = $_POST['product_id'] ?? 0;
    if (isset($cart[$productId])) {
        unset($cart[$productId]);
        $_SESSION['cart'] = $cart;
        $message = 'Item removed from cart';
    }
}

if (isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = (int)$_POST['quantity'] ?? 1;
    if (isset($cart[$productId])) {
        $cart[$productId] = $quantity;
        $_SESSION['cart'] = $cart;
        $message = 'Cart updated';
    }
}

if (isset($_POST['checkout'])) {
    $tender_amount = floatval($_POST['tender_amount'] ?? 0);
    $products = [];
    $total = 0;
    
    foreach ($cart as $productId => $quantity) {
        $product = getProduct($productId);
        if ($product && $product['stock_quantity'] >= $quantity) {
            $products[] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'tax_rate' => $product['tax_rate']
            ];
            $total += $product['price'] * $quantity * (1 + $product['tax_rate']/100);
        }
    }
    
    if ($tender_amount >= $total) {
        if (createTransaction($_SESSION['user_id'], ['items' => $products, 'tender_amount' => $tender_amount])) {
            $_SESSION['last_transaction'] = [
                'products' => $products,
                'total' => $total,
                'tender' => $tender_amount,
                'change' => $tender_amount - $total
            ];
            unset($_SESSION['cart']);
            header('Location: receipt.php');
            exit();
        }
    } else {
        $message = 'Insufficient tender amount';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Computer Parts Shop</title>
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
        
        .alert-danger {
            background-color: #fff5f5;
            border-color: #f56565;
            color: #c53030;
        }
        
        /* Section styling */
        .cart-section {
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
        
        /* Card styling */
        .cart-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .cart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        /* Table styling */
        .table-cart {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        
        .table-cart th {
            background-color: rgba(66, 153, 225, 0.1);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 0.75rem 1rem;
            border: none;
        }
        
        .table-cart td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table-cart tr:last-child td {
            border-bottom: none;
        }
        
        .table-cart .text-end {
            text-align: right;
        }
        
        .table-cart .grand-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* Quantity control */
        .quantity-control {
            display: flex;
            align-items: center;
        }
        
        .quantity-input {
            width: 70px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .btn-update {
            border: none;
            background: var(--gradient-primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-remove {
            background-color: #f56565;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        }
        
        /* Form styling */
        .checkout-form {
            background-color: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
            border: 1px solid #e2e8f0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
            outline: none;
        }
        
        /* Button styling */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            color: var(--primary);
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-outline:hover {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
            text-decoration: none;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        /* Empty cart */
        .empty-cart {
            text-align: center;
            padding: 3rem 0;
        }
        
        .empty-cart i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
        }
        
        .empty-cart h3 {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }
        
        .empty-cart p {
            color: #718096;
            margin-bottom: 2rem;
        }
        
        /* Responsive styling */
        @media (max-width: 768px) {
            .table-cart {
                display: block;
                overflow-x: auto;
            }
            
            .quantity-control {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .quantity-input {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
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
                        <a class="nav-link active" href="cart.php">
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
            <div class="alert-custom <?php echo strpos($message, 'removed') !== false || strpos($message, 'Insufficient') !== false ? 'alert-danger' : 'alert-success'; ?> fade show" id="message-notification">
                <i class="<?php echo strpos($message, 'removed') !== false || strpos($message, 'Insufficient') !== false ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?>"></i>
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

        <div class="cart-section">
            <h2 class="section-title">
                <i class="fas fa-shopping-cart me-2"></i> Shopping Cart
            </h2>
            
            <?php if (!empty($cart)): ?>
                <div class="cart-card">
                    <div class="table-responsive">
                        <table class="table table-cart">                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($cart as $productId => $quantity):
                                    $product = getProduct($productId);
                                    if ($product):
                                        $subtotal = $product['price'] * $quantity;
                                        $tax = $subtotal * ($product['tax_rate'] / 100);
                                        $itemTotal = $subtotal + $tax;
                                        $total += $itemTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <?php if (!empty($product['product_picture'])): ?>
                                                <div class="mt-2">
                                                    <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                </div>                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" action="" class="quantity-control">
                                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                <div class="input-group">
                                                    <input type="number" class="form-control form-control-sm quantity-input" 
                                                           name="quantity" value="<?php echo $quantity; ?>" 
                                                           min="1" max="<?php echo $product['stock_quantity']; ?>">
                                                    <button type="submit" name="update_quantity" class="btn btn-update">
                                                        <i class="fas fa-sync-alt"></i> Update
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>₱<?php echo number_format($itemTotal, 2); ?></td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                <button type="submit" name="remove_item" class="btn btn-remove">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>                                <tr>
                                    <td colspan="2" class="text-end">
                                        <strong>Grand Total:</strong>
                                    </td>
                                    <td class="grand-total">₱<?php echo number_format($total, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="checkout-form">
                                <h5 class="mb-4"><i class="fas fa-credit-card me-2"></i> Payment Details</h5>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="tender_amount" class="form-label">Tender Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" id="tender_amount" name="tender_amount" class="form-control"
                                                   step="0.01" min="<?php echo $total; ?>" 
                                                   placeholder="Enter amount" required>
                                        </div>
                                        <div class="form-text">
                                            <small>Enter an amount equal to or greater than the total: ₱<?php echo number_format($total, 2); ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="dashboard.php" class="btn btn-outline-secondary me-md-2" style="font-size: 0.9rem; padding: 0.5rem 1rem; height: 38px; display: flex; align-items: center;">
                                            <i class="fas fa-arrow-left" style="font-size: 0.9rem; margin-right: 8px; position: relative; top: 1px;"></i> Continue Shopping
                                        </a>
                                        <button type="submit" name="checkout" class="btn btn-primary">
                                            <i class="fas fa-check-circle"></i> Proceed to Checkout
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-card empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center mx-auto" style="width: fit-content; font-size: 0.9rem; padding: 0.4rem 1.5rem; border-color: #dee2e6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>