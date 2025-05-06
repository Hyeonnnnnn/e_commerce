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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard">
    <nav>
        <div class="nav-brand">Computer Parts Shop</div>
        <div class="nav-items">
            <a href="dashboard.php">Back to Products</a>
            <a href="../includes/logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <section class="checkout">
            <h2>Checkout</h2>
            <div class="cart-form">
                <table>
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
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['tax_rate']; ?>%</td>
                            <td>₱<?php echo number_format($subtotal, 2); ?></td>
                            <td>₱<?php echo number_format($tax, 2); ?></td>
                            <td>₱<?php echo number_format($total, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                            <td><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <form method="POST" action="" class="checkout-form">
                    <div class="form-group">
                        <label for="tender_amount">Tender Amount:</label>
                        <input type="number" id="tender_amount" name="tender_amount" 
                               step="0.01" min="<?php echo $total; ?>" required>
                    </div>
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <button type="submit" name="process_payment">Process Payment</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>