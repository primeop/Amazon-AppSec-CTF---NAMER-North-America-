<?php
function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$fromDimension = $_GET['from-dimension'] ?? '';
$toDimension = $_GET['to-dimension'] ?? '';
$transport = $_GET['transport'] ?? 'Spaceship';

$from = urlencode($fromDimension);
$to = urlencode($toDimension);

date_default_timezone_set('UTC');
$distance = rand(100, 5000) . ' parsecs';
$riskLevels = ['Low', 'Medium', 'High'];
$riskLevel = $riskLevels[array_rand($riskLevels)];

$apiUrl = "http://localhost:8000/price/$from/$to";
$priceData = fetchData($apiUrl);
$price = $priceData['price'] ?? 'Unknown Schmeckles';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Confirmation - Space Uber</title>
    <link href="css/bootstrap-5.3.3.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">🚀 Space Uber</a>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h2>Trip Confirmation</h2>
        <p><strong>From:</strong> <?= htmlspecialchars($fromDimension) ?></p>
        <p><strong>To:</strong> <?= htmlspecialchars($toDimension) ?></p>
        <p><strong>Distance:</strong> <?= $distance ?></p>
        <p><strong>Risk Level:</strong> <?= $riskLevel ?></p>
        <p><strong>Transport:</strong> <?= htmlspecialchars($transport) ?></p>
        <h4><strong>Total Price:</strong> <?= $price ?></h4>
        <a href="index.php" class="btn btn-custom mt-3">Confirm & Return</a>
    </div>
</body>
</html>
