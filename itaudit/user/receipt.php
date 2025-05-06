<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isset($_SESSION['last_transaction'])) {
    header('Location: dashboard.php');
    exit();
}

$transaction = $_SESSION['last_transaction'];
unset($_SESSION['last_transaction']); // Clear the transaction data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Computer Parts Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .receipt {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: white;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .receipt-items {
            margin: 2rem 0;
        }
        .receipt-total {
            border-top: 2px solid #ddd;
            margin-top: 1rem;
            padding-top: 1rem;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .receipt {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h1>Computer Parts Shop</h1>
            <p>Official Receipt</p>
            <p>Date: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>Transaction ID: <?php echo rand(1000000, 9999999); ?></p>
        </div>

        <div class="receipt-items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal = 0;
                    $total_tax = 0;
                    foreach ($transaction['products'] as $item):
                        if (!isset($item['id'])) continue; // Skip non-product entries
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $item_tax = $item_subtotal * ($item['tax_rate'] / 100);
                        $subtotal += $item_subtotal;
                        $total_tax += $item_tax;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td>₱<?php echo number_format($item_subtotal, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-total">
            <p><strong>Subtotal:</strong> ₱<?php echo number_format($subtotal, 2); ?></p>
            <p><strong>Tax (12%):</strong> ₱<?php echo number_format($total_tax, 2); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($transaction['total'], 2); ?></p>
            <p><strong>Tender Amount:</strong> ₱<?php echo number_format($transaction['tender'], 2); ?></p>
            <p><strong>Change:</strong> ₱<?php echo number_format($transaction['change'], 2); ?></p>
        </div>

        <div class="receipt-footer">
            <p>Thank you for shopping with us!</p>
            <p>This serves as your official receipt</p>
            <p>Keep this receipt for warranty purposes</p>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 2rem;">
            <button onclick="window.print()">Print Receipt</button>
            <a href="dashboard.php" class="button">Back to Shop</a>
        </div>
    </div>
</body>
</html>