<?php
session_start();
require 'config.php';


$errors = [];
$success = '';

if (!isset($_SESSION['client_id'])) {
    die("Vous devez être connecté en tant que client pour prendre un rendez-vous.");
}

$id_client = $_SESSION['client_id'];
$id_avocat = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_avocat <= 0) {
    die("Avocat invalide.");
}


$stmt = $pdo->prepare("SELECT prenom, nom FROM avocat WHERE id_avocat = ?");
$stmt->execute([$id_avocat]);
$avocat = $stmt->fetch();
if (!$avocat) {
    die("Avocat introuvable.");
}
$avocat_nom = $avocat['prenom'] . ' ' . $avocat['nom'];


$allowed_hours = [
    "09:30", "10:00", "10:30", "11:00", "11:30",
    "12:00", "12:30", "13:00", "13:30", "14:00",
    "14:30", "15:00", "15:30", "16:00", "16:30"
];


function isWeekend($date) {
    $dayNum = date('N', strtotime($date));
    return $dayNum >= 6; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sujet = trim($_POST['sujet'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date_rdv = $_POST['date_rdv'] ?? '';
    $heure_rdv = $_POST['heure_rdv'] ?? '';

    
    if (!$sujet || !$description || !$date_rdv || !$heure_rdv) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    
    if ($date_rdv && isWeekend($date_rdv)) {
        $errors[] = "Les rendez-vous sont disponibles uniquement du lundi au vendredi.";
    }

    
    if ($heure_rdv && !in_array($heure_rdv, $allowed_hours)) {
        $errors[] = "L'heure doit être comprise entre 09:30 et 16:30 (toutes les demi-heures).";
    }

    
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM rendezvous WHERE id_avocat = ? AND date_rdv = ? AND heure_rdv = ?");
        $stmtCheck->execute([$id_avocat, $date_rdv, $heure_rdv]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = "Ce créneau est déjà réservé. Veuillez choisir une autre date ou heure.";
        }
    }

    // Insert if no errors
    if (empty($errors)) {
        $stmtInsert = $pdo->prepare("INSERT INTO rendezvous (id_client, id_avocat, sujet, description, date_rdv, heure_rdv, statut) VALUES (?, ?, ?, ?, ?, ?, 'en attente')");
        $success = $stmtInsert->execute([$id_client, $id_avocat, $sujet, $description, $date_rdv, $heure_rdv])
            ? "Votre demande de rendez-vous a été envoyée avec succès."
            : "Erreur lors de la prise de rendez-vous.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Prendre rendez-vous avec Me <?= htmlspecialchars($avocat_nom) ?></title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #FAF9F4; 
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        
        .hero-section {
            background: #604B33;
            padding: 0;
            box-shadow: 0 2px 10px rgba(96, 75, 51, 0.3);
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: rgba(250, 249, 244, 0.1);
        }
        
        .logo img {
            height: 50px;
            width: auto;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2rem;
        }
        
        .nav-links a {
            color: #FAF9F4;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background: rgba(250, 249, 244, 0.2);
            transform: translateY(-2px);
        }
        
       
        .main-content {
            padding: 60px 20px;
        }
        
        .form-box {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(96, 75, 51, 0.1);
            border: 2px solid rgba(96, 75, 51, 0.1);
        }
        
        .form-box h2 {
            color: #604B33;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        label {
            display: block;
            color: #604B33;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        input, textarea, select {
            width: 100%;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid rgba(96, 75, 51, 0.2);
            box-sizing: border-box;
            font-size: 1rem;
            background: #FAF9F4;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #604B33;
            background: white;
            box-shadow: 0 0 0 3px rgba(96, 75, 51, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        button {
            width: 100%;
            background: linear-gradient(135deg, #604B33, #4a3829);
            color: #FAF9F4;
            border: none;
            cursor: pointer;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        button:hover {
            background: linear-gradient(135deg, #4a3829, #604B33);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(96, 75, 51, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error { 
            background: linear-gradient(135deg, #f8d7da, #f5c6cb); 
            color: #721c24; 
            padding: 15px 20px; 
            margin-bottom: 20px; 
            border-left: 5px solid #dc3545;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .success { 
            background: linear-gradient(135deg, #d4edda, #c3e6cb); 
            color: #155724; 
            padding: 15px 20px; 
            margin-bottom: 20px; 
            border-left: 5px solid #28a745;
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<header class="hero-section">
    <nav class="navbar">
        <div class="logo">
            <img src="lawyers imag/LOGO2-removebg-preview.png" alt="Law Firm Logo">
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

<div class="main-content">
    <div class="form-box">
        <h2>Prendre rendez-vous avec Me <?= htmlspecialchars($avocat_nom) ?></h2>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <form method="POST" action="">
            <label for="sujet">Sujet :</label>
            <input type="text" id="sujet" name="sujet" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

            <label for="date_rdv">Date (Lundi à Vendredi) :</label>
            <input type="date" id="date_rdv" name="date_rdv" value="<?= htmlspecialchars($_POST['date_rdv'] ?? '') ?>" required min="<?= date('Y-m-d') ?>">

            <label for="heure_rdv">Heure (de 09:30 à 16:30):</label>
            <select id="heure_rdv" name="heure_rdv" required>
                <option value="">-- Choisissez une heure --</option>
                <?php
                foreach ($allowed_hours as $hour) {
                    $selected = (isset($_POST['heure_rdv']) && $_POST['heure_rdv'] === $hour) ? 'selected' : '';
                    echo "<option value=\"$hour\" $selected>$hour</option>";
                }
                ?>
            </select>

            <button type="submit">Envoyer</button>
        </form>
    </div>
</div>

</body>
</html>