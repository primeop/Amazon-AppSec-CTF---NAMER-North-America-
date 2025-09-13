<?php
function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$apiUrl = 'http://localhost:8000/dimensions';
$dimensions = fetchData($apiUrl);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Uber</title>
    <link href="css/bootstrap-5.3.3.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script>
        const risks = ["Low", "Medium", "High"];

        async function updatePrice() {
            const fromDimension = document.getElementById("from-dimension").value;
            const toDimension = document.getElementById("to-dimension").value;
            
            if (fromDimension && toDimension) {
                const formData = new FormData();
                formData.append("action", "price");
                formData.append("dim1", fromDimension);
                formData.append("dim2", toDimension);
                
                try {
                    const response = await fetch("conn.php", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.price) {
                        document.getElementById("total-price").textContent = data.price;
                    } else {
                        document.getElementById("total-price").textContent = "Error fetching price";
                    }

                    document.getElementById("distance").textContent = (Math.floor(Math.random() * 900) + 100) + " parsecs";
                    document.getElementById("risk-level").textContent = risks[Math.floor(Math.random() * risks.length)];

                } catch (error) {
                    document.getElementById("total-price").textContent = "Request failed";
                }
            }
        }

        async function bookRide() {
            const fromDimension = document.getElementById("from-dimension").value;
            const toDimension = document.getElementById("to-dimension").value;
            
            if (fromDimension && toDimension) {
                const formData = new FormData();
                formData.append("action", "book");
                formData.append("dim1", fromDimension);
                formData.append("dim2", toDimension);
                
                try {
                    const response = await fetch("conn.php", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.ride_id) {
                        alert("Ride booked successfully! Ride ID: " + data.ride_id);
                    } else {
                        alert("Error booking ride");
                    }
                } catch (error) {
                    alert("Request failed");
                }
            }
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">🚀 Space Uber</a>
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
        <div class="mb-3">
            <label class="form-label">From Dimension</label>
            <select class="form-select" id="from-dimension" onchange="updatePrice()">
                <?php foreach ($dimensions as $dimension) : ?>
                    <option value="<?= htmlspecialchars($dimension) ?>">
                        <?= htmlspecialchars($dimension) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">To Dimension</label>
            <select class="form-select" id="to-dimension" onchange="updatePrice()">
                <?php foreach ($dimensions as $dimension) : ?>
                    <option value="<?= htmlspecialchars($dimension) ?>">
                        <?= htmlspecialchars($dimension) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Transport Method</label>
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-custom">🚀 Spaceship</button>
                <button class="btn btn-secondary">🌀 Portal Gun</button>
            </div>
        </div>
        <div class="trip-details text-start">
            <p><strong>Distance:</strong> <span id="distance">0 parsecs</span></p>
            <p><strong>Risk Level:</strong> <span id="risk-level">Low</span></p>
            <p><strong>Transport:</strong> 🚀 Spaceship </p>
            <h4><strong>Total Price:</strong> <span id="total-price">0 Schmeckles</span></h4>
        </div>
        <button class="btn btn-custom w-100 mt-3" onclick="bookRide()">Book Interdimensional Ride</button>
    </div>
</body>
</html>
