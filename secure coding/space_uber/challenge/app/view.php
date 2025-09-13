<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['file'])) {
    echo "Invalid request.";
    exit();
}

$fileName = $_GET['file'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Viewer</title>
    <link rel="stylesheet" href="css/bootstrap-5.3.3.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <p class="text-monospace">
                <?php
                try {
                    echo nl2br(file_get_contents($fileName));
                } catch (Exception $e) {
                    echo "Unable to read file";
                }
                ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>
