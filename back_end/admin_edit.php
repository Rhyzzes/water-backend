<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? 0;
$is_edit = !empty($id);

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$columns = [];
if (!empty($table)) {
    $sql = "SHOW COLUMNS FROM $table";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] != 'id' && $row['Field'] != 'created_at' && $row['Field'] != 'updated_at') {
            $columns[$row['Field']] = $row;
        }
    }
}

// Get existing data if editing
$existing_data = [];
if ($is_edit) {
    $sql = "SELECT * FROM $table WHERE id = $id";
    $result = $conn->query($sql);
    $existing_data = $result->fetch_assoc();
}
// Handle image upload
if (!empty($_FILES['photo']['tmp_name'])) {
    $upload_dir = 'images/team/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
        // Save path to database
        $photo_url = $destination;
    } else {
        $error = "Failed to upload image";
    }
}

// Then include $photo_url in your INSERT/UPDATE query
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $values = [];
    foreach ($columns as $column => $info) {
        $value = $_POST[$column] ?? '';
        $values[] = "$column = '" . $conn->real_escape_string($value) . "'";
    }
    
    if ($is_edit) {
        $sql = "UPDATE $table SET " . implode(', ', $values) . " WHERE id = $id";
    } else {
        $sql = "INSERT INTO $table SET " . implode(', ', $values);
    }
    
    if ($conn->query($sql)) {
        $success = "Record " . ($is_edit ? "updated" : "added") . " successfully";
        if (!$is_edit) {
            header("Location: admin.php?table=$table");
            exit();
        }
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit' : 'Add' ?> <?= ucfirst($table) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .admin-header {
            background: linear-gradient(90deg, #0077b6, #00b4d8);
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
            color: #caf0f8;
            margin-left: 0.5rem;
        }
        .water-icon {
            font-size: 1.8rem;
            margin-right: 0.5rem;
        }
        .admin-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-weight: 500;
        }
        .form-group input, 
        .form-group textarea, 
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-group textarea {
            min-height: 120px;
        }
        .btn {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="logo">
            <span class="water-icon">💧</span>
            <span>Aqua<span>Pure</span> Admin</span>
        </div>
    </div>

    <div class="admin-container">
        <h2><?= $is_edit ? 'Edit' : 'Add New' ?> <?= ucfirst(str_replace('_', ' ', $table)) ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <?php foreach ($columns as $column => $info): ?>
                    <div class="form-group">
                        <label for="<?= $column ?>"><?= ucfirst(str_replace('_', ' ', $column)) ?></label>
                        <?php if ($info['Type'] == 'text'): ?>
                            <textarea id="<?= $column ?>" name="<?= $column ?>"><?= $existing_data[$column] ?? '' ?></textarea>
                        <?php elseif (strpos($info['Type'], 'enum') === 0): ?>
                            <?php
                            preg_match("/enum\('(.+)'\)/", $info['Type'], $matches);
                            $options = explode("','", $matches[1]);
                            ?>
                            <select id="<?= $column ?>" name="<?= $column ?>">
                                <?php foreach ($options as $option): ?>
                                    <option value="<?= $option ?>" <?= (isset($existing_data[$column]) && $existing_data[$column] == $option) ? 'selected' : '' ?>>
                                        <?= ucfirst($option) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?= $info['Type'] == 'date' ? 'date' : 'text' ?>" 
                                   id="<?= $column ?>" 
                                   name="<?= $column ?>" 
                                   value="<?= $existing_data[$column] ?? '' ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn"><?= $is_edit ? 'Update' : 'Save' ?></button>
                <a href="admin.php?table=<?= $table ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>