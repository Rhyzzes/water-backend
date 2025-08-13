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

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']); // Remove non-numeric characters
    $location = htmlspecialchars($_POST['location']);
    $password = $_POST['password'];
    
    // Basic validation (frontend should have already done this)
    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (strlen($phone) < 10) {
        $errors[] = "Phone number must be at least 10 digits";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if ($password !== $_POST['confirmPassword']) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    $stmt->close();
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (email, phone, location, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $phone, $location, $hashed_password);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Registration successful
            header("Location: signup_success.html");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    // If there were errors, return to form with error messages
    if (!empty($errors)) {
        session_start();
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
}

$conn->close();
?>