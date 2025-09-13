<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require '../includes/product.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (uploadFile($_FILES['file'])) {
        $message = 'Your invention has been cataloged in the Council archives!';
    } else {
        $error = 'Aw geez! That file seems to be from a corrupted dimension!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Invention | Citadel of Ricks</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #0b0c10;
            font-family: 'Courier New', monospace;
            color: #62ff00;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
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
        }

        @keyframes portal-spin {
            from {
                transform: rotate(0deg) scale(1);
                border-color: #97ce4c;
            }
            50% {
                transform: rotate(180deg) scale(1.1);
                border-color: #62ff00;
            }
            to {
                transform: rotate(360deg) scale(1);
                border-color: #97ce4c;
            }
        }

        .form-container {
            background: rgba(36, 50, 95, 0.8);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid #62ff00;
            box-shadow: 0 0 15px #62ff00;
            width: 300px;
            position: relative;
            z-index: 1;
            animation: container-glow 2s ease-in-out infinite;
        }

        @keyframes container-glow {
            0%, 100% { box-shadow: 0 0 15px #62ff00; }
            50% { box-shadow: 0 0 30px #62ff00; }
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

        .file-input-container {
            position: relative;
            margin: 20px 0;
        }

        input[type="file"] {
            width: 100%;
            padding: 12px;
            background: rgba(11, 12, 16, 0.7);
            border: 1px solid #62ff00;
            border-radius: 5px;
            color: #62ff00;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
        }

        input[type="file"]:focus {
            outline: none;
            border-color: #97ce4c;
            box-shadow: 0 0 10px #62ff00;
            transform: translateY(-2px);
        }

        input[type="file"]::file-selector-button {
            background: #97ce4c;
            color: #0b0c10;
            padding: 8px 16px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        input[type="file"]::file-selector-button:hover {
            background: #62ff00;
            box-shadow: 0 0 15px #62ff00;
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

        .message {
            color: #62ff00;
            text-align: center;
            margin-top: 10px;
            animation: message-fade 0.3s ease-in;
        }

        .error {
            color: #ff0033;
            text-align: center;
            margin-top: 10px;
            animation: error-fade 0.3s ease-in;
        }

        @keyframes message-fade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes error-fade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-links {
            text-align: center;
            margin-top: 20px;
        }

        .nav-link {
            display: inline-block;
            padding: 8px 16px;
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

        .dimension-particles {
            position: fixed;
            left: 0;
            width: 100%;
            height: 105vh; /* ensures it covers the entire viewport height */
            pointer-events: none;
            z-index: 0;
        }


        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
            animation: particle-float 3s linear infinite;
        }

        @keyframes particle-float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100px) scale(1);
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
    <div class="form-container">
        <h2>Submit Invention</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="this.querySelector('button').classList.add('loading')">
            <div class="file-input-container">
                <input type="file" name="file" required>
            </div>
            <button type="submit">Upload to Council</button>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (isset($message)): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </form>
        <div class="nav-links">
            <a href="products.php" class="nav-link">Inventory</a>
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
            button.textContent = 'Transmitting to Council...';
        });
    </script>
</body>
</html>
