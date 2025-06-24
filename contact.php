<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Cabinet d'Avocats</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #604B33;
            --primary-light: #8B7355;
            --background-color: #FAF9F4;
            --accent-color: #bc9f6a;
            --text-color: #333;
            --text-light: #666;
            --white: #ffffff;
            --shadow-light: rgba(96, 75, 51, 0.08);
            --shadow-medium: rgba(96, 75, 51, 0.15);
            --border-radius: 20px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #f0ede6 50%, #e8e3d8 100%);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -20%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(188, 159, 106, 0.03) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -50%;
            left: -20%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(96, 75, 51, 0.02) 0%, transparent 70%);
            animation: float 25s ease-in-out infinite reverse;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Header Styles */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 20px 30px;
            box-shadow: 0 8px 32px var(--shadow-medium);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 60px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
            transition: var(--transition);
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .logo-text {
            color: var(--white);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-links li a {
            color: #FAF9F4;
            text-decoration: none;
            font-weight: 600;
            padding: 14px 22px;
            border-radius: 12px;
            transition: var(--transition);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .nav-links li a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: var(--transition);
        }

        .nav-links li a:hover {
            background: rgba(250, 249, 244, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .nav-links li a:hover::before {
            left: 100%;
        }

        /* Main Content Container */
        .main-container {
            padding: 80px 40px;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        /* Contact Section */
        .contact-section {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 60px;
            box-shadow: 
                0 20px 60px var(--shadow-light),
                0 8px 25px var(--shadow-medium);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, 
                var(--primary-color) 0%, 
                var(--accent-color) 50%, 
                var(--primary-color) 100%);
        }

        .contact-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(188, 159, 106, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1) opacity(0.5); }
            50% { transform: scale(1.2) opacity(0.8); }
        }

        .contact-section:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 30px 80px var(--shadow-light),
                0 15px 35px var(--shadow-medium);
        }

        .contact-section h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 50px;
            font-size: 3.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            animation: fadeInDown 0.8s ease-out;
        }

        .contact-section h1::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        /* Contact Info Grid */
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .contact-item {
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            border-radius: 16px;
            padding: 30px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(96, 75, 51, 0.1);
            animation: fadeInUp 0.8s ease-out;
        }

        .contact-item:nth-child(1) { animation-delay: 0.1s; }
        .contact-item:nth-child(2) { animation-delay: 0.2s; }
        .contact-item:nth-child(3) { animation-delay: 0.3s; }
        .contact-item:nth-child(4) { animation-delay: 0.4s; }

        .contact-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(96, 75, 51, 0.02) 0%, rgba(188, 159, 106, 0.02) 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .contact-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px var(--shadow-medium);
            border-color: var(--accent-color);
        }

        .contact-item:hover::before {
            opacity: 1;
        }

        .contact-item-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 15px;
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(96, 75, 51, 0.3);
            transition: var(--transition);
        }

        .contact-item:hover .icon-wrapper {
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 12px 35px rgba(96, 75, 51, 0.4);
        }

        .contact-item-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .contact-item-content {
            color: var(--text-light);
            font-size: 1rem;
            line-height: 1.6;
            margin-left: 78px;
        }

        .contact-item-content strong {
            color: var(--text-color);
            font-weight: 600;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 40px 20px;
            }

            .contact-section {
                padding: 40px 30px;
            }

            .contact-section h1 {
                font-size: 2.4rem;
                margin-bottom: 40px;
            }

            .contact-info {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .contact-item {
                padding: 25px;
            }

            .contact-item-content {
                margin-left: 0;
                margin-top: 15px;
            }

            .contact-item-header {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }

            .navbar {
                flex-direction: column;
                gap: 20px;
                padding: 20px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 6px;
            }

            .nav-links li a {
                padding: 12px 18px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0;
            }

            .hero-section {
                padding: 15px 20px;
            }

            .contact-section {
                padding: 30px 20px;
                margin: 20px;
                border-radius: 16px;
            }

            .contact-section h1 {
                font-size: 2rem;
            }

            .contact-item {
                padding: 20px;
            }

            .icon-wrapper {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .nav-links li a {
                padding: 10px 14px;
                font-size: 11px;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus styles for accessibility */
        .nav-links a:focus {
            outline: 2px solid var(--accent-color);
            outline-offset: 2px;
        }

        /* Loading animation */
        .contact-item {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
</head>
<body>
    <header class="hero-section">
        <nav class="navbar">
            <div class="logo">
                <img src="lawyers imag/LOGO2-removebg-preview.png" alt="Law Firm Logo" />
            </div>
            <ul class="nav-links">
                <li><a href="home.php">HOME</a></li>
                <li><a href="about.php">About US</a></li>
                <li><a href="displayAvocat.php">LAWYERS</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="dashboard_avocat.php">Avocat</a></li>
                <li><a href="dashboard_client.php">Client</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <div class="contact-section">
            <h1>Contactez-nous</h1>
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-item-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="contact-item-title">Adresse</h3>
                    </div>
                    <div class="contact-item-content">
                        <strong>123 Rue des Avocats</strong><br>
                        Casablanca, Maroc<br>
                        <em>Proche du centre-ville</em>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 class="contact-item-title">Téléphone</h3>
                    </div>
                    <div class="contact-item-content">
                        <strong>+212 6 12 34 56 78</strong><br>
                        Disponible 24h/7j pour urgences juridiques
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="contact-item-title">Email</h3>
                    </div>
                    <div class="contact-item-content">
                        <strong>contact@plateforme-avocats.ma</strong><br>
                        Réponse garantie sous 24h
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="contact-item-title">Horaires</h3>
                    </div>
                    <div class="contact-item-content">
                        <strong>Lundi à Vendredi</strong><br>
                        9h - 17h<br>
                        <em>Consultations sur rendez-vous</em>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        // Observe all contact items
        document.querySelectorAll('.contact-item').forEach(item => {
            observer.observe(item);
        });

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add subtle parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.contact-section');
            const speed = scrolled * 0.05;
            
            if (parallax) {
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });

        // Add loading class removal after page load
        window.addEventListener('load', () => {
            document.body.classList.add('loaded');
        });
    </script>
</body>
</html>