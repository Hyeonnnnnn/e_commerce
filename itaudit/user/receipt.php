<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.85rem;
            line-height: 1.5;
            color: var(--dark-gray);
            background-color: #f5f7fa;
            background-image: linear-gradient(rgba(245, 247, 250, 0.95), rgba(245, 247, 250, 0.95)), 
                              url('https://images.unsplash.com/photo-1587202372616-b43abea06c2a?ixlib=rb-4.0.3');
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
            margin: 0;
        }
        
        .receipt-container {
            max-width: 600px;
            width: 100%;
        }
        
        .receipt {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .receipt::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .receipt-header h2 {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
        }
        
        .receipt-header p {
            color: #718096;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        
        .receipt-logo {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            color: var(--primary);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .receipt-items {
            margin: 1.25rem 0;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.8rem;
        }
        
        .table thead th {
            background-color: #f8fafc;
            font-weight: 600;
            padding: 0.6rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--primary-dark);
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table tbody td {
            padding: 0.5rem 0.6rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .receipt-total {
            border-top: 2px solid #e2e8f0;
            margin-top: 1rem;
            padding-top: 1rem;
            font-size: 0.85rem;
        }
        
        .receipt-total p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.35rem;
        }
        
        .receipt-total p:last-child {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 1rem;
            padding-top: 0.35rem;
            border-top: 1px dashed #e2e8f0;
            margin-top: 0.35rem;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.75rem;
        }
        
        .btn-row {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1.25rem;
            gap: 1.5rem; /* Add space between buttons */
            padding: 0 0.25rem;
        }
        
        .btn-primary, .btn-secondary {
            border: none;
            padding: 0 1rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.85rem;
            text-align: center;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            height: 38px; /* Set fixed height for both buttons */
            width: 180px; /* Set fixed width for both buttons */
            margin: 0; /* Remove any default margins */
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .btn-primary:hover, .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .detail-row {
            font-size: 0.8rem;
        }
        
        @media print {
            .btn-row {
                display: none !important;
            }
            
            body {
                padding: 0;
                margin: 0;
                background: white;
                font-size: 10pt;
            }
            
            .receipt {
                box-shadow: none;
                border: none;
            }
            
            .receipt::before {
                display: none;
            }
            
            .table {
                font-size: 9pt;
            }
            
            .receipt-footer {
                font-size: 8pt;
            }
        }
        
        @media (max-width: 576px) {
            .receipt {
                padding: 1rem;
            }
            
            .receipt-container {
                padding: 0.5rem;
            }
            
            .table {
                font-size: 0.7rem;
            }
            
            .table thead th {
                padding: 0.4rem;
                font-size: 0.65rem;
            }
            
            .table tbody td {
                padding: 0.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt">
            <div class="receipt-header">
                <div class="receipt-logo">
                    <i class="fas fa-microchip"></i>
                </div>
                <h2>Computer Parts Shop</h2>
                <p class="mb-3">Official Receipt</p>
                <div class="d-flex justify-content-between mb-1 detail-row">
                    <span><strong>Date:</strong></span>
                    <span><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
                <div class="d-flex justify-content-between detail-row">
                    <span><strong>Transaction ID:</strong></span>
                    <span><?php echo $transaction['transaction_id'] ?? rand(1000000, 9999999); ?></span>
                </div>
            </div>

            <div class="receipt-items">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
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
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">₱<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-end">₱<?php echo number_format($item_subtotal, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="receipt-total">
                <p>
                    <span>Subtotal:</span>
                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                </p>
                <p>
                    <span>Tax (12%):</span>
                    <span>₱<?php echo number_format($total_tax, 2); ?></span>
                </p>
                <p>
                    <span>Total Amount:</span>
                    <span>₱<?php echo number_format($transaction['total'], 2); ?></span>
                </p>
                <p>
                    <span>Tender Amount:</span>
                    <span>₱<?php echo number_format($transaction['tender'], 2); ?></span>
                </p>
                <p>
                    <span>Change:</span>
                    <span>₱<?php echo number_format($transaction['change'], 2); ?></span>
                </p>
            </div>

            <div class="receipt-footer">
                <p class="mb-1 fw-bold">Thank you for shopping with us!</p>
                <p class="mb-1">This serves as your official receipt</p>
                <p class="mb-0">Keep this receipt for warranty purposes</p>
            </div>
        </div>
        
        <div class="btn-row">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Shop
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>