<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 1s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            z-index: 1;
        }

        .title {
            font-size: 8rem;
            font-weight: bold;
            background: linear-gradient(45deg, #6b3de0 0%, #9c44dc 50%, #6b3de0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shine 2s linear infinite;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(156, 68, 220, 0.3);
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .subtitle {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }

        .message {
            color: #ccc;
            font-size: 1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #6b3de0, #9c44dc);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(107, 61, 224, 0.4);
        }

        @media (max-width: 768px) {
            .title {
                font-size: 5rem;
            }

            .subtitle {
                font-size: 1.2rem;
            }

            .message {
                font-size: 0.9rem;
            }

            .button {
                padding: 0.8rem 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 3rem;
            }

            .container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
<div class="stars"></div>
<div class="container">
    <h1 class="title">Oops!</h1>
    <h2 class="subtitle">404 - Page Not Found</h2>
    <p class="message">The page you are looking for might have been removed, had its name changed, or is
        temporarily unavailable.</p>
    <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">GO
        TO HOMEPAGE</a>
</div>

<script>
    // Create stars
    function createStars() {
        const stars = document.querySelector('.stars');
        const numStars = 200;

        for (let i = 0; i < numStars; i++) {
            const star = document.createElement('div');
            star.className = 'star';

            // Random position
            const x = Math.random() * 100;
            const y = Math.random() * 100;

            // Random size
            const size = Math.random() * 3;

            star.style.left = `${x}%`;
            star.style.top = `${y}%`;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;

            // Random animation delay
            star.style.animationDelay = `${Math.random() * 1}s`;

            stars.appendChild(star);
        }
    }

    createStars();
</script>
</body>

</html>
