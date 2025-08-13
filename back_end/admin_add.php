<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water";

// Get table from URL parameter
$current_table = $_GET['table'] ?? '';
$allowed_tables = ['products', 'support_tickets', 'users', 'teams'];
if (!in_array($current_table, $allowed_tables)) {
    header("Location: admin.php");
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$columns = [];
$result = $conn->query("SHOW COLUMNS FROM $current_table");
while ($row = $result->fetch_assoc()) {
    // Skip id, created_at, updated_at columns
    if (!in_array($row['Field'], ['id', 'created_at', 'updated_at'])) {
        $columns[$row['Field']] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fields = [];
    $values = [];
    
    foreach ($columns as $column => $info) {
        // Handle file upload for photo_url in teams table
        if ($column == 'photo_url' && $current_table == 'teams' && isset($_FILES['photo_url'])) {
            $upload_dir = 'images/team/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['photo_url']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['photo_url']['tmp_name'], $destination)) {
                $fields[] = $column;
                $values[] = "'" . $conn->real_escape_string($destination) . "'";
            }
        } 
        // Handle regular fields
        elseif (isset($_POST[$column])) {
            $fields[] = $column;
            $values[] = "'" . $conn->real_escape_string($_POST[$column]) . "'";
        }
    }
    
    if (!empty($fields)) {
        $sql = "INSERT INTO $current_table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "New record created successfully";
            header("Location: admin.php?table=$current_table");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New <?= ucfirst(str_replace('_', ' ', $current_table)) ?> | AquaPure Admin</title>
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
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .admin-header {
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

        .admin-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .form-container {
            background-color: var(--white);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        h2 {
            color: var(--primary-blue);
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray);
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
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 120px;
        }

        .btn {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary {
            background-color: var(--gray);
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 0.5rem;
            display: none;
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
        <h2>Add New <?= ucfirst(str_replace('_', ' ', $current_table)) ?></h2>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <?php foreach ($columns as $column => $info): ?>
                    <div class="form-group">
                        <label for="<?= $column ?>"><?= ucfirst(str_replace('_', ' ', $column)) ?></label>
                        
                        <?php if ($column == 'photo_url' && $current_table == 'teams'): ?>
                            <input type="file" id="photo_url" name="photo_url" accept="image/*" onchange="previewImage(this)">
                            <img id="preview" class="file-preview" alt="Image preview">
                            <script>
                                function previewImage(input) {
                                    const preview = document.getElementById('preview');
                                    const file = input.files[0];
                                    
                                    if (file) {
                                        preview.style.display = 'block';
                                        preview.src = URL.createObjectURL(file);
                                    } else {
                                        preview.style.display = 'none';
                                    }
                                }
                            </script>
                        
                        <?php elseif (strpos($info['Type'], 'text') !== false || strpos($info['Type'], 'varchar') !== false): ?>
                            <input type="text" id="<?= $column ?>" name="<?= $column ?>" 
                                   value="<?= htmlspecialchars($_POST[$column] ?? '') ?>">
                        
                        <?php elseif (strpos($info['Type'], 'int') !== false || strpos($info['Type'], 'decimal') !== false): ?>
                            <input type="number" id="<?= $column ?>" name="<?= $column ?>" 
                                   value="<?= htmlspecialchars($_POST[$column] ?? '') ?>"
                                   step="<?= strpos($info['Type'], 'decimal') !== false ? '0.01' : '1' ?>">
                        
                        <?php elseif (strpos($info['Type'], 'date') !== false): ?>
                            <input type="date" id="<?= $column ?>" name="<?= $column ?>" 
                                   value="<?= htmlspecialchars($_POST[$column] ?? '') ?>">
                        
                        <?php elseif (strpos($info['Type'], 'enum') !== false): ?>
                            <?php
                            preg_match("/enum\('(.+)'\)/", $info['Type'], $matches);
                            $options = explode("','", $matches[1]);
                            ?>
                            <select id="<?= $column ?>" name="<?= $column ?>">
                                <?php foreach ($options as $option): ?>
                                    <option value="<?= $option ?>"><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                        
                        <?php else: ?>
                            <textarea id="<?= $column ?>" name="<?= $column ?>"><?= htmlspecialchars($_POST[$column] ?? '') ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn">Save</button>
                <a href="admin.php?table=<?= $current_table ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>