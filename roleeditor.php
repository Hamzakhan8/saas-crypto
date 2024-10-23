<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    $_SESSION['error_message'] = "Access denied. Superadmin privileges required.";
    header("Location: index.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle role editing and user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $user_id = (int) $_POST['user_id'];
    
    if ($_POST['action'] == 'edit') {
        $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
        
        // Prevent superadmin from changing their own role
        if ($user_id == $_SESSION['user_id'] && $new_role != 'superadmin') {
            $_SESSION['error_message'] = "You cannot change your own superadmin role.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Role updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif ($_POST['action'] == 'delete') {
        // Prevent superadmin from deleting themselves
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error_message'] = "You cannot delete your own account.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: roleeditor.php");
    exit();
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
      <?php include 'sidebar.php'; ?>

        <div class="content flex-grow-1">
            <!-- Navbar -->
            <?php include 'navbar.php'; ?>

            <div class="container mt-5">
                <h2>Role Editor</h2>
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                    unset($_SESSION['error_message']);
                }
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                    unset($_SESSION['success_message']);
                }
                ?>
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
                                    <select name="new_role" class="form-select" id="role-<?= $user['id'] ?>" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="superadmin" <?= $user['role'] == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm edit-role" data-user-id="<?= $user['id'] ?>" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-user" data-user-id="<?= $user['id'] ?>" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit role functionality
        document.querySelectorAll('.edit-role').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const newRole = document.getElementById(`role-${userId}`).value;
                
                if (confirm('Are you sure you want to change this user\'s role?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'roleeditor.php';
                    
                    const userIdInput = document.createElement('input');
                    userIdInput.type = 'hidden';
                    userIdInput.name = 'user_id';
                    userIdInput.value = userId;
                    
                    const newRoleInput = document.createElement('input');
                    newRoleInput.type = 'hidden';
                    newRoleInput.name = 'new_role';
                    newRoleInput.value = newRole;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'edit';
                    
                    form.appendChild(userIdInput);
                    form.appendChild(newRoleInput);
                    form.appendChild(actionInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Delete user functionality
        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'roleeditor.php';
                    
                    const userIdInput = document.createElement('input');
                    userIdInput.type = 'hidden';
                    userIdInput.name = 'user_id';
                    userIdInput.value = userId;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    
                    form.appendChild(userIdInput);
                    form.appendChild(actionInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
    </script>
</body>
</html>
