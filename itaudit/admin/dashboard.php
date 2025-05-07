<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit();
}

$action = $_GET['action'] ?? '';
$message = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
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

        if (addProduct($name, $category, $description, $price, $stock, $tax_rate, $product_picture)) {
            $message = 'Product added successfully';
        } else {
            $message = 'Failed to add product';
        }
    }
}

// Get reports data if requested
$reports = [];
if (isset($_GET['report_type'])) {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    if ($_GET['report_type'] === 'sales') {
        $reports = generateSalesReport($startDate, $endDate);
    } else if ($_GET['report_type'] === 'tax') {
        $reports = generateTaxReport($startDate, $endDate);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Computer Parts Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard">
    <nav>
        <div class="nav-brand">Computer Parts Shop - Admin</div>
        <div class="nav-items">
            <a href="?action=inventory">Inventory</a>
            <a href="?action=reports">Reports</a>
            <a href="../includes/logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($action === 'inventory' || $action === ''): ?>
            <section class="inventory">
                <h2>Inventory Management</h2>
                <div class="inventory-header">
                    <button class="add-product-btn" onclick="openModal()">+ Add New Product</button>
                </div>

                <!-- Add Product Modal -->
                <div id="addProductModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Add New Product</h3>
                            <span class="close" onclick="closeModal()">&times;</span>
                        </div>
                        <form method="POST" action="" class="add-product-form" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Product Name:</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category:</label>
                                <select id="category" name="category" required>
                                    <option value="CPU">CPU</option>
                                    <option value="GPU">GPU</option>
                                    <option value="RAM">RAM</option>
                                    <option value="Storage">Storage</option>
                                    <option value="Motherboard">Motherboard</option>
                                    <option value="PSU">Power Supply</option>
                                    <option value="Case">Case</option>
                                    <option value="Peripheral">Peripheral</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="stock">Stock Quantity:</label>
                                <input type="number" id="stock" name="stock" required>
                            </div>
                            <div class="form-group">
                                <label for="tax_rate">Tax Rate (%):</label>
                                <input type="number" id="tax_rate" name="tax_rate" step="0.01" value="12.00" required>
                            </div>
                            <div class="form-group">
                                <label for="product_image">Product Image:</label>
                                <input type="file" id="product_image" name="product_image" accept="image/*">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                                <button type="submit" name="add_product" class="submit-btn">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="product-list">
                    <h3>Current Inventory</h3>
                    <?php
                    $products = getAllProducts();
                    if ($products): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Tax Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td><?php echo htmlspecialchars($product['price']); ?></td>
                                        <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($product['tax_rate']); ?>%</td>
                                        <td>
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No products found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <script>
                function openModal() {
                    document.getElementById('addProductModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    document.getElementById('addProductModal').style.display = 'none';
                    document.body.style.overflow = 'auto';
                }

                // Close modal when clicking outside
                window.onclick = function(event) {
                    var modal = document.getElementById('addProductModal');
                    if (event.target == modal) {
                        closeModal();
                    }
                }

                // Close modal on escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });
            </script>
        <?php elseif ($action === 'reports'): ?>
            <section class="reports">
                <h2>Sales Report</h2>
                <form method="GET" action="" class="report-form">
                    <input type="hidden" name="action" value="reports">
                    <input type="hidden" name="report_type" value="sales">
                    <div class="form-group">
                        <label>Time Span:</label>
                        <div class="date-inputs">
                            <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>" required>
                            <span>to</span>
                            <input type="date" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); ?>" required>
                            <button type="submit">Apply Filter</button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($reports)): ?>
                    <div class="report-summary">
                        <div class="summary-card">
                            <div class="summary-title">TOTAL REVENUE</div>
                            <div class="summary-value">₱<?php echo number_format($reports['summary']['total_revenue'] ?? 0, 2); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-title">TOTAL ORDERS</div>
                            <div class="summary-value"><?php echo number_format($reports['summary']['total_orders'] ?? 0); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-title">PRODUCTS SOLD</div>
                            <div class="summary-value"><?php echo number_format($reports['summary']['total_products_sold'] ?? 0); ?></div>
                        </div>
                    </div>

                    <div class="detailed-sales">
                        <h3>Detailed Sales</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports['transactions'] as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['product_names']); ?></td>
                                        <td><?php echo htmlspecialchars($row['products_sold']); ?> items</td>
                                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center">Totals: <?php echo count($reports['transactions']); ?> records</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>