<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isset($_SESSION['checkout_item'])) {
    header('Location: dashboard.php');
    exit();
}

$item = $_SESSION['checkout_item'];
$subtotal = $item['price'] * $item['quantity'];
$tax = $subtotal * ($item['tax_rate'] / 100);
$total = $subtotal + $tax;
$cart = $_SESSION['cart'] ?? [];

if (isset($_POST['process_payment'])) {
    $tender_amount = floatval($_POST['tender_amount'] ?? 0);
    
    if ($tender_amount >= $total) {
        $products = [
            'items' => [
                [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'tax_rate' => $item['tax_rate']
                ]
            ],
            'tender_amount' => $tender_amount
        ];
        
        if (createTransaction($_SESSION['user_id'], $products)) {
            $_SESSION['last_transaction'] = [
                'products' => $products['items'],
                'total' => $total,
                'tender' => $tender_amount,
                'change' => $tender_amount - $total
            ];
            unset($_SESSION['checkout_item']);
            header('Location: receipt.php');
            exit();
        }
    }
    $error = 'Insufficient tender amount';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Computer Parts Shop</title>
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
            max-width: 1200px;
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
        .checkout-section {
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
        .checkout-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .checkout-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        /* Table styling */
        .table-checkout {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        
        .table-checkout th {
            background-color: rgba(66, 153, 225, 0.1);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 0.75rem 1rem;
            border: none;
        }
        
        .table-checkout td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table-checkout tr:last-child td {
            border-bottom: none;
        }
        
        .table-checkout .text-right {
            text-align: right;
        }
        
        .table-checkout .grand-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* Form styling */
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
            font-family: 'Montserrat', sans-serif;
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
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        /* Error message */
        .error-message {
            color: #c53030;
            background-color: #fff5f5;
            border-left: 4px solid #f56565;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        /* Responsive styling */
        @media (max-width: 768px) {
            .table-checkout {
                display: block;
                overflow-x: auto;
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
        <div class="checkout-section">
            <h2 class="section-title">
                <i class="fas fa-cash-register me-2"></i> Checkout
            </h2>
            
            <div class="checkout-card">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-4"><i class="fas fa-shopping-bag me-2"></i> Order Summary</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-checkout">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Tax Rate</th>
                                        <th>Subtotal</th>
                                        <th>Tax Amount</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        </td>
                                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo $item['tax_rate']; ?>%</td>
                                        <td>₱<?php echo number_format($subtotal, 2); ?></td>
                                        <td>₱<?php echo number_format($tax, 2); ?></td>
                                        <td>₱<?php echo number_format($total, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">
                                            <strong>Grand Total:</strong>
                                        </td>
                                        <td class="grand-total">₱<?php echo number_format($total, 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-4"><i class="fas fa-credit-card me-2"></i> Payment Details</h5>
                        
                        <form method="POST" action="" class="payment-form">
                            <div class="mb-4">
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
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                                <button type="submit" name="process_payment" class="btn btn-primary">
                                    <i class="fas fa-check-circle"></i> Process Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>