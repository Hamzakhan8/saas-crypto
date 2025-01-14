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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int) $_POST['id'];
    $coin_name = mysqli_real_escape_string($conn, $_POST['coin_name']);
    $rr_percentage = (float) mysqli_real_escape_string($conn, $_POST['rr_percentage']);
    $total = (float) mysqli_real_escape_string($conn, $_POST['total']);
    $trade_time = mysqli_real_escape_string($conn, $_POST['trade_time']);
    
    $stmt = $conn->prepare("UPDATE trades SET coin_name=?, rr_percentage=?, total=?, trade_time=? WHERE id=?");
    $stmt->bind_param("sddsi", $coin_name, $rr_percentage, $total, $trade_time, $id);

    if ($stmt->execute()) {
        echo "Trade updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM trades WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trade = $result->fetch_assoc();
    $stmt->close();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="d-flex">
        <?php include '../sidebar.php'; ?>
        <div class="content flex-grow-1">
            <main class="p-4">
                <h2>Edit Trade</h2>
                <form action="update_trade.php" method="post">
                    <input type="hidden" name="id" value="<?= $trade['id'] ?>">
                    <div class="form-group mb-3">
                        <label for="coin_name">Coin Name:</label>
                        <input type="text" id="coin_name" name="coin_name" class="form-control" value="<?= htmlspecialchars($trade['coin_name']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="rr_percentage">R:R Percentage:</label>
                        <input type="number" id="rr_percentage" name="rr_percentage" class="form-control" step="0.01" value="<?= number_format($trade['rr_percentage'], 2) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="total">Total:</label>
                        <input type="number" id="total" name="total" class="form-control" step="0.01" value="<?= number_format($trade['total'], 2) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="trade_time">Trade Time:</label>
                        <input type="datetime-local" id="trade_time" name="trade_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($trade['trade_time'])) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Trade</button>
                </form>
            </main>
        </div>
    </div>
    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 