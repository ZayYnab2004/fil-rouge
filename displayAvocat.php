<?php  
session_start();
require 'config.php';



$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$language = isset($_POST['language']) ? trim($_POST['language']) : '';
$specialty = isset($_POST['specialty']) ? trim($_POST['specialty']) : '';

$langMap = [
    'Français' => ['français', 'fr', 'french'],
    'Anglais'  => ['anglais', 'en', 'english'],
    'Espagnol' => ['espagnol', 'es', 'spanish'],
    'Arabic'   => ['arabic', 'ar', 'arabe']
];

// Get list of specialties
$stmtSpecs = $pdo->query("SELECT id_Specialite, nom_specialite FROM specialite");
$specialtiesList = $stmtSpecs->fetchAll(PDO::FETCH_ASSOC);

// Build query
$query = "SELECT a.*, s.nom_specialite 
          FROM avocat a 
          LEFT JOIN specialite s ON a.id_Specialite = s.id_Specialite 
          WHERE 1=1";
$params = [];

if ($city) {
    $query .= " AND a.adresse LIKE ?";
    $params[] = "%$city%";
}

if ($language && isset($langMap[$language])) {
    $langConditions = [];
    foreach ($langMap[$language] as $langSynonym) {
        $langConditions[] = "LOWER(a.langues) LIKE ?";
        $params[] = "%" . strtolower($langSynonym) . "%";
    }
    $query .= " AND (" . implode(" OR ", $langConditions) . ")";
}

if ($specialty) {
    $query .= " AND s.nom_specialite LIKE ?";
    $params[] = "%$specialty%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher un Avocat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF9F4;
            margin: 0;
            padding: 0;
            color: #222;
        }

        .header {
            position: relative;
            text-align: center;
        }

        .header-image {
            width: 100%;
            height: auto;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 20px;
            width: 90px;
        }

        .nav {
            position: absolute;
            top: 15px;
            right: 20px;
            display: flex;
            gap: 15px;
        }

        .nav a {
            color: #fff;
           
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
        }

        .nav a:hover {
            background: #4a3a28;
        }

        .intro-section {
            background: #fff;
            padding: 40px 20px;
            text-align: center;
            max-width: 900px;
            margin: 30px auto 10px;
            border-radius: 10px;
            border-left: 6px solid #604B33;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .intro-section h3 {
            color: #604B33;
            font-size: 2em;
            margin-bottom: 15px;
        }

        .filter-form {
            background-color: #fff;
            padding: 25px;
            margin: 30px auto;
            max-width: 1000px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid #e0dcd4;
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            align-items: flex-end;
            justify-content: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            width: 220px;
        }

        .filter-group label {
            font-weight: 600;
            color: #604B33;
            margin-bottom: 6px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            background-color: #FAF9F4;
        }

        .filter-group button {
            height: 44px;
            padding: 0 24px;
            background-color: #604B33;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .filter-group button:hover {
            background-color: #4a3a28;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .card {
            background: #fff;
            border: 1px solid #e0dcd4;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 20px;
            width: 280px;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .card h3 {
            color: #604B33;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .card p {
            color: #444;
            margin: 5px 0;
            font-size: 0.95em;
        }

        .card p strong {
            color: #bc9f6a;
        }

        .buttons {
            margin-top: 12px;
        }

        .buttons a {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 5px;
            color: #fff;
            font-weight: 600;
            font-size: 0.9em;
            margin: 5px 4px;
            text-decoration: none;
        }

        .buttons a:first-child {
            background: #604B33;
        }

        .buttons a.contact {
            background: #28a745;
        }

        .no-results {
            text-align: center;
            color: #777;
            font-style: italic;
            margin: 30px;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <img src="lawyers imag/image webiste lawyers.jpg" alt="Header" class="header-image">
    <img src="lawyers imag/LOGO2-removebg-preview.png" alt="Logo" class="logo">
    <div class="nav">
        <a href="home.php">Home</a>
        <a href="displayAvocat.php">Lawyers</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="dashboard_avocat.php">Avocat</a>
                   <a href="dashboard_client.php">Client</a>
    </div>
</div>

<div class="intro-section">
    <h3>Recherchez l'expertise juridique dont vous avez besoin</h3>
    <p>Utilisez notre outil de recherche avancée pour trouver l'avocat qui correspond exactement à vos besoins spécifiques.</p>
</div>

<form class="filter-form" method="POST">
    <div class="filter-group">
        <label for="city">Ville :</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($city) ?>" placeholder="ex. Casablanca">
    </div>

    <div class="filter-group">
        <label for="language">Langue :</label>
        <select id="language" name="language">
            <option value="">Toutes</option>
            <option value="Français" <?= $language === 'Français' ? 'selected' : '' ?>>Français</option>
            <option value="Anglais" <?= $language === 'Anglais' ? 'selected' : '' ?>>Anglais</option>
            <option value="Espagnol" <?= $language === 'Espagnol' ? 'selected' : '' ?>>Espagnol</option>
            <option value="Arabic" <?= $language === 'Arabic' ? 'selected' : '' ?>>Arabe</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="specialty">Spécialité :</label>
        <select id="specialty" name="specialty">
            <option value="">Toutes</option>
            <?php foreach ($specialtiesList as $spec): ?>
                <option value="<?= htmlspecialchars($spec['nom_specialite']) ?>" 
                    <?= $specialty === $spec['nom_specialite'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($spec['nom_specialite']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <button type="submit">🔎 Rechercher</button>
    </div>
</form>

<?php if (count($rows) > 0): ?>
    <div class="card-container">
        <?php foreach ($rows as $row): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($row['photo'] ?: 'default.jpg') ?>" alt="Photo de l'avocat">
                <h3><?= htmlspecialchars($row['nom']) . ' ' . htmlspecialchars($row['prenom']) ?></h3>
                <p><strong>Adresse:</strong> <?= htmlspecialchars($row['adresse']) ?></p>
                <p><strong>Téléphone:</strong> <?= htmlspecialchars($row['telephone']) ?></p>
                <p><strong>Langues:</strong> <?= htmlspecialchars($row['langues']) ?></p>
                <p><strong>Spécialité:</strong> <?= htmlspecialchars($row['nom_specialite']) ?></p>
                <div class="buttons">
                    <a href="profilAvocat.php?id=<?= urlencode($row['id_avocat']) ?>">Voir le profil</a>
                    <a href="<?= isset($_SESSION['client_id']) ? 'contacterAvocat.php?id=' . urlencode($row['id_avocat']) : 'loginClient.php'; ?>" class="contact">Contacter</a>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-results">Aucun avocat trouvé.</p>
<?php endif; ?>

</body>
</html>
