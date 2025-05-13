<?php
require_once '../includes/functions.php';
require_once '../config/database.php'; // Add database connection at the beginning of the file

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
    
    // Handle edit product form submission
    else if (isset($_POST['edit_product'])) {
        $id = $_POST['edit_id'] ?? '';
        $name = $_POST['edit_name'] ?? '';
        $category = $_POST['edit_category'] ?? '';
        $description = $_POST['edit_description'] ?? '';
        $price = $_POST['edit_price'] ?? 0;
        $stock = $_POST['edit_stock'] ?? 0;
        $tax_rate = $_POST['edit_tax_rate'] ?? 12.00;
        
        // Handle file upload
        $product_picture = null;
        if (isset($_FILES['edit_product_image']) && $_FILES['edit_product_image']['error'] === UPLOAD_ERR_OK) {
            $temp_name = $_FILES['edit_product_image']['tmp_name'];
            $filename = $_FILES['edit_product_image']['name'];
            
            // Move the uploaded file to the images directory
            if (move_uploaded_file($temp_name, "../images/$filename")) {
                $product_picture = $filename;
            }
        }

        if (updateProduct($id, $name, $category, $description, $price, $stock, $tax_rate, $product_picture)) {
            $message = 'Product updated successfully';
        } else {
            $message = 'Failed to update product';
        }
    }
    
    // Handle delete product
    else if (isset($_POST['delete_product'])) {
        $id = $_POST['delete_product'] ?? '';
        
        if (deleteProduct($id)) {
            $message = 'Product deleted successfully';
        } else {
            $message = 'Failed to delete product';
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
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
        
        body.dashboard {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: var(--dark-gray);
            background-color: #f5f7fa;
            background-image: linear-gradient(rgba(245, 247, 250, 0.95), rgba(245, 247, 250, 0.95)), 
                              url('https://images.unsplash.com/photo-1587202372616-b43abea06c2a?ixlib=rb-4.0.3');
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 0;
            margin: 0;
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
        
        .navbar-nav .nav-link.active,
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-nav .nav-link i {
            margin-right: 5px;
        }
        
        /* Main Content Area */
        main.dashboard-content {
            flex: 1;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-right: 0.5rem;
        }
        
        /* Cards & Content */
        .dash-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .dash-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .dash-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }
        
        .dash-card-header h3 {
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0;
            font-size: 1.25rem;
        }
        
        .add-product-btn {
            position: relative;
        }
        
        /* Table styling */
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
        }
        
        .table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background-color: #f8fafc;
            font-weight: 600;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary-dark);
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table tbody tr:hover {
            background-color: rgba(66, 153, 225, 0.05);
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        
        /* Actions buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 0.25rem;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: #3182ce;
            color: white;
        }
        
        .btn-delete {
            background-color: #e53e3e;
            color: white;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Badges */
        .badge {
            padding: 0.35em 0.65em;
            border-radius: 30px;
            font-weight: 500;
        }
        
        /* Modal Styling */
        .modal {
            z-index: 1050;
        }
        
        .modal-dialog {
            z-index: 1052;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1052;
            overflow: hidden;
        }
        
        .modal-backdrop {
            z-index: 1040;
        }
        
        .modal-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 0;
            padding: 1.2rem 1.5rem;
            border-bottom: none;
        }
        
        .modal-header .btn-close-custom {
            color: white;
            background: none;
            font-size: 1.5rem;
            padding: 0;
            border: none;
            cursor: pointer;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.2s;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-header .btn-close-custom:hover {
            opacity: 1;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }
        
        /* Modal footer buttons consistent styling */
        .modal-footer .btn {
            border-radius: 30px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .modal-footer .btn:hover {
            transform: translateY(-2px);
        }
        
        .modal-footer .btn-secondary {
            background-color: #6c757d;
            border: none;
            box-shadow: none;
        }
        
        .modal-footer .btn-secondary:hover {
            background-color: #5a6268;
            box-shadow: 0 8px 15px rgba(108, 117, 125, 0.3);
        }
        
        /* Form elements */
        .form-label {
            font-weight: 500;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            border: 1px solid #cbd5e0;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }
        
        /* Report area styling */
        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .summary-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #718096;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .summary-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            background: linear-gradient(135deg, #2c5282 0%, #4299e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Filter Form */
        .filter-form {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .filter-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
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
        
        /* Responsive fixes */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            main.dashboard-content {
                padding: 1rem;
            }
            
            .dash-card {
                padding: 1rem;
            }
            
            .report-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dashboard">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-microchip fa-lg"></i>
                Computer Parts Shop - Admin
            </a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($action === '' || $action === 'inventory') ? 'active' : ''; ?>" href="?action=inventory">
                            <i class="fas fa-box"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($action === 'reports') ? 'active' : ''; ?>" href="?action=reports">
                            <i class="fas fa-chart-line"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($action === 'logs') ? 'active' : ''; ?>" href="?action=logs">
                            <i class="fas fa-history"></i> Logs
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

    <main class="dashboard-content container">
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

        <?php if ($action === 'inventory' || $action === ''): ?>
            <h1 class="page-title">
                <i class="fas fa-box me-2"></i>Inventory Management
            </h1>
            
            <div class="row">
                <div class="col-12">
                    <div class="dash-card">
                        <div class="dash-card-header justify-content-center position-relative">
                            <h3>Inventory</h3>
                            <div style="position: absolute; right: 0;">
                                <button class="btn btn-primary btn-sm py-1" style="width: 80px;" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                    <i class="fas fa-plus me-1"></i>Add
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <?php
                            $products = getAllProducts();
                            if ($products): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($product['product_picture'])): ?>
                                                            <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" 
                                                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                                class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                        <?php else: ?>
                                                            <div class="me-3" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-image text-secondary"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($product['category']); ?></span></td>
                                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <?php if ($product['stock_quantity'] <= 5): ?>
                                                        <span class="badge bg-danger"><?php echo htmlspecialchars($product['stock_quantity']); ?></span>
                                                    <?php elseif ($product['stock_quantity'] <= 10): ?>
                                                        <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($product['stock_quantity']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?php echo htmlspecialchars($product['stock_quantity']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($product['tax_rate']); ?>%</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <button type="button" class="btn btn-action btn-edit me-2" title="Edit Product" 
                                                               onclick="openEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" action="" style="margin: 0; display: inline-block; line-height: 0;">
                                                            <input type="hidden" name="delete_product" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete Product">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No products found</h5>
                                    <p>Start by adding your first product to the inventory</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                            <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">✕</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" enctype="multipart/form-data" id="addProductForm">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
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
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="price" class="form-label">Price (₱)</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="stock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="stock" name="stock" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" value="12.00" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                                    <div class="form-text">Upload an image of the product (optional)</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addProductForm" name="add_product" class="btn btn-primary">Add Product</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                            <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">✕</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" enctype="multipart/form-data" id="editProductForm">
                                <input type="hidden" id="edit_id" name="edit_id">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_category" class="form-label">Category</label>
                                        <select class="form-select" id="edit_category" name="edit_category" required>
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
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_description" name="edit_description" rows="3" required></textarea>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="edit_price" class="form-label">Price (₱)</label>
                                        <input type="number" class="form-control" id="edit_price" name="edit_price" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_stock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="edit_stock" name="edit_stock" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_tax_rate" class="form-label">Tax Rate (%)</label>
                                        <input type="number" class="form-control" id="edit_tax_rate" name="edit_tax_rate" step="0.01" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_product_image" class="form-label">Product Image</label>
                                    <div id="current_image_container" class="mb-3"></div>
                                    <input type="file" class="form-control" id="edit_product_image" name="edit_product_image" accept="image/*">
                                    <div class="form-text">Upload a new image or leave empty to keep the current one</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="editProductForm" name="edit_product" class="btn btn-primary">Update Product</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($action === 'reports'): ?>
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2"></i>Sales Reports
            </h1>
            
            <div class="dash-card filter-form mb-4">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="action" value="reports">
                    <input type="hidden" name="report_type" value="sales">
                    
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center" style="white-space: nowrap; min-width: 180px; margin: 0 auto;">
                            <i class="fas fa-search me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <?php if (!empty($reports)): ?>
                <div class="report-summary">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="summary-title">TOTAL REVENUE</div>
                        <div class="summary-value">₱<?php echo number_format($reports['summary']['total_revenue'] ?? 0, 2); ?></div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="summary-title">TOTAL ORDERS</div>
                        <div class="summary-value"><?php echo number_format($reports['summary']['total_orders'] ?? 0); ?></div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="summary-title">PRODUCTS SOLD</div>
                        <div class="summary-value"><?php echo number_format($reports['summary']['total_products_sold'] ?? 0); ?></div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-header d-flex justify-content-between align-items-center">
                        <h3>Detailed Sales</h3>
                        <button class="btn btn-primary d-flex align-items-center justify-content-center" style="white-space: nowrap; width: 160px; border-radius: 30px;" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                <?php if (count($reports['transactions']) > 0): ?>
                                    <?php foreach ($reports['transactions'] as $row): ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['product_names']); ?></td>
                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['products_sold']); ?> items</span></td>
                                            <td><strong>₱<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No transactions found for the selected period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <strong>Total Records: <?php echo count($reports['transactions']); ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="dash-card text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Report Data Available</h5>
                    <p>Select a date range and generate a report to view sales data</p>
                </div>
            <?php endif; ?>
            
        <?php elseif ($action === 'logs'): ?>
            <h1 class="page-title">
                <i class="fas fa-history me-2"></i>Audit Logs
            </h1>
            
            <div class="dash-card filter-form mb-4">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="action" value="logs">
                    
                    <div class="col-md-3">
                        <label for="log_start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="log_start_date" name="log_start_date" 
                               value="<?php echo isset($_GET['log_start_date']) ? htmlspecialchars($_GET['log_start_date']) : date('Y-m-d', strtotime('-7 days')); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="log_end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="log_end_date" name="log_end_date" 
                               value="<?php echo isset($_GET['log_end_date']) ? htmlspecialchars($_GET['log_end_date']) : date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="action_type" class="form-label">Action Type</label>
                        <select class="form-select" id="action_type" name="action_type">
                            <option value="">All Actions</option>
                            <option value="user_login" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'user_login') ? 'selected' : ''; ?>>Login</option>
                            <option value="user_logout" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'user_logout') ? 'selected' : ''; ?>>Logout</option>
                            <option value="user_register" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'user_register') ? 'selected' : ''; ?>>Register</option>
                            <option value="product_added" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'product_added') ? 'selected' : ''; ?>>Product Add</option>
                            <option value="product_updated" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'product_updated') ? 'selected' : ''; ?>>Product Edit</option>
                            <option value="product_deleted" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'product_deleted') ? 'selected' : ''; ?>>Product Delete</option>
                            <option value="transaction_created" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'transaction_created') ? 'selected' : ''; ?>>Purchase</option>
                            <option value="stock_updated" <?php echo (isset($_GET['action_type']) && $_GET['action_type'] === 'stock_updated') ? 'selected' : ''; ?>>Stock Update</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filter Logs
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="dash-card">
                <div class="dash-card-header d-flex justify-content-between align-items-center">
                    <h3>System Activity Logs</h3>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Logs
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <?php
                    // Get audit logs with filtering
                    $startDate = isset($_GET['log_start_date']) ? $_GET['log_start_date'] : date('Y-m-d', strtotime('-7 days'));
                    $endDate = isset($_GET['log_end_date']) ? $_GET['log_end_date'] : date('Y-m-d');
                    $actionType = isset($_GET['action_type']) && $_GET['action_type'] !== '' ? $_GET['action_type'] : '';
                    
                    // Debug output
                    echo "<!-- Debug: Action Type = '" . htmlspecialchars($actionType) . "' -->";
                    
                    // Convert end date to include the entire day
                    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
                    
                    // Build the query with filters
                    $query = "SELECT al.*, u.username 
                              FROM audit_log al
                              LEFT JOIN users u ON al.user_id = u.id
                              WHERE al.created_at BETWEEN :startDate AND :endDate";
                    
                    $params = [':startDate' => $startDate, ':endDate' => $endDate];
                    
                    if (!empty($actionType)) {
                        $query .= " AND al.action_type = :actionType";
                        $params[':actionType'] = $actionType;
                    }
                    
                    $query .= " ORDER BY al.created_at DESC LIMIT 500";
                    
                    // Debug output
                    echo "<!-- Debug: Query = " . htmlspecialchars($query) . " -->";
                    
                    try {
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($params);
                        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "<!-- Debug: DB Error = " . htmlspecialchars($e->getMessage()) . " -->";
                        $logs = [];
                    }
                    ?>
                    
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($logs) > 0): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td width="20%"><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></td>
                                        <td width="15%">
                                            <?php if (!empty($log['username'])): ?>
                                                <?php echo htmlspecialchars($log['username']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Unknown User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td width="15%">
                                            <?php
                                            $actionClass = 'bg-info';
                                            
                                            switch($log['action_type']) {
                                                case 'user_login':
                                                    $actionClass = 'bg-success';
                                                    break;
                                                case 'user_logout':
                                                    $actionClass = 'bg-secondary';
                                                    break;
                                                case 'user_register':
                                                    $actionClass = 'bg-primary';
                                                    break;
                                                case 'product_added':
                                                case 'product_updated':
                                                case 'product_deleted':
                                                    $actionClass = 'bg-warning text-dark';
                                                    break;
                                                case 'transaction_created':
                                                    $actionClass = 'bg-success';
                                                    break;
                                                case 'stock_updated':
                                                    $actionClass = 'bg-info';
                                                    break;
                                                default:
                                                    $actionClass = 'bg-info';
                                            }
                                            ?>
                                            <span class="badge <?php echo $actionClass; ?>">
                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $log['action_type']))); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No audit logs found for the selected filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <?php if (count($logs) >= 500): ?>
                        <div class="text-center mt-3">
                            <span class="badge bg-warning text-dark">Showing the 500 most recent logs. Please use filters to narrow your search.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        function openEditModal(product) {
            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock_quantity;
            document.getElementById('edit_tax_rate').value = product.tax_rate;

            const currentImageContainer = document.getElementById('current_image_container');
            currentImageContainer.innerHTML = '';
            if (product.product_picture) {
                const imgElement = document.createElement('img');
                imgElement.src = `../images/${product.product_picture}`;
                imgElement.alt = product.name;
                imgElement.style.width = '100px';
                imgElement.style.height = '100px';
                imgElement.style.objectFit = 'cover';
                imgElement.style.borderRadius = '8px';
                imgElement.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.1)';
                imgElement.style.marginBottom = '10px';
                
                const imageLabel = document.createElement('p');
                imageLabel.textContent = 'Current image:';
                imageLabel.style.marginBottom = '5px';
                imageLabel.style.fontWeight = '500';
                
                currentImageContainer.appendChild(imageLabel);
                currentImageContainer.appendChild(imgElement);
            }

            const editProductModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            editProductModal.show();
        }
    </script>
</body>
</html>