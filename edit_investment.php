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

// Fetch investment details for editing
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM investments WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $investment = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Invalid investment ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Investment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Investment</h2>
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $investment['id'] ?>">
            <div class="form-group mb-3">
                <label for="crypto-name">Crypto Name:</label>
                <input type="text" id="crypto-name" name="crypto_name" class="form-control" value="<?= htmlspecialchars($investment['crypto_name']) ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="amount-invested">Amount Invested ($):</label>
                <input type="number" id="amount-invested" name="amount_invested" class="form-control" value="<?= $investment['amount_invested'] ?>" step="0.01" required>
            </div>
            <div class="form-group mb-3">
                <label for="current-value">Current Value ($):</label>
                <input type="number" id="current-value" name="current_value" class="form-control" value="<?= $investment['current_value'] ?>" step="0.01" required>
            </div>
            <div class="form-group mb-3">
                <label for="date-invested">Date Invested:</label>
                <input type="date" id="date-invested" name="date_invested" class="form-control" value="<?= $investment['date_invested'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Investment</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>