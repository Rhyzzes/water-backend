<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaPure - Our Products</title>
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
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: linear-gradient(to right, var(--primary-blue), var(--dark-blue));
            box-shadow: 0 2px 10px rgba(0, 180, 216, 0.3);
        }
        .btn-disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
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
    </style>
</head>
<body>
<header>
    <div class="logo">
        <span class="water-icon">💧</span>
        Aqua<span>Pure</span>
    </div>
    <nav>
        <a href="dashboard.html">Home</a>
        <a href="products.php" style="background-color: rgba(255, 255, 255, 0.2)">Products</a>
        <a href="customersupport.php">Customer Support</a>
        <a href="about.php">About</a>
    </nav>
</header>

<div class="container">
    <div class="page-content">
        <h1><span class="water-icon">🚰</span> Our Water Products</h1>
        <div class="product-grid">
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "water";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM products");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($products) > 0) {
                    foreach ($products as $product) {
                        $name = htmlspecialchars($product['name']);
                        $size = htmlspecialchars($product['size']);
                        $desc = htmlspecialchars($product['description']);
                        $price = number_format($product['price'], 2);
                        $stock = (int)$product['stock_quantity'];
                        $image = !empty($product['image_path']) && file_exists($product['image_path']) 
                            ? '<img src="'.htmlspecialchars($product['image_path']).'" alt="'.$name.'" style="width:100%;height:100%;object-fit:cover;">'
                            : '<div style="font-size:3rem;">💧</div>';

                        echo '
                        <div class="product-card">
                            <div class="product-image">'.$image.'</div>
                            <div class="product-details">
                                <h3 class="product-title">'.$name.' ('.$size.')</h3>
                                <div class="product-price">$'.$price.'</div>
                                <p class="product-description">'.$desc.'</p>
                                <div class="product-stock" style="font-size:0.8rem;color:'.($stock > 0 ? 'green' : 'red').'">
                                    '.($stock > 0 ? 'In Stock ('.$stock.' available)' : 'Out of Stock').'
                                </div>
                                <form action="payment.php" method="POST">
                                    <input type="hidden" name="product_id" value="'.(int)$product['id'].'">
                                    <input type="hidden" name="product_name" value="'.$name.' '.$size.'">
                                    <input type="hidden" name="product_price" value="'.$product['price'].'">
                                    <button type="submit" class="btn '.($stock <= 0 ? 'btn-disabled' : '').'" '.($stock <= 0 ? 'disabled' : '').'>
                                        '.($stock > 0 ? 'Order Now' : 'Out of Stock').'
                                    </button>
                                </form>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>No products found in the database.</p>';
                }
            } catch(PDOException $e) {
                echo '<p>Error loading products: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            $conn = null;
            ?>
        </div>
    </div>
</div>
</body>
</html>
