<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawPostData = file_get_contents('php://input');
    
    // Disable external entity loading to prevent XXE attacks
    $oldValue = libxml_disable_entity_loader(true);
    
    // Parse XML with secure flags - NO external entity substitution
    $xml = simplexml_load_string($rawPostData, null, LIBXML_DTDLOAD | LIBXML_DTDATTR);
    
    // Re-enable entity loader
    libxml_disable_entity_loader($oldValue);
    
    // Check if XML parsing was successful
    if ($xml === false) {
        die("Invalid XML format.");
    }
    
    $rick_id = (string) $xml->rick_id ?? '';
    $rating = (string) $xml->rating ?? '';
    $description = (string) $xml->description ?? '';
    
    // Validate input data
    if (empty($rick_id) || empty($description) || empty($rating)) {
        die("All fields are required.");
    }
    
    // Sanitize input to prevent XSS
    $rick_id = htmlspecialchars(trim($rick_id), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($description), ENT_QUOTES, 'UTF-8');
    $rating = (int) $rating;
    
    // Validate rating range
    if ($rating < 1 || $rating > 5) {
        die("Rating must be between 1 and 5.");
    }

    $insertQuery = "INSERT INTO ricks (rick_id, rating, description) VALUES (:rick_id, :rating, :description)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':rick_id', $rick_id, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        die("Failed to add Rick to database!");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Rick - Council of Ricks</title>
    <link href="css/bootstrap-5.3.3.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&#x2190; Back to Dashboard</a>
        <div class="card">
            <h1 class="heading mb-4">Register New Rick</h1>
            
            <div id="success_msg" class="alert alert-success" style="display: none;">Rick successfully registered!</div>

            <form method="POST" onsubmit="convertToXml(event, this)">
                <div class="mb-3">
                    <label class="form-label">Rick Identifier</label>
                    <input type="text" name="rick_id" class="form-control" required placeholder="e.g., Rick C-137">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control" required placeholder="Describe this Rick's characteristics..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating (1-5)</label>
                    <input type="range" name="rating" min="1" max="5" class="form-range">
                    <div class="rating-labels">
                        <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-teal w-100">Register Rick</button>
            </form>
        </div>
    </div>
    <script>
        function convertToXml(event, form) {
            event.preventDefault();

            const rick_id = form.rick_id.value;
            const description = form.description.value;
            const rating = form.rating.value;

            const xml = `<rick><rick_id>${rick_id}</rick_id><rating>${rating}</rating><description>${description}</description></rick>`;

            console.log(xml);

            fetch("add-rick.php", {
                method: "POST",
                headers: { "Content-Type": "application/xml" },
                body: xml,
            })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text();
                    }
                })
                .then(_ => {
                    let succ = document.getElementById("success_msg");
                    succ.style.display = "block";
                })
                .catch(error => console.error("Error:", error));
        }
    </script>
</body>
</html>
