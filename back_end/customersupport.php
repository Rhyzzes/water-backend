<?php
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "water";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare SQL and bind parameters
        $stmt = $conn->prepare("INSERT INTO support_tickets (name, email, subject, message, created_at) 
                               VALUES (:name, :email, :subject, :message, NOW())");
        
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':subject', $_POST['subject']);
        $stmt->bindParam(':message', $_POST['message']);
        
        $stmt->execute();
        
        $success_message = "Your message has been sent successfully! We'll get back to you soon.";
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaPure - Customer Support</title>
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

        .support-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .support-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--secondary-blue);
        }

        .support-card h2 {
            color: var(--primary-blue);
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .support-card p {
            color: var(--gray);
            line-height: 1.6;
        }

        .contact-form {
            margin-top: 2rem;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 120px;
        }

        .btn {
            background: linear-gradient(to right, var(--secondary-blue), var(--primary-blue));
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
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
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
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
            <a href="customersupport.php" style="background-color: rgba(255, 255, 255, 0.2)">Customer Support</a>
            <a href="about.php">About</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-content">
            <h1><span class="water-icon">🛎️</span> Customer Support</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="support-options">
                <div class="support-card">
                    <h2><span>📞</span> Phone Support</h2>
                    <p>Our customer service team is available 24/7 to assist you with any questions or concerns.</p>
                    <p><strong>Phone:</strong> +254 780-911-882</p>
                    <p><strong>Hours:</strong> 24/7</p>
                </div>
                
                <div class="support-card">
                    <h2><span>✉️</span> Email Support</h2>
                    <p>Send us an email and we'll respond within 24 hours.</p>
                    <p><strong>Email:</strong> support@aquapure.com</p>
                </div>
                
                <div class="support-card">
                    <h2><span>💬</span> Live Chat</h2>
                    <p>Chat with one of our representatives in real-time during business hours.</p>
                    <p><strong>Hours:</strong> Mon-Fri, 8AM-8PM EST</p>
                    <a href="https://wa.link/6gu5f3" class="btn" target="_blank">Start Chat</a>
                </div>
            </div>
            
            <div class="contact-form">
                <h2><span>📝</span> Contact Form</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">-- Select Subject --</option>
                            <option value="Delivery Issue">Delivery Issue</option>
                            <option value="Product Question">Product Question</option>
                            <option value="Billing Inquiry">Billing Inquiry</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>