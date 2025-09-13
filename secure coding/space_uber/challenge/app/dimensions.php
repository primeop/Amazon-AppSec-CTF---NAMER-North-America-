<?php
function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$apiBaseUrl = 'http://localhost:8000/dimensions';
$dimensions = fetchData($apiBaseUrl);
$dimensionData = null;

if (isset($_GET['dimension'])) {
    $dimensionName = urlencode($_GET['dimension']);
    $dimensionData = fetchData("$apiBaseUrl/$dimensionName");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dimensions - Space Uber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        body {
            background-color: #000;
            color: #33ff33;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
        }
        .container {
            max-width: 600px;
            background: #080808;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 20px #33ff33;
            border: 2px solid #33ff33;
        }
        .navbar {
            background: #111;
            box-shadow: 0 0 10px #33ff33;
        }
        .navbar-brand, .nav-link {
            color: #33ff33 !important;
            font-weight: bold;
        }
        .dimension-list {
            list-style: none;
            padding: 0;
        }
        .dimension-item {
            background: #111;
            border: 1px solid #33ff33;
            padding: 10px;
            margin: 5px 0;
            border-radius: 10px;
            box-shadow: 0 0 10px #33ff33;
        }
        a {
            color: #33ffcc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">🚀 Space Uber</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dimensions.php">Dimensions</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($dimensionData): ?>
            <h1><?= htmlspecialchars($dimensionData['name']) ?></h1>
            <p><strong>Status:</strong> <?= htmlspecialchars($dimensionData['status']) ?></p>
            <p><strong>Inhabitants:</strong> <?= htmlspecialchars($dimensionData['inhabitants']) ?></p>
            <a href="dimensions.php" class="btn btn-custom">Back to Dimensions</a>
        <?php else: ?>
            <h1>Available Dimensions</h1>
            <ul class="dimension-list">
                <?php foreach ($dimensions as $dimension) : ?>
                    <li class="dimension-item">
                        🌌 <a href="dimensions.php?dimension=<?= urlencode($dimension) ?>">
                            <?= htmlspecialchars($dimension) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
