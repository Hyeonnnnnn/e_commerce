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
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <section class="cart">
            <h2>Shopping Cart</h2>
            <?php if (!empty($cart)): ?>
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
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline">
                                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" 
                                                   min="1" max="<?php echo $product['stock_quantity']; ?>" 
                                                   style="width: 60px">
                                            <button type="submit" name="update_quantity" class="button">Update</button>
                                        </form>
                                    </td>
                                    <td><?php echo $product['tax_rate']; ?>%</td>
                                    <td>₱<?php echo number_format($subtotal, 2); ?></td>
                                    <td>₱<?php echo number_format($tax, 2); ?></td>
                                    <td>₱<?php echo number_format($itemTotal, 2); ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline">
                                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                            <button type="submit" name="remove_item" class="button" 
                                                    style="background-color: #dc3545;">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                            <tr>
                                <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                                <td colspan="2"><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <form method="POST" action="" class="checkout-form">
                        <div class="form-group">
                            <label for="tender_amount">Tender Amount:</label>
                            <input type="number" id="tender_amount" name="tender_amount" 
                                   step="0.01" min="<?php echo $total; ?>" required>
                        </div>
                        <button type="submit" name="checkout" class="checkout">Proceed to Checkout</button>
                    </form>
                </div>
            <?php else: ?>
                <p>Your cart is empty. <a href="dashboard.php">Continue shopping</a></p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>