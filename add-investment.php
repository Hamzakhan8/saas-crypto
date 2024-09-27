<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!-- Simple front view to insert data -->


<?php
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = mysqli_connect("localhost", "root", "", "test_crypto");
    
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Escape user input to prevent SQL injection
    $crypto_name = mysqli_real_escape_string($conn, $_POST['crypto_name']);
    $amount_invested = (float) mysqli_real_escape_string($conn, $_POST['amount_invested']);
    $current_value = (float) mysqli_real_escape_string($conn, $_POST['current_value']);
    $profit_loss = $current_value - $amount_invested;

    // Use prepared statements for safer SQL execution
    $stmt = $conn->prepare("INSERT INTO investments (crypto_name, amount_invested, current_value, profit_loss) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddd", $crypto_name, $amount_invested, $current_value, $profit_loss);

    if ($stmt->execute()) {
        // If successful, redirect to a success page or the same page
        header("Location: add-investment.php"); // Adjust the redirection as needed
        exit();
    } else {
        // Added error reporting
        echo "Error: " . $stmt->error; // Display the error
    }

    // Close statement and connection
    $stmt->close();
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crypto Investment Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Added Font Awesome CDN -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        input[type="text"], input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crypto Investment Dashboard</h2>
        <div class="form-group">
            <h3>Insert Data</h3>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <label for="crypto_name">Crypto Name:</label>
                <input type="text" id="crypto_name" name="crypto_name"><br><br>
                <label for="amount_invested">Amount Invested:</label>
                <input type="number" id="amount_invested" name="amount_invested"><br><br>
                <label for="current_value">Current Value:</label>
                <input type="number" id="current_value" name="current_value"><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
        <div class="form-group">
            <h3>View Data</h3>
            <table>
                <thead>
                    <tr>
                        <th>Crypto Name</th>
                        <th>Amount Invested</th>
                        <th>Current Value</th>
                        <th>Profit/Loss</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "test_crypto");
                    $query = "SELECT crypto_name, amount_invested, current_value, profit_loss FROM investments";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['crypto_name'] . "</td>";
                            echo "<td>" . $row['amount_invested'] . "</td>";
                            echo "<td>" . $row['current_value'] . "</td>";
                            echo "<td>" . $row['profit_loss'] . "</td>";
                            echo "<td><a href='javascript:void(0);' onclick='openUpdateModal(\"" . $row['crypto_name'] . "\", \"" . $row['amount_invested'] . "\", \"" . $row['current_value'] . "\");'><i class='fas fa-edit'></i></a></td>";
                            echo "<td><a href='?action=delete&id=" . $row['crypto_name'] . "' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fas fa-trash'></i></a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data found</td></tr>";
                    }
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <h3>Update Data</h3>
            <button id="updateButton">Update</button>
        </div>
        <div id="updateModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="update_crypto_name" name="update_crypto_name"><!-- Hidden input for crypto name -->
                    <label for="update_amount_invested">Amount Invested:</label>
                    <input type="number" id="update_amount_invested" name="update_amount_invested"><br><br>
                    <label for="update_current_value">Current Value:</label>
                    <input type="number" id="update_current_value" name="update_current_value"><br><br>
                    <input type="submit" value="Update">
                </form>
            </div>
        </div>
        <script>
            var updateModal = document.getElementById('updateModal');
            var updateButton = document.getElementById('updateButton');
            var span = document.getElementsByClassName("close")[0];
            updateButton.onclick = function() {
                updateModal.style.display = "block";
            }
            span.onclick = function() {
                updateModal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == updateModal) {
                    updateModal.style.display = "none";
                }
            }
        </script>
        <?php
        if (isset($_POST['update_crypto_name']) && isset($_POST['update_amount_invested']) && isset($_POST['update_current_value'])) {
            // Reconnect to the database
            $conn = mysqli_connect("localhost", "root", "", "test_crypto");
            
            $update_crypto_name = mysqli_real_escape_string($conn, $_POST['update_crypto_name']);
            $update_amount_invested = mysqli_real_escape_string($conn, $_POST['update_amount_invested']);
            $update_current_value = mysqli_real_escape_string($conn, $_POST['update_current_value']);

            $update_query = "UPDATE investments SET amount_invested = '$update_amount_invested', current_value = '$update_current_value' WHERE crypto_name = '$update_crypto_name'";
            $update_result = mysqli_query($conn, $update_query);

            if ($update_result) {
                echo "Data updated successfully.";
            } else {
                echo "Error updating data: " . mysqli_error($conn);
            }
        }
        ?>
    </div>
</body>
</html>
