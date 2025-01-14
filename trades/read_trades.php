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

$result = mysqli_query($conn, "SELECT * FROM trades");
$trades = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trades Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="d-flex">
        <?php include '../sidebar.php'; ?>
        <div class="content flex-grow-1">
            <main class="p-4">
                <h2>Trades Overview</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Coin Name</th>
                            <th>R:R Percentage</th>
                            <th>Total</th>
                            <th>Trade Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trades as $trade) : ?>
                            <tr>
                                <td><?= htmlspecialchars($trade['coin_name']) ?></td>
                                <td><?= number_format($trade['rr_percentage'], 2) ?></td>
                                <td><?= number_format($trade['total'], 2) ?></td>
                                <td><?= htmlspecialchars($trade['trade_time']) ?></td>
                                <td>
                                    <a href="update_trade.php?id=<?= $trade['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_trade.php?id=<?= $trade['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 