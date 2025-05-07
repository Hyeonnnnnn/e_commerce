<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// User Authentication Functions
function registerUser($username, $email, $password, $role = 'user') {
    global $pdo;
    // Store password without hashing
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        logAudit(null, 'user_registration', "New user registered: $username");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function loginUser($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            logAudit($user['id'], 'user_login', "User logged in: $username");
            return true;
        }
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Inventory Management Functions
function addProduct($name, $category, $description, $price, $stock, $tax_rate = 12.00, $product_picture = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, description, price, stock_quantity, tax_rate, product_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $description, $price, $stock, $tax_rate, $product_picture]);
        $productId = $pdo->lastInsertId();
        logAudit($_SESSION['user_id'], 'product_added', "New product added: $name (ID: $productId)");
        return $productId;
    } catch(PDOException $e) {
        return false;
    }
}

function updateStock($productId, $quantity) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $productId]);
        logAudit($_SESSION['user_id'], 'stock_updated', "Stock updated for product ID: $productId");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Product Management Functions
function getAllProducts($sortBy = 'name', $category = null) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM products";
        $params = [];
        
        if ($category) {
            $sql .= " WHERE category = ?";
            $params[] = $category;
        }
        
        switch ($sortBy) {
            case 'price_low':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY price DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY created_at DESC";
                break;
            default:
                $sql .= " ORDER BY name ASC";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return false;
    }
}

function getProduct($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return false;
    }
}

function updateProduct($id, $name, $category, $description, $price, $stock, $tax_rate, $product_picture = null) {
    global $pdo;
    
    try {
        // If a new image was provided, update the image as well
        if ($product_picture) {
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, category = ?, description = ?, price = ?, 
                    stock_quantity = ?, tax_rate = ?, product_picture = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $category, $description, $price, $stock, $tax_rate, $product_picture, $id]);
        } else {
            // Otherwise, don't change the image
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, category = ?, description = ?, price = ?, 
                    stock_quantity = ?, tax_rate = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $category, $description, $price, $stock, $tax_rate, $id]);
        }
        
        logAudit($_SESSION['user_id'], 'product_updated', "Updated product: $name");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function deleteProduct($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        logAudit($_SESSION['user_id'], 'product_deleted', "Deleted product ID: $id");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Helper Functions
function calculateTotal($products) {
    $total = 0;
    foreach ($products as $product) {
        if (!isset($product['id'])) continue;
        $total += $product['price'] * $product['quantity'] * (1 + $product['tax_rate']/100);
    }
    return $total;
}

function calculateTax($price, $quantity, $tax_rate) {
    return ($price * $quantity) * ($tax_rate / 100);
}

// Cart Functions
function addToCart($productId, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }
    
    $_SESSION['cart'][$productId] += $quantity;
}

function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

function clearCart() {
    unset($_SESSION['cart']);
}

function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = getProduct($productId);
            if ($product) {
                $total += $product['price'] * $quantity * (1 + $product['tax_rate']/100);
            }
        }
    }
    return $total;
}

// Transaction Functions
function createTransaction($userId, $data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $totalAmount = 0;
        $totalTax = 0;
        
        // Calculate totals
        foreach ($data['items'] as $product) {
            $totalAmount += $product['price'] * $product['quantity'];
            $taxAmount = ($product['price'] * $product['quantity']) * ($product['tax_rate'] / 100);
            $totalTax += $taxAmount;
        }
        
        // Create transaction record
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_amount, tax_amount, tender_amount, change_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId, 
            $totalAmount + $totalTax, 
            $totalTax, 
            $data['tender_amount'], 
            $data['tender_amount'] - ($totalAmount + $totalTax)
        ]);
        $transactionId = $pdo->lastInsertId();
        
        // Create transaction details
        foreach ($data['items'] as $product) {
            $subtotal = $product['price'] * $product['quantity'];
            $taxAmount = $subtotal * ($product['tax_rate'] / 100);
            
            $stmt = $pdo->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, unit_price, subtotal, tax_amount) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $transactionId, 
                $product['id'], 
                $product['quantity'], 
                $product['price'], 
                $subtotal, 
                $taxAmount
            ]);
            
            // Update stock
            updateStock($product['id'], -$product['quantity']);
        }
        
        $pdo->commit();
        logAudit($userId, 'transaction_created', "New transaction created: $transactionId");
        return $transactionId;
    } catch(PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// Audit Functions
function logAudit($userId, $actionType, $description) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action_type, description) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $actionType, $description]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Report Generation Functions
function generateSalesReport($startDate, $endDate) {
    global $pdo;
    
    try {
        // Get individual transactions with product names
        $stmt = $pdo->prepare("
            SELECT 
                t.id as transaction_id,
                t.transaction_date,
                u.username,
                t.total_amount,
                t.tax_amount,
                t.tender_amount,
                t.change_amount,
                GROUP_CONCAT(DISTINCT p.name) as product_names,
                COUNT(DISTINCT td.product_id) as products_sold
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            LEFT JOIN products p ON td.product_id = p.id
            WHERE DATE(t.transaction_date) BETWEEN ? AND ?
            GROUP BY t.id
            ORDER BY t.transaction_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $transactions = $stmt->fetchAll();

        // Get summary data
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT t.id) as total_orders,
                COUNT(DISTINCT td.product_id) as total_products_sold,
                SUM(t.total_amount) as total_revenue
            FROM transactions t
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            WHERE DATE(t.transaction_date) BETWEEN ? AND ?
        ");
        $stmt->execute([$startDate, $endDate]);
        $summary = $stmt->fetch();

        return [
            'transactions' => $transactions,
            'summary' => $summary
        ];
    } catch(PDOException $e) {
        return false;
    }
}

function generateTaxReport($startDate, $endDate) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                DATE(t.transaction_date) as date,
                SUM(t.total_amount) as total_sales,
                SUM(t.tax_amount) as total_tax
            FROM transactions t
            WHERE t.transaction_date BETWEEN ? AND ?
            GROUP BY DATE(t.transaction_date)
            ORDER BY date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return false;
    }
}

// Utility Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function logout() {
    if (isset($_SESSION['user_id'])) {
        logAudit($_SESSION['user_id'], 'user_logout', "User logged out: " . $_SESSION['username']);
    }
    session_destroy();
}
?>