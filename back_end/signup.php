<?php
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "water";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $location = $_POST['location'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($phone) || empty($location) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or Email already exists.";
        }
        $stmt->close();

        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, location, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $phone, $location, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Account created successfully. <a href='login.php'>Login here</a>";
            } else {
                $errors[] = "Error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AquaPure - Sign Up</title>
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
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 40px 0;
        }

        .water-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
            animation: waterFlow 20s infinite linear;
        }

        /* Water drop animation */
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
            0% { transform: translateY(-100px) scale(0.5); opacity: 0; }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% { transform: translateY(100vh) scale(1); opacity: 0; }
        }

        .container {
            width: 90%;
            max-width: 450px; /* smaller form */
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            z-index: 1;
            animation: fadeInUp 0.8s 0.3s forwards;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        h2 {
            text-align: center;
            color: #0066cc;
            margin-bottom: 20px;
            font-size: 22px;
        }

        label {
            font-weight: 500;
            display: block;
            margin-top: 10px;
            color: #333;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            margin: 6px 0 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: #0066cc;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
        }

        button {
            width: 100%;
            background: linear-gradient(to right, #0066cc, #0099ff);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(to right, #0055aa, #0088ee);
        }

        .error, .success {
            padding: 10px;
            margin-bottom: 12px;
            font-size: 13px;
            border-radius: 4px;
        }

        .error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }

        .success {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border-left: 4px solid #2ecc71;
        }

        p {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes waterFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>
    <div class="water-bg"></div>

    <!-- Water drops -->
    <div class="water-drop" style="left: 10%; animation-delay: 0s;"></div>
    <div class="water-drop" style="left: 20%; animation-delay: 0.5s;"></div>
    <div class="water-drop" style="left: 35%; animation-delay: 1s;"></div>
    <div class="water-drop" style="left: 50%; animation-delay: 1.5s;"></div>
    <div class="water-drop" style="left: 65%; animation-delay: 2s;"></div>
    <div class="water-drop" style="left: 80%; animation-delay: 2.5s;"></div>
    <div class="water-drop" style="left: 90%; animation-delay: 3s;"></div>

    <div class="container">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/3163/3163478.png" alt="AquaPure Logo">
        </div>

        <h2>Create Account</h2>

        <?php
        if (!empty($errors)) {
            echo "<div class='error'><ul>";
            foreach ($errors as $err) echo "<li>$err</li>";
            echo "</ul></div>";
        }
        if ($success) {
            echo "<div class='success'>$success</div>";
        }
        ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Location:</label>
            <select name="location" required>
                <option value="">-- Select County --</option>
                <option value="Nairobi">Nairobi</option>
                <option value="Mombasa">Mombasa</option>
                <option value="Kisumu">Kisumu</option>
                <option value="Nakuru">Nakuru</option>
                <option value="Uasin Gishu">Uasin Gishu</option>
                <option value="Kiambu">Kiambu</option>
                <option value="Machakos">Machakos</option>
                <option value="Kericho">Kericho</option>
                <option value="Nyeri">Nyeri</option>
                <option value="Kakamega">Kakamega</option>
            </select>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Create extra animated drops dynamically
        function createWaterDrops() {
            const dropsCount = 10;
            for (let i = 0; i < dropsCount; i++) {
                const drop = document.createElement('div');
                drop.className = 'water-drop';
                drop.style.left = Math.random() * 100 + '%';
                drop.style.animationDelay = Math.random() * 5 + 's';
                drop.style.animationDuration = (3 + Math.random() * 3) + 's';
                drop.style.width = drop.style.height = (10 + Math.random() * 15) + 'px';
                document.body.appendChild(drop);
            }
        }
        window.addEventListener('load', createWaterDrops);
    </script>
</body>
</html>
