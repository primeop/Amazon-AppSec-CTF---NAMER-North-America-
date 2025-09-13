<?php
// TODO move API to not be in SMB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$action = $_POST['action'] ?? '';
$dim1 = urlencode($_POST['dim1'] ?? '');
$dim2 = urlencode($_POST['dim2'] ?? '');

if (!$action || !$dim1 || !$dim2) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

// SSRF Protection: Block free_ride action to prevent persistence
if ($action === 'free_ride') {
    echo json_encode(["error" => "Free ride action is not allowed"]);
    exit;
}

$apiBaseUrl = 'http://localhost:8000';
$apiUrl = "$apiBaseUrl/$action/$dim1/$dim2";

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$response = fetchData($apiUrl);
echo json_encode($response);
?>
