<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require '../includes/product.php';
$name = $_GET['name'] ?? '';
$orderBy = $_GET['order'] ?? 'name';
$products = searchProducts($name, removeSQL($orderBy));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interdimensional Products | Citadel of Ricks</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #0b0c10;
            font-family: 'Courier New', monospace;
            color: #62ff00;
            position: relative;
            overflow-x: hidden;
        }

        .portal-bg {
            position: fixed;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            border: 30px solid #97ce4c;
            animation: portal-spin 20s linear infinite;
            box-shadow: 0 0 20px #62ff00, 0 0 30px #62ff00, 0 0 40px #62ff00;
            opacity: 0.2;
            pointer-events: none;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        @keyframes portal-spin {
            from {
                transform: translate(-50%, -50%) rotate(0deg) scale(1);
                border-color: #97ce4c;
            }
            50% {
                transform: translate(-50%, -50%) rotate(180deg) scale(1.1);
                border-color: #62ff00;
            }
            to {
                transform: translate(-50%, -50%) rotate(360deg) scale(1);
                border-color: #97ce4c;
            }
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .search-container {
            background: rgba(36, 50, 95, 0.8);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #62ff00;
            box-shadow: 0 0 15px #62ff00;
            margin-bottom: 2rem;
            animation: container-glow 2s ease-in-out infinite;
        }

        @keyframes container-glow {
            0%, 100% { box-shadow: 0 0 15px #62ff00; }
            50% { box-shadow: 0 0 30px #62ff00; }
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(11, 12, 16, 0.7);
            border: 1px solid #62ff00;
            border-radius: 5px;
            color: #62ff00;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #97ce4c;
            box-shadow: 0 0 10px #62ff00;
            transform: translateY(-2px);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2362ff00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 40px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #97ce4c;
            color: #0b0c10;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background: #62ff00;
            box-shadow: 0 0 15px #62ff00;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        button.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        button.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: #0b0c10;
            border-radius: 50%;
            animation: button-loading 1s linear infinite;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes button-loading {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        .products-list {
            background: rgba(36, 50, 95, 0.8);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #62ff00;
            box-shadow: 0 0 15px #62ff00;
            animation: container-glow 2s ease-in-out infinite;
            animation-delay: 1s;
        }

        .product-item {
            padding: 1rem;
            border-bottom: 1px solid rgba(98, 255, 0, 0.3);
            transition: all 0.3s ease;
            animation: item-fade-in 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes item-fade-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-item:nth-child(1) { animation-delay: 0.1s; }
        .product-item:nth-child(2) { animation-delay: 0.2s; }
        .product-item:nth-child(3) { animation-delay: 0.3s; }
        .product-item:nth-child(4) { animation-delay: 0.4s; }
        .product-item:nth-child(5) { animation-delay: 0.5s; }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item:hover {
            background: rgba(98, 255, 0, 0.1);
            transform: translateX(10px);
        }

        .nav-links {
            text-align: center;
            margin-top: 2rem;
        }

        .nav-link {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background: rgba(36, 50, 95, 0.8);
            color: #62ff00;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #62ff00;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: #62ff00;
            color: #0b0c10;
            box-shadow: 0 0 15px #62ff00;
            transform: translateY(-2px);
        }

        h2 {
            text-align: center;
            color: #62ff00;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px #62ff00;
            animation: text-pulse 2s ease-in-out infinite;
        }

        @keyframes text-pulse {
            0%, 100% { text-shadow: 0 0 10px #62ff00; }
            50% { text-shadow: 0 0 20px #62ff00; }
        }

        .dimension-particles {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
        }



        .particle {
            position: absolute;
            bottom: 0;
            width: 4px;
            height: 4px;
            background: #62ff00;
            border-radius: 50%;
            animation: particle-float 3s linear infinite;
        }

        @keyframes particle-float {
            0% {
                transform: translateY(0) scale(0);
                opacity: 0;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-110vh) scale(1);
                opacity: 0;
            }
        }

    </style>
</head>
<body>
    <div class="dimension-particles">
        <?php for($i = 0; $i < 50; $i++): ?>
            <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 3000) ?>ms;"></div>
        <?php endfor; ?>
    </div>
    <div class="container">
        <h2>Interdimensional Inventory</h2>

        <div class="search-container">
            <form method="GET" onsubmit="this.querySelector('button').classList.add('loading')">
                <input type="text" name="name" placeholder="Search across dimensions..." value="<?= htmlspecialchars($name) ?>">
                <select name="order" onchange="this.form.submit()">
                    <option value="name" <?= $orderBy === 'name' ? 'selected' : '' ?>>Sort by Name</option>
                    <option value="size" <?= $orderBy === 'size' ? 'selected' : '' ?>>Sort by Size</option>
                </select>
                <button type="submit">Search Multiverse</button>
            </form>
        </div>

        <div class="products-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <?= htmlspecialchars($product['name']) ?> - <?= htmlspecialchars($product['size']) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="nav-links">
            <a href="idea.php" class="nav-link">Submit Invention</a>
            <a href="logout.php" class="nav-link">Close Portal</a>
        </div>
    </div>
    <script>
        // Add dynamic particles
        const particlesContainer = document.querySelector('.dimension-particles');
        setInterval(() => {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 3000 + 'ms';
            particlesContainer.appendChild(particle);
            setTimeout(() => particle.remove(), 3000);
        }, 300);

        // Form loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button');
            button.classList.add('loading');
            button.textContent = 'Scanning Multiverse...';
        });
    </script>
</body>
</html>
