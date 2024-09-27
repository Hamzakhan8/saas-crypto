<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user profile information
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    $user = null; // Set user to null if not found
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Update user information
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $update_query = "UPDATE users SET username='$username', password='$hashed_password' WHERE id='$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: index.php");
        exit();
    } else {
        echo "<p class='text-danger'>Error updating profile: " . mysqli_error($conn) . "</p>";
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>User Profile</h2>
        <?php if ($user): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Name:</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        <?php else: ?>
            <p class="text-danger">User not found. Please check your session or contact support.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>