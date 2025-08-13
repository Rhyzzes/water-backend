<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get product details from POST data
    $product_id = $_POST['product_id'] ?? null;
    
    if ($product_id) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            header("Location: products.html?error=product_not_found");
            exit();
        }
    } else {
        header("Location: products.php?error=no_product_selected");
        exit();
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaPure - Payment</title>
    <style>
:root {
--primary-blue: #0077b6;
--secondary-blue: #00b4d8;
--light-blue: #caf0f8;
--dark-blue: #005f8a;
--white: #ffffff;
--gray: #666666;
}

body {
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
margin: 0;
padding: 0;
background-color: #e6f7ff;
background-image:
linear-gradient(to bottom, rgba(230, 247, 255, 0.9), rgba(173, 216, 230, 0.7)),
url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%2300b4d8" fill-opacity="0.1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
background-repeat: no-repeat;
background-position: bottom;
background-size: contain;
min-height: 100vh;
}

/* Header Styles */
header {
background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
color: white;
padding: 1rem 2rem;
display: flex;
justify-content: space-between;
align-items: center;
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo {
font-size: 1.5rem;
font-weight: bold;
display: flex;
align-items: center;
}

.logo span {
color: var(--light-blue);
margin-left: 0.5rem;
}

.water-icon {
font-size: 1.8rem;
margin-right: 0.5rem;
}

/* Navigation */
nav {
display: flex;
gap: 1.5rem;
}

nav a {
color: white;
text-decoration: none;
font-weight: 500;
padding: 0.5rem 1rem;
border-radius: 5px;
transition: background-color 0.3s;
}

nav a:hover {
background-color: rgba(255, 255, 255, 0.2);
}

/* Main Content */
.container {
max-width: 1200px;
margin: 2rem auto;
padding: 0 2rem;
}

.page-content {
background-color: rgba(255, 255, 255, 0.9);
border-radius: 10px;
padding: 2rem;
box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
min-height: 60vh;
}

h1 {
color: var(--primary-blue);
border-bottom: 2px solid var(--light-blue);
padding-bottom: 0.5rem;
display: flex;
align-items: center;
gap: 0.5rem;
}

.product-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
gap: 2rem;
margin-top: 2rem;
}

.product-card {
background: white;
border-radius: 8px;
overflow: hidden;
box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
transition: transform 0.3s;
}

.product-card:hover {
transform: translateY(-5px);
}

.product-image {
height: 200px;
background-color: var(--light-blue);
display: flex;
align-items: center;
justify-content: center;
font-size: 3rem;
color: var(--primary-blue);
}

.product-details {
padding: 1.5rem;
}

.product-title {
font-weight: 600;
color: var(--primary-blue);
margin: 0 0 0.5rem;
}

.product-price {
font-weight: bold;
color: var(--dark-blue);
font-size: 1.2rem;
margin: 0.5rem 0;
}

.product-description {
color: var(--gray);
font-size: 0.9rem;
margin: 0.5rem 0;
}

.btn {
display: inline-block;
background: linear-gradient(to right, var(--secondary-blue), var(--primary-blue));
color: white;
padding: 0.6rem 1.2rem;
border-radius: 5px;
text-decoration: none;
font-weight: 500;
margin-top: 1rem;
transition: all 0.3s;
}

.btn:hover {
background: linear-gradient(to right, var(--primary-blue), var(--dark-blue));
box-shadow: 0 2px 10px rgba(0, 180, 216, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
header {
flex-direction: column;
padding: 1rem;
}

nav {
margin-top: 1rem;
flex-wrap: wrap;
justify-content: center;
}

.container {
padding: 0 1rem;
}

.product-grid {
grid-template-columns: 1fr;
}
}
        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .order-summary {
            background-color: var(--light-blue);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-blue);
            font-weight: 500;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <span class="water-icon">💧</span>
            <span>Aqua<span>Pure</span></span>
        </div>
        
        <nav>
            <a href="dashboard.html">Home</a>
            <a href="products.php">Products</a>
            <a href="customersupport.php">Customer Support</a>
            <a href="about.php">About</a>
        </nav>
    </header>

    <div class="container">
        <div class="payment-container">
            <h1><span class="water-icon">💳</span> Complete Your Order</h1>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <p><strong>Product:</strong> <?php echo htmlspecialchars($product['name']).' ('.htmlspecialchars($product['size']).')'; ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            </div>
            
            <form action="process_payment.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <h2>Delivery Information</h2>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <h2>Payment Method</h2>
                <div class="form-group">
                    <label for="payment_method">Select Payment Method</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">-- Select --</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cash_on_delivery">Cash on Delivery</option>
                    </select>
                </div>
                
                <div id="credit_card_fields" style="display:none;">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number">
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                    </div>
                    
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv">
                    </div>
                </div>
                
                <div id="mobile_money_fields" style="display:none;">
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number">
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile_provider">Provider</label>
                        <select id="mobile_provider" name="mobile_provider">
                            <option value="Telkom">Telkom</option>
                            <option value="airtel">Airtel</option>
                            <option value="Safaricom">Safaricom</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn">Complete Payment</button>
            </form>
        </div>
    </div>

    <script>
        // Show/hide payment method fields based on selection
        document.getElementById('payment_method').addEventListener('change', function() {
            document.getElementById('credit_card_fields').style.display = 'none';
            document.getElementById('mobile_money_fields').style.display = 'none';
            
            if (this.value === 'credit_card') {
                document.getElementById('credit_card_fields').style.display = 'block';
            } else if (this.value === 'mobile_money') {
                document.getElementById('mobile_money_fields').style.display = 'block';
            }
        });
    </script>
</body>
</html>