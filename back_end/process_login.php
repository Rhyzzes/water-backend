<?php
// process_login.php
session_start(); // Start session at the beginning

$servername = "localhost";
$username = "root";
$password = ""; // Removed space if your password is empty
$dbname = "water";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = $_POST['password'];

    // Prepare SQL query to check email/username and password
    $sql = "SELECT id, email, username, password FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password against hashed password in database
        if (password_verify($password, $user['password'])) {
            // Authentication successful - set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['username'] = $user['username'] ?? '';
            $_SESSION['logged_in'] = true;
            
            // Redirect to dashboard
            header("Location: dashboard.html");
            exit();
        } else {
            // Invalid password
            $_SESSION['login_error'] = "Invalid password";
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // User not found
        $_SESSION['login_error'] = "User not found";
        header("Location: login.php?error=user_not_found");
        exit();
    }

    $stmt->close();
} else {
    // Not a POST request
    header("Location: login.php");
    exit();
}

$conn->close();
?>