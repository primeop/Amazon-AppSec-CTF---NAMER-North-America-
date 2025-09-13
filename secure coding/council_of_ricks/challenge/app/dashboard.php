<?php
include 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$stmt = $pdo->query("SELECT rick_id, rating, description, created_at FROM ricks");
$ricks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Council of Ricks</title>
    <link href="css/bootstrap-5.3.3.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111827;
            color: #d1d5db;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #1f2937;
            padding: 15px;
        }
        .navbar-brand {
            color: #2dd4bf;
            font-weight: bold;
        }
        .btn-teal {
            background-color: #2dd4bf;
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-teal:hover {
            background-color: #0f766e;
        }
        .btn-gray {
            background-color: #374151;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-gray:hover {
            background-color: #4b5563;
        }
        .rick-card {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: white;
            border-radius: 8px;
            padding: 15px;
        }
        .rick-card:hover {
            border-color: #2dd4bf;
        }
        .star {
            color: #d1d5db;
        }
        .star-filled {
            color: #facc15;
        }
        .muted-text {
            color: #999999
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Council of Ricks Database</a>
            <div class="d-flex">
                <a href="add-rick.php" class="btn btn-teal me-2">+ Add New Rick</a>
                <a href="/logout.php" class="btn btn-gray">&#x21B3; Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <?php foreach ($ricks as $rick) : ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="rick-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <h2> <?= htmlspecialchars($rick['rick_id']) ?> </h2>
                            <div>
                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                    <span class="star <?= $i < $rick['rating'] ? 'star-filled' : '' ?>">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="mt-3"> <?= htmlspecialchars($rick['description']) ?> </p>
                        <small class="muted-text">Added: <?= htmlspecialchars($rick['created_at']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
