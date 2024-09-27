<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    die("Access denied.");
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
    <!-- Sidebar with Glass Effect -->
    <div class="d-flex">
        <div id="sidebar" class="sidebar glass-effect p-3">
            <h2 class="">Crypto Dashboard</h2>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php"><i class="fa fa-home"></i> Dashboard</a>
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
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php"><i class="fa fa-user-circle"></i> Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

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
                            <th>Date Invested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investments as $investment) : ?>
                            <tr>
                                <td><?= htmlspecialchars($investment['crypto_name']) ?></td>
                                <td><?= number_format($investment['amount_invested'], 2) ?></td>
                                <td><?= number_format($investment['current_value'], 2) ?></td>
                                <td><?= isset($investment['profit']) ? number_format(floatval($investment['profit']), 2) : 'N/A' ?></td>
                                <td><?= isset($investment['loss']) ? number_format(floatval($investment['loss']), 2) : 'N/A' ?></td>
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
