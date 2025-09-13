<?php

header('Content-Type: application/json');

$dimensions = [
    "C-137" => ["name" => "Dimension C-137", "status" => "Destroyed", "inhabitants" => "Few survivors"],
    "Froopyland" => ["name" => "Froopyland", "status" => "Artificial", "inhabitants" => "Mutant creatures"],
    "Cronenberg" => ["name" => "Cronenberg Dimension", "status" => "Overrun", "inhabitants" => "Cronenbergs"],
    "Fantasy" => ["name" => "Fantasy Dimension", "status" => "Thriving", "inhabitants" => "Fantasy beings"],
    "Evil Morty" => ["name" => "Evil Morty's Universe", "status" => "Oppressive", "inhabitants" => "Evil versions"],
    "Microverse" => ["name" => "Microverse", "status" => "Operational", "inhabitants" => "Micro-citizens"],
    "Tinyverse" => ["name" => "Tinyverse", "status" => "Subservient", "inhabitants" => "Tiny beings"],
    "Miniverse" => ["name" => "Miniverse", "status" => "Unaware", "inhabitants" => "Miniverse dwellers"],
    "Pizza Universe" => ["name" => "Pizza Universe", "status" => "Tasty", "inhabitants" => "Talking pizzas"],
    "Phone Universe" => ["name" => "Phone Universe", "status" => "Loud", "inhabitants" => "Sentient phones"],
    "Chair Universe" => ["name" => "Chair Universe", "status" => "Stable", "inhabitants" => "Living chairs"],
    "Reverse Dimension" => ["name" => "Reverse Dimension", "status" => "Inverted", "inhabitants" => "Reversed beings"]
];

function calculatePrice($dim1, $dim2) {
    return rand(100, 5000) . " Schmeckles";
}

$ridesFile = 'booked_rides.json';

if (file_exists($ridesFile)) {
    $bookedRides = json_decode(file_get_contents($ridesFile), true);
} else {
    $bookedRides = [];
}

$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$dim1 = urldecode($uri[1] ?? '');
$dim2 = urldecode($uri[2] ?? '');

if ($uri[0] === "dimensions") {
    if (count($uri) == 1) {
        echo json_encode(array_keys($dimensions));
    } elseif (count($uri) == 2 && isset($dimensions[$dim1])) {
        echo json_encode($dimensions[$dim1]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Dimension " . $uri[1] . " not found"]);
    }
} elseif ($uri[0] === "price" && count($uri) == 3) {
    if (isset($dimensions[$dim1]) && isset($dimensions[$dim2])) {
        echo json_encode(["from" => $dim1, "to" => $dim2, "price" => calculatePrice($dim1, $dim2)]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "One or both dimensions not found"]);
    }
} elseif ($uri[0] === "book" && count($uri) == 3) {
    if (isset($dimensions[$dim1]) && isset($dimensions[$dim2])) {
        $price = calculatePrice($dim1, $dim2);
        $rideId = uniqid("ride_");
        $bookedRides[$rideId] = [
            "from" => $dim1,
            "to" => $dim2,
            "price" => $price
        ];
        file_put_contents($ridesFile, json_encode($bookedRides));

        echo json_encode(["ride_id" => $rideId, "from" => $dim1, "to" => $dim2, "price" => $price]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "One or both dimensions not found"]);
    }
} elseif ($uri[0] === "free_ride" && count($uri) == 3) {
    if (isset($dimensions[$dim1]) && isset($dimensions[$dim2])) {
        $rideId = uniqid("ride_");
        $bookedRides[$rideId] = [
            "from" => $dim1,
            "to" => $dim2,
            "price" => 0
        ];
        file_put_contents($ridesFile, json_encode($bookedRides));

        echo json_encode(["ride_id" => $rideId, "from" => $dim1, "to" => $dim2, "price" => $price]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "One or both dimensions not found"]);
    }
} elseif ($uri[0] === "rides") {
    echo json_encode($bookedRides);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid endpoint"]);
}

?>