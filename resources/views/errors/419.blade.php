<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #c31432 0%, #240b36 100%);
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
            0%, 100% {
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
            color: #fff;
            margin-bottom: 1rem;
        }

        .subtitle {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 2rem;
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
            background: #f85032; /* Fallback for older browsers */
            background: -webkit-linear-gradient(to right, #e73827, #f85032); /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #e73827, #f85032); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
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
            box-shadow: 0 5px 15px rgba(233, 32, 32, 0.4);
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
    <h2 class="subtitle">419 - Page Expired</h2>
    <p class="message">The page has expired due to inactivity. Please refresh the page and try again.</p>
    <a href="{{ request()->is('admin*') ? route('admin.dashboard') : route('store.dashboard') }}" class="button">GO TO HOMEPAGE</a>
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
