<?php 
session_start();
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Identifiant invalide.";
    exit;
}

$id_avocat = (int) $_GET['id'];

$sql = "SELECT a.*, s.nom_specialite 
        FROM avocat a
        LEFT JOIN specialite s ON a.id_Specialite = s.id_Specialite
        WHERE a.id_avocat = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_avocat]);
$avocat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avocat) {
    echo "Avocat non trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Profil de Maître <?= htmlspecialchars($avocat['prenom'] . ' ' . $avocat['nom']) ?></title>
    <style>
   
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        
        body {
            font-family: 'Georgia', serif;
            background: #FAF9F4;
            color: #604B33;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

       
        .hero-section {
            background: #604B33;
            padding: 15px 30px;
            box-shadow: 0 2px 20px rgba(96, 75, 51, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .logo img {
            height: 60px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        .nav-links li a {
            color: #FAF9F4;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links li a:hover {
            background: rgba(250, 249, 244, 0.15);
            transform: translateY(-2px);
        }

        
        .profile-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 10px 30px rgba(96, 75, 51, 0.1),
                0 1px 8px rgba(96, 75, 51, 0.05);
            border: 1px solid rgba(96, 75, 51, 0.1);
            display: flex;
            gap: 40px;
            max-width: 900px;
            width: 100%;
            margin: 40px auto;
            padding: 40px;
        }

        
        .profile-photo {
            width: 250px;
            height: 250px;
            border-radius: 15px;
            border: 4px solid #FAF9F4;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background: #FAF9F4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #604B33;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .profile-photo:hover {
            transform: scale(1.05);
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            border-radius: 15px;
            object-fit: cover;
        }

       
        .profile-info {
            flex: 1;
        }

        .profile-info h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #604B33;
        }

        .info-row {
            margin-bottom: 10px;
            font-size: 1.1rem;
            line-height: 1.4;
            color: #604B33;
        }

        .info-label {
            font-weight: 700;
            color: #7a6142;
            display: inline-block;
            min-width: 110px;
            text-transform: none;
            letter-spacing: normal;
        }

        
        .info-row:last-child {
            margin-top: 20px;
            line-height: 1.6;
            text-align: justify;
        }

       
        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 14px 30px;
            background-color: #604B33;
            color: #FAF9F4;
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .back-link:hover {
            background-color: #4a3b28;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(96, 75, 51, 0.3);
        }

        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                padding: 25px 20px;
            }
            .profile-photo {
                width: 180px;
                height: 180px;
                margin: 0 auto 25px auto;
            }
            .profile-info h1 {
                font-size: 2.2rem;
                text-align: center;
            }
            .info-row {
                font-size: 1rem;
                margin-bottom: 12px;
            }
            .info-label {
                min-width: 100px;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
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

<div class="profile-container">
    <?php if (!empty($avocat['photo'])): ?>
        <img src="<?= htmlspecialchars($avocat['photo']) ?>" alt="Photo de Maître <?= htmlspecialchars($avocat['prenom']) ?>" class="profile-photo" />
    <?php else: ?>
        <div class="profile-photo" style="display:flex;align-items:center;justify-content:center;color:#999;">Pas de photo</div>
    <?php endif; ?>

    <div class="profile-info">
        <h1>Maître <?= htmlspecialchars($avocat['prenom'] . ' ' . $avocat['nom']) ?></h1>

        <div class="info-row"><span class="info-label">Adresse :</span> <?= htmlspecialchars($avocat['adresse'] ?? 'Non renseignée') ?></div>
        <div class="info-row"><span class="info-label">Téléphone :</span> <?= htmlspecialchars($avocat['telephone'] ?? 'Non renseigné') ?></div>
        <div class="info-row"><span class="info-label">Email :</span> <?= htmlspecialchars($avocat['email'] ?? 'Non renseigné') ?></div>
        <div class="info-row"><span class="info-label">Expérience :</span> <?= htmlspecialchars($avocat['annees_experience'] ?? 'N/A') ?> ans</div>
        <div class="info-row"><span class="info-label">Langues :</span> <?= htmlspecialchars($avocat['langues'] ?? 'Non renseignées') ?></div>
        <div class="info-row"><span class="info-label">Diplôme :</span> <?= htmlspecialchars($avocat['diplome'] ?? 'Non renseigné') ?></div>
        <div class="info-row"><span class="info-label">Spécialité :</span> <?= htmlspecialchars($avocat['nom_specialite'] ?? 'Non spécifiée') ?></div>
        <div class="info-row"><span class="info-label">Biographie :</span> <?= nl2br(htmlspecialchars($avocat['biographie'] ?? 'Pas de biographie')) ?></div>

        <a href="javascript:history.back()" class="back-link">← Retour</a>
    </div>
</div>

</body>
</html>
