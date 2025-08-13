<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch team members from database
    $stmt = $conn->prepare("SELECT * FROM teams ORDER BY position_order ASC");
    $stmt->execute();
    $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $teamError = "Error loading team members: " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaPure - About Us</title>
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

        .about-section {
            margin-bottom: 2rem;
        }

        .about-section h2 {
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .about-section p {
            color: var(--gray);
            line-height: 1.6;
        }

        .mission-statement {
            background-color: var(--light-blue);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
            border-left: 4px solid var(--secondary-blue);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-member {
            text-align: center;
        }

        .team-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: var(--light-blue);
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-blue);
        }

        .team-member h3 {
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }

        .team-member p {
            color: var(--gray);
            margin: 0;
            font-size: 0.9rem;
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

            .team-grid {
                grid-template-columns: 1fr;
            }
        }
        .team-error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
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
            <a href="about.php" style="background-color: rgba(255, 255, 255, 0.2)">About</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-content">
            <h1><span class="water-icon">ℹ️</span> About AquaPure</h1>
            
            <div class="about-section">
                <h2><span>🌊</span> Our Story</h2>
                <p>Founded in 2010, AquaPure began with a simple mission: to provide the purest, most refreshing water to our community. What started as a small local delivery service has grown into a trusted regional provider of premium water products.</p>
            </div>
            
            <div class="mission-statement">
                <h2><span>🎯</span> Our Mission</h2>
                <p>To deliver exceptional quality water with unmatched convenience, while preserving and protecting our planet's precious water resources for future generations.</p>
            </div>
            
            <div class="about-section">
                <h2><span>♻️</span> Sustainability</h2>
                <p>We're committed to environmentally responsible practices. All our bottles are 100% recyclable, and we've reduced our carbon footprint by 40% through optimized delivery routes and electric vehicles.</p>
            </div>
            
            <div class="about-section">
                <h2><span>👥</span> Meet the Team</h2>
                
                <?php if (isset($teamError)): ?>
                    <div class="team-error"><?php echo $teamError; ?></div>
                <?php endif; ?>
                
                <div class="team-grid">
                    <?php if (!empty($teamMembers)): ?>
                        <?php foreach ($teamMembers as $member): ?>
                            <div class="team-member">
                            <div class="team-photo">
    <?php if (!empty($member['photo_url'])): ?>
        <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" 
             alt="<?php echo htmlspecialchars($member['name']); ?>"
             style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
    <?php else: ?>
        <div style="font-size:3rem;"><?php echo substr($member['name'], 0, 1); ?></div>
    <?php endif; ?>
</div>
                                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                <p><?php echo htmlspecialchars($member['position']); ?></p>
                                <?php if (!empty($member['bio'])): ?>
                                    <p style="font-size:0.8rem;margin-top:0.5rem;"><?php echo htmlspecialchars($member['bio']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No team members found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>