<?php
// Define API URL
$apiUrl = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=15&page=1&sparkline=false";

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects if any
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');  // Set a user agent to mimic a browser

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    exit;
}

// Close cURL
curl_close($ch);

// Decode the JSON response
$cryptoData = json_decode($response, true);

// Error check
if (!$cryptoData) {
    echo "Error retrieving data.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Portfolio Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            max-width: 1200px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td.positive {
            color: green;
        }
        td.negative {
            color: red;
        }
        @media screen and (max-width: 768px) {
            table {
                font-size: 14px;
            }
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<h2>Crypto Portfolio Tracker</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Symbol</th>
            <th>Price</th>
            <th>24h High</th>
            <th>24h Low</th>
            <th>Market Cap</th>
            <th>Volume</th>
            <th>Change (24h)</th>
            <th>ROI</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cryptoData as $index => $crypto): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $crypto['name'] ?></td>
                <td><?= strtoupper($crypto['symbol']) ?></td>
                <td>$<?= number_format($crypto['current_price'], 2) ?></td>
                <td>$<?= number_format($crypto['high_24h'], 2) ?></td>
                <td>$<?= number_format($crypto['low_24h'], 2) ?></td>
                <td>$<?= number_format($crypto['market_cap'], 2) ?></td>
                <td>$<?= number_format($crypto['total_volume'], 2) ?></td>
                <td class="<?= $crypto['price_change_percentage_24h'] >= 0 ? 'positive' : 'negative' ?>">
                    <?= number_format($crypto['price_change_percentage_24h'], 2) ?>%
                </td>
                <td>1.000</td> <!-- Static ROI value, adjust as needed -->
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
