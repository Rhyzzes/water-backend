<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "water";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current table or default to products
$current_table  = $_GET['table'] ?? 'products';
$allowed_tables = ['products', 'support_tickets', 'users', 'teams'];
if (!in_array($current_table, $allowed_tables)) {
    $current_table = 'products';
}

// Handle delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Cast to int for safety
    $stmt = $conn->prepare("DELETE FROM $current_table WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Record deleted successfully";
    } else {
        $error = "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}

// Fetch data for current table
$sql    = "SELECT * FROM $current_table";
$result = $conn->query($sql);
$columns = [];
$data    = [];
if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    if (!empty($data)) {
        $columns = array_keys($data[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaPure - Admin Dashboard</title>
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
            background-color: #f5f5f5;
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
        .admin-nav {
            display: flex;
            background-color: var(--white);
            padding: 0.5rem 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .admin-nav a {
            color: var(--gray);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }
        .admin-container {
            display: flex;
            min-height: calc(100vh - 120px);
        }
        .sidebar {
            width: 250px;
            background-color: var(--white);
            padding: 1.5rem;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h3 {
            color: var(--primary-blue);
            border-bottom: 2px solid var(--light-blue);
            padding-bottom: 0.5rem;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        .sidebar-menu a {
            display: block;
            padding: 0.7rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        .table-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-blue);
        }
        th {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            font-weight: 600;
        }
        tr:hover {
            background-color: rgba(202, 240, 248, 0.3);
        }
        .action-btns a {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            margin-right: 0.3rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
        }
        .edit-btn {
            background-color: var(--secondary-blue);
            color: white;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .add-btn {
            background-color: #28a745;
            color: white;
            padding: 0.6rem 1rem;
            margin-bottom: 1rem;
            display: inline-block;
            text-decoration: none;
            border-radius: 5px;
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
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="logo">
            <span class="water-icon">💧</span>
            <span>Aqua<span>Pure</span> Admin</span>
        </div>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="admin-nav">
        <a href="?table=products" class="<?= $current_table == 'products' ? 'active' : '' ?>">Products</a>
        <a href="?table=support_tickets" class="<?= $current_table == 'support_tickets' ? 'active' : '' ?>">Support Tickets</a>
        <a href="?table=users" class="<?= $current_table == 'users' ? 'active' : '' ?>">Users</a>
        <a href="?table=teams" class="<?= $current_table == 'teams' ? 'active' : '' ?>">Team Members</a>
    </div>

    <div class="admin-container">
        <div class="sidebar">
            <h3>Admin Actions</h3>
            <ul class="sidebar-menu">
                <li><a href="?table=<?= $current_table ?>" class="active">View <?= ucfirst(str_replace('_', ' ', $current_table)) ?></a></li>
                <li><a href="admin_add.php?table=<?= $current_table ?>">Add New <?= rtrim(ucfirst(str_replace('_', ' ', $current_table)), 's') ?></a></li>

        </div>

        <div class="main-content">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <h2><?= ucfirst(str_replace('_', ' ', $current_table)) ?> Management</h2>
            <a href="admin_add.php?table=<?= $current_table ?>" class="add-btn">+ Add New</a>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <td>
                                        <?php 
                                        if (in_array($column, ['created_at','updated_at'])) {
                                            echo date('M j, Y g:i A', strtotime($row[$column]));
                                        } elseif ($column === 'price') {
                                            echo '$' . number_format($row[$column], 2);
                                        } else {
                                            echo substr($row[$column], 0, 50) . (strlen($row[$column]) > 50 ? '...' : '');
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="action-btns">
                                    <a href="admin_edit.php?table=<?= $current_table ?>&id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                                    <a href="?table=<?= $current_table ?>&delete=1&id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
