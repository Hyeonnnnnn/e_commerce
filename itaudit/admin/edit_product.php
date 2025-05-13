<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$product = null;

if (isset($_GET['id'])) {
    $product = getProduct($_GET['id']);
    if (!$product) {
        header('Location: dashboard.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
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

    if (updateProduct($id, $name, $category, $description, $price, $stock, $tax_rate, $product_picture)) {
        header('Location: dashboard.php?action=inventory&updated=1');
        exit();
    } else {
        $message = 'Failed to update product';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Computer Parts Shop</title>
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
        
        /* Main Content */
        .edit-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Card styling */
        .edit-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .edit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        .card-header {
            background: var(--gradient-primary);
            color: white;
            padding: 1.2rem 1.5rem;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header h3 {
            font-weight: 600;
            margin: 0;
            font-size: 1.25rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Alert message */
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
        
        .alert-danger {
            background-color: #fff5f5;
            border-color: #f56565;
            color: #c53030;
        }
        
        .alert-custom i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        /* Form elements */
        .form-label {
            font-weight: 500;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, 
        .form-select {
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            border: 1px solid #cbd5e0;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
        }
        
        .form-control:focus, 
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
            outline: none;
        }
        
        textarea.form-control {
            min-height: 100px;
        }
        
        .current-image {
            margin-bottom: 1rem;
        }
        
        .current-image img {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            height: auto;
        }
        
        .help-text {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.25rem;
        }
        
        /* Button styling */
        .btn {
            border-radius: 30px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            box-shadow: 0 8px 15px rgba(44, 82, 130, 0.3);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 8px 15px rgba(108, 117, 125, 0.3);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-microchip fa-lg"></i>
                Computer Parts Shop - Admin
            </a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?action=inventory">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
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

    <div class="edit-container">
        <?php if ($message): ?>
            <div class="alert-custom alert-danger fade show" id="message-notification">
                <i class="fas fa-exclamation-circle"></i>
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

        <div class="edit-card">
            <div class="card-header">
                <h3><i class="fas fa-edit me-2"></i>Edit Product</h3>
                <a href="dashboard.php?action=inventory" class="text-white text-decoration-none">
                    <span class="btn-close-custom">✕</span>
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <?php
                                $categories = ['CPU', 'GPU', 'RAM', 'Storage', 'Motherboard', 'PSU', 'Case', 'Peripheral'];
                                foreach ($categories as $cat):
                                    $selected = ($cat === $product['category']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $cat; ?>" <?php echo $selected; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price (₱)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" value="<?php echo htmlspecialchars($product['tax_rate']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_image" class="form-label">Product Image</label>
                        <?php if (!empty($product['product_picture'])): ?>
                        <div class="current-image mb-3">
                            <p class="mb-2">Current image:</p>
                            <img src="../images/<?php echo htmlspecialchars($product['product_picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid" style="max-height: 200px;">
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                        <div class="form-text">Upload a new image or leave empty to keep the current one</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                        <a href="dashboard.php?action=inventory" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>