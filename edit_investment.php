<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "test_crypto");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the investment to edit
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM investments WHERE id = $id");
    $investment = mysqli_fetch_assoc($result);
    if (!$investment) {
        die("Investment not found.");
    }
}

// Handle form submission for updating the investment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $crypto_name = mysqli_real_escape_string($conn, $_POST['crypto_name']);
    $amount_invested = (float)mysqli_real_escape_string($conn, $_POST['amount_invested']);
    $current_value = (float)mysqli_real_escape_string($conn, $_POST['current_value']);
    $date_invested = mysqli_real_escape_string($conn, $_POST['date_invested']);
    $profit_loss = $current_value - $amount_invested;

    $stmt = $conn->prepare("UPDATE investments SET crypto_name = ?, amount_invested = ?, current_value = ?, profit_loss = ?, date_invested = ? WHERE id = ?");
    $stmt->bind_param("sdddsd", $crypto_name, $amount_invested, $current_value, $profit_loss, $date_invested, $id);

    if ($stmt->execute()) {
        echo "Investment updated successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Investment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Edit Investment</h1>
        </header>
        <main>
            <form action="edit_investment.php" method="post">
                <input type="hidden" name="id" value="<?= $investment['id'] ?>">
                <div class="form-group">
                    <label for="crypto-name">Crypto Name:</label>
                    <input type="text" id="crypto-name" name="crypto_name" value="<?= htmlspecialchars($investment['crypto_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="amount-invested">Amount Invested ($):</label>
                    <input type="number" id="amount-invested" name="amount_invested" value="<?= $investment['amount_invested'] ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="current-value">Current Value ($):</label>
                    <input type="number" id="current-value" name="current_value" value="<?= $investment['current_value'] ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="date-invested">Date Invested:</label>
                    <input type="date" id="date-invested" name="date_invested" value="<?= $investment['date_invested'] ?>" required>
                </div>
                <button type="submit" class="submit-btn">Update Investment</button>
            </form>
        </main>
    </div>
</body>
</html>
