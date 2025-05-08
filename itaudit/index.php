<?php
require_once 'includes/functions.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Parts Shop - Premium PC Components</title>
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
            overflow-x: hidden;
            background-color: var(--white);
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(44, 82, 130, 0.3);
        }
        
        /* Navbar */
        .navbar {
            padding: 20px 0;
            transition: all 0.4s ease;
        }
        
        .navbar.scrolled {
            background-color: var(--white);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }
        
        .navbar.scrolled .nav-link {
            color: var(--primary-dark) !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--white);
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            margin-right: 10px;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--white) !important;
            margin: 0 15px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-light) !important;
        }
        
        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1614624532603-258e3f8aa10c?ixlib=rb-4.0.3') no-repeat center center/cover;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to top, var(--white), transparent);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 40px;
            max-width: 600px;
        }
        
        /* Features Section */
        .features {
            padding: 100px 0;
            background-color: var(--white);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .section-subtitle {
            color: var(--primary-light);
            font-weight: 500;
            margin-bottom: 40px;
        }
        
        .feature-card {
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            background-color: var(--white);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: var(--white);
            font-size: 1.5rem;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        
        /* Products Section */
        .products {
            padding: 100px 0;
            background-color: var(--light-gray);
        }
        
        .product-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-details {
            padding: 20px;
        }
        
        .product-category {
            color: var(--primary-light);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-gray);
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        /* Accounts Section */
        .accounts {
            padding: 100px 0;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }
        
        .accounts::before {
            content: "";
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .accounts::after {
            content: "";
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .accounts .section-title {
            color: var(--white);
        }
        
        .accounts .section-subtitle {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .account-card {
            background-color: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
            text-align: center;
            height: 100%;
        }
        
        .account-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: var(--white);
            font-size: 2rem;
        }
        
        .account-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        
        .account-card p {
            margin-bottom: 25px;
            color: var(--dark-gray);
        }
        
        /* Contact Section */
        .contact {
            padding: 100px 0;
            background-color: var(--light-gray);
        }
        
        .contact-info {
            margin-bottom: 30px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            margin-right: 15px;
        }
        
        .contact-form {
            background-color: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(44, 82, 130, 0.3);
        }
        
        /* Footer */
        .footer {
            padding: 30px 0;
            background-color: var(--primary-dark);
            color: var(--white);
            text-align: center;
        }
        
        .footer-logo {
            margin-bottom: 20px;
            border-radius: 10px;
            max-height: 50px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--white);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .feature-card {
                margin-bottom: 20px;
            }
            
            .account-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-microchip fa-2x me-2"></i>
                Computer Parts Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#products">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#accounts">Accounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="row hero-content">
                <div class="col-lg-8">
                    <h1>Premium Computer Parts<br>For Ultimate Performance</h1>
                    <p>Welcome to the Computer Parts Shop, your one-stop destination for high-quality computer components. Elevate your computing experience with our premium selection of parts.</p>
                    <a href="#products" class="btn btn-primary">Explore Products</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Why Choose Us</h2>
                    <p class="section-subtitle">Premier destination for all your computing needs</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3>Premium Components</h3>
                        <p>We offer only the highest quality computer components from trusted manufacturers to ensure optimal performance and reliability.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Fast Delivery</h3>
                        <p>Get your orders quickly with our efficient shipping process. Same-day shipping available for orders placed before 2 PM.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Expert Support</h3>
                        <p>Our knowledgeable team is ready to assist with technical questions and help you choose the right components for your needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="products" class="products">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Featured Products</h2>
                    <p class="section-subtitle">Browse our top-selling components</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/Intel Core i9-13900K.webp" alt="CPU">
                        </div>
                        <div class="product-details">
                            <div class="product-category">CPU</div>
                            <h3 class="product-title">Intel Core i9-13900K</h3>
                            <div class="product-price">₱34,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/NVIDIA RTX 4090.jpg" alt="GPU">
                        </div>
                        <div class="product-details">
                            <div class="product-category">GPU</div>
                            <h3 class="product-title">NVIDIA RTX 4090</h3>
                            <div class="product-price">₱89,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/G.Skill Trident Z5 32GB.webp" alt="RAM">
                        </div>
                        <div class="product-details">
                            <div class="product-category">RAM</div>
                            <h3 class="product-title">G.Skill Trident Z5 32GB</h3>
                            <div class="product-price">₱13,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/Samsung 990 Pro 2TB.webp" alt="Storage">
                        </div>
                        <div class="product-details">
                            <div class="product-category">Storage</div>
                            <h3 class="product-title">Samsung 990 Pro 2TB</h3>
                            <div class="product-price">₱14,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/ASUS ROG Maximus Z790.png" alt="Motherboard">
                        </div>
                        <div class="product-details">
                            <div class="product-category">Motherboard</div>
                            <h3 class="product-title">ASUS ROG Maximus Z790</h3>
                            <div class="product-price">₱29,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/Lian Li O11 Dynamic.webp" alt="Case">
                        </div>
                        <div class="product-details">
                            <div class="product-category">Case</div>
                            <h3 class="product-title">Lian Li O11 Dynamic</h3>
                            <div class="product-price">₱8,999.00</div>
                            <a href="login.php" class="btn btn-primary btn-sm">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="login.php" class="btn btn-primary">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Accounts Section -->
    <section id="accounts" class="accounts">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Join Our Community</h2>
                    <p class="section-subtitle">Create an account to enjoy special benefits</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="account-card">
                        <div class="account-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>Customer Account</h3>
                        <p>Create a customer account to shop our products, track your orders, and receive exclusive offers.</p>
                        <a href="register.php" class="btn btn-primary">Register Now</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="account-card">
                        <div class="account-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>Already Have an Account?</h3>
                        <p>Log in to your existing account to manage your orders and continue shopping.</p>
                        <a href="login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Get In Touch</h2>
                    <p class="section-subtitle">We're here to answer your questions</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5>Our Location</h5>
                                <p>123 Tech Street, Silicon Valley, Philippines</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h5>Phone Number</h5>
                                <p>+63 912 345 6789</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5>Email Address</h5>
                                <p>info@computerpartsshop.com</p>
                            </div>
                        </div>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form class="contact-form">
                        <input type="text" class="form-control" placeholder="Your Name" required>
                        <input type="email" class="form-control" placeholder="Your Email" required>
                        <input type="text" class="form-control" placeholder="Subject">
                        <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <i class="fas fa-microchip fa-3x mb-3"></i>
            <div class="footer-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#products">Products</a>
                <a href="#accounts">Accounts</a>
                <a href="#contact">Contact</a>
            </div>
            <p>&copy; <?php echo date('Y'); ?> Computer Parts Shop. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll behavior
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>