<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    die("Access deny please contact admin staff ");
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission for adding a new investment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $crypto_name = mysqli_real_escape_string($conn, $_POST['crypto_name']);
    $amount_invested = (float) mysqli_real_escape_string($conn, $_POST['amount_invested']);
    $current_value = (float) mysqli_real_escape_string($conn, $_POST['current_value']);
    $date_invested = mysqli_real_escape_string($conn, $_POST['date_invested']);
    $profit = $current_value + $amount_invested;
    $loss = $amount_invested - $current_value;
    $stmt = $conn->prepare("INSERT INTO investments (crypto_name, amount_invested, current_value, profit, loss, date_invested) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdddds", $crypto_name, $amount_invested, $current_value, $profit, $loss, $date_invested);

    if ($stmt->execute()) {
        echo "Investment added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle form submission for editing an investment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int) $_POST['id'];
    $crypto_name = mysqli_real_escape_string($conn, $_POST['crypto_name']);
    $amount_invested = (float) mysqli_real_escape_string($conn, $_POST['amount_invested']);
    $current_value = (float) mysqli_real_escape_string($conn, $_POST['current_value']);
    $date_invested = mysqli_real_escape_string($conn, $_POST['date_invested']);
    $profit = $current_value + $amount_invested;
    $loss = $amount_invested - $current_value;
    $stmt = $conn->prepare("UPDATE investments SET crypto_name=?, amount_invested=?, current_value=?, profit=?, loss=?, date_invested=? WHERE id=?");
    $stmt->bind_param("sddddsi", $crypto_name, $amount_invested, $current_value, $profit, $loss, $date_invested, $id);

    if ($stmt->execute()) {
        echo "Investment updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
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
}

// Fetch all investments
$result = mysqli_query($conn, "SELECT * FROM investments");
$investments = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Investment Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <div class="content flex-grow-1">
            <?php include 'navbar.php'; ?>

            <main>
                <form id="investment-form" action="index.php" method="post" class="p-4">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group mb-3">
                        <label for="crypto-name">Crypto Name:</label>
                        <input type="text" id="crypto-name" name="crypto_name" class="form-control" placeholder="Crypto Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="amount-invested">Amount Invested ($):</label>
                        <input type="number" id="amount-invested" name="amount_invested" class="form-control" placeholder="Amount Invested ($)" step="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="current-value">Current Value ($):</label>
                        <input type="number" id="current-value" name="current_value" class="form-control" placeholder="Current Value ($)" step="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="date-invested">Date Invested:</label>
                        <input type="date" id="date-invested" name="date_invested" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Investment</button>
                </form>
                
                <h2 class="mt-4">Investments Overview</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Crypto Name</th>
                            <th>Amount Invested ($)</th>
                            <th>Current Value ($)</th>
                            <th>Profit($)</th>
                            <th>Loss ($)</th>
                            <th>Total Balance ($)</th>
                            <th>Date Invested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investments as $investment) : ?>
                            <?php
                            $total_balance = $investment['current_value'] - $investment['amount_invested'];
                            $balance_class = $total_balance >= 0 ? 'bg-success text-white' : 'bg-danger text-white';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($investment['crypto_name']) ?></td>
                                <td><?= number_format($investment['amount_invested'], 2) ?></td>
                                <td><?= number_format($investment['current_value'], 2) ?></td>
                                <td><?= isset($investment['profit']) ? number_format(floatval($investment['profit']), 2) : 'N/A' ?></td>
                                <td><?= isset($investment['loss']) ? number_format(floatval($investment['loss']), 2) : 'N/A' ?></td>
                                <td class="<?= $balance_class ?>"><?= number_format($total_balance, 2) ?></td>
                                <td><?= htmlspecialchars($investment['date_invested']) ?></td>
                                <td>
                                    <a href="edit_investment.php?id=<?= $investment['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="delete_investment.php?id=<?= $investment['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
