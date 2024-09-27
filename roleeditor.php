<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: index.php"); // Redirect if not superadmin
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle role editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $user_id = (int) $_POST['user_id'];
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        echo "Role updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all users for role editing
$result = mysqli_query($conn, "SELECT id, username, role FROM users");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar with Glass Effect -->
    <div class="d-flex">
        <div id="sidebar" class="sidebar glass-effect p-3">
            <h2 class="">Crypto Dashboard</h2>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fa fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="roleeditor.php"><i class="fa fa-user"></i>Role Editor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
                <!-- Add more menu items as needed -->
            </ul>
        </div>

        <div class="content flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-outline-primary" id="sidebarToggle">
                        <span id="toggleIcon">☰</span>
                    </button>
                    <a class="navbar-brand ms-3" href="#">Crypto Investment Dashboard</a>
                </div>
            </nav>

            <div class="container mt-5">
                <h2>Role Editor</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Current Role</th>
                            <th>New Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td>
                                    <form action="roleeditor.php" method="post">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="new_role" class="form-select">
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                            <option value="superadmin">Superadmin</option>
                                        </select>
                                </td>
                                <td>
                                    <button type="submit" name="action" value="edit" class="btn btn-primary">Edit Role</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
