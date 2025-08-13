<?php
session_start();

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "water";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST['email_or_username']);
    $password          = $_POST['password'];

    if (empty($email_or_username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $email_or_username, $email_or_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: dashboard.html");
                exit();
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "No account found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AquaPure - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0066cc, #66ccff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .water-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1505118380757-91f5f5632de0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
            animation: waterFlow 20s infinite linear;
        }
        
        .login-container {
            width: 400px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.8s 0.3s forwards;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 25px;
            animation: bounceIn 1s 0.5s both;
        }
        
        .logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        
        h2 {
            text-align: center;
            color: #0066cc;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 28px;
        }
        
        label {
            font-weight: 500;
            display: block;
            margin-top: 15px;
            color: #333;
            font-size: 14px;
            transform: translateX(-10px);
            opacity: 0;
            animation: fadeInRight 0.5s 0.7s forwards;
        }
        
        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-top: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: all 0.3s;
            transform: translateX(-10px);
            opacity: 0;
            animation: fadeInRight 0.5s 0.8s forwards;
        }
        
        input[type="text"]:focus, 
        input[type="password"]:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.2);
            outline: none;
        }
        
        button {
            width: 100%;
            background: linear-gradient(to right, #0066cc, #0099ff);
            color: white;
            padding: 14px;
            margin-top: 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
            transform: translateY(10px);
            opacity: 0;
            animation: fadeInUp 0.5s 1s forwards;
        }
        
        button:hover {
            background: linear-gradient(to right, #0055aa, #0088ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }
        
        p {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
            transform: translateY(10px);
            opacity: 0;
            animation: fadeInUp 0.5s 1.1s forwards;
        }
        
        a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        a:hover {
            color: #004488;
            text-decoration: underline;
        }
        
        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 5px;
            font-size: 14px;
            animation: shake 0.5s;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            60% {
                opacity: 1;
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        @keyframes waterFlow {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }
        
        .water-drop {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: drop 4s linear infinite;
            z-index: 0;
        }
        
        @keyframes drop {
            0% {
                transform: translateY(-100px) scale(0.5);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
            }
            90% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(100vh) scale(1);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="water-bg"></div>
    
    <!-- Animated water drops -->
    <div class="water-drop" style="left: 10%; animation-delay: 0s;"></div>
    <div class="water-drop" style="left: 20%; animation-delay: 0.5s;"></div>
    <div class="water-drop" style="left: 30%; animation-delay: 1s;"></div>
    <div class="water-drop" style="left: 40%; animation-delay: 1.5s;"></div>
    <div class="water-drop" style="left: 50%; animation-delay: 2s;"></div>
    <div class="water-drop" style="left: 60%; animation-delay: 2.5s;"></div>
    <div class="water-drop" style="left: 70%; animation-delay: 3s;"></div>
    <div class="water-drop" style="left: 80%; animation-delay: 3.5s;"></div>
    <div class="water-drop" style="left: 90%; animation-delay: 4s;"></div>
    
    <div class="login-container">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/3163/3163478.png" alt="AquaPure Logo">
        </div>
        
        <h2>Welcome to AquaPure</h2>
        
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <label>Email or Username:</label>
            <input type="text" name="email_or_username" placeholder="Enter your email or username">

            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter your password">

            <button type="submit">Login</button>
        </form>
        
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>

    <script>
        // Add more dynamic water drops
        function createWaterDrops() {
            const body = document.querySelector('body');
            const dropsCount = 15;
            
            for (let i = 0; i < dropsCount; i++) {
                const drop = document.createElement('div');
                drop.className = 'water-drop';
                drop.style.left = Math.random() * 100 + '%';
                drop.style.animationDelay = Math.random() * 5 + 's';
                drop.style.animationDuration = 3 + Math.random() * 3 + 's';
                drop.style.width = 10 + Math.random() * 15 + 'px';
                drop.style.height = drop.style.width;
                body.appendChild(drop);
            }
        }
        
        // Initialize on page load
        window.addEventListener('load', createWaterDrops);
        
        // Add ripple effect to button
        const loginBtn = document.querySelector('button[type="submit"]');
        loginBtn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            this.appendChild(ripple);
            
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            setTimeout(() => {
                ripple.remove();
            }, 1000);
        });
    </script>
</body>
</html>