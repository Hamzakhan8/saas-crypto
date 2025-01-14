<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = ""; // Variable to hold success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $coin_name = mysqli_real_escape_string($conn, $_POST['coin_name']);
    $rr_percentage = (float) mysqli_real_escape_string($conn, $_POST['rr_percentage']);
    $total = (float) mysqli_real_escape_string($conn, $_POST['total']);
    $trade_time = mysqli_real_escape_string($conn, $_POST['trade_time']);
    
    $stmt = $conn->prepare("INSERT INTO trades (coin_name, rr_percentage, total, trade_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdds", $coin_name, $rr_percentage, $total, $trade_time);

    if ($stmt->execute()) {
        $message = "Trade added successfully!";
        // Redirect to read_trades.php after successful addition
        header("Location: read_trades.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

mysqli_close($conn);

// Include the new sidebar for create_trade.php
include 'sidebar_create_trade.php'; // Updated path to the new sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="d-flex">
        <?php include '../sidebar.php'; ?>
        <div class="content flex-grow-1">
            <main class="p-4">
                <h2>Add Trade</h2>
                <?php if ($message): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form action="create_trade.php" method="post">
                    <div class="form-group mb-3">
                        <label for="coin_name">Coin Name:</label>
                        <input type="text" id="coin_name" name="coin_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="rr_percentage">R:R Percentage:</label>
                        <input type="number" id="rr_percentage" name="rr_percentage" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="total">Total:</label>
                        <input type="number" id="total" name="total" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="trade_time">Trade Time:</label>
                        <input type="datetime-local" id="trade_time" name="trade_time" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Trade</button>
                </form>
                <a href="read_trades.php" class="btn btn-secondary mt-3">View Trades</a> <!-- Button to view trades -->
            </main>
        </div>
    </div>
    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 