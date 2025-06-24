<?php
session_start();
require 'config.php';

if (!isset($_SESSION['avocat_id'])) {
    header("Location: loginAvocat.php");
    exit;
}

$avocat_id = $_SESSION['avocat_id'];
$message = '';

$stmt = $pdo->prepare("SELECT * FROM avocat WHERE id_avocat = ?");
$stmt->execute([$avocat_id]);
$avocat = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biographie = $_POST['biographie'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $annees_experience = $_POST['annees_experience'];
    $langues = $_POST['langues'];

    $stmt = $pdo->prepare("UPDATE avocat SET biographie=?, adresse=?, telephone=?, email=?, annees_experience=?, langues=? WHERE id_avocat=?");
    $stmt->execute([$biographie, $adresse, $telephone, $email, $annees_experience, $langues, $avocat_id]);

    $message = "✅ Informations modifiées avec succès. Redirection vers le tableau de bord...";
    echo "<meta http-equiv='refresh' content='2;url=dashboard_avocat.php'>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #FAF9F4; /* Light background color */
            padding: 30px;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #604B33; /* Dark border color */
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #604B33; /* Dark button color */
            color: white;
            padding: 10px;
            border: none;
            margin-top: 15px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #7A6A5A; /* Lighter shade on hover */
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Modifier mes informations</h2>

    <?php if (!empty($message)) : ?>
        <div class="success-message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Biographie</label>
        <textarea name="biographie"><?= htmlspecialchars($avocat['biographie']) ?></textarea>

        <label>Adresse</label>
        <input type="text" name="adresse" value="<?= htmlspecialchars($avocat['adresse']) ?>">

        <label>Téléphone</label>
        <input type="text" name="telephone" value="<?= htmlspecialchars($avocat['telephone']) ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($avocat['email']) ?>">

        <label>Années d'expérience</label>
        <input type="number" name="annees_experience" value="<?= htmlspecialchars($avocat['annees_experience']) ?>">

        <label>Langues</label>
        <select name="langues" required>
            <option value="">-- Sélectionnez --</option>
            <option value="Français" <?= $avocat['langues'] === 'Français' ? 'selected' : '' ?>>Français</option>
            <option value="Arabe" <?= $avocat['langues'] === 'Arabe' ? 'selected' : '' ?>>Arabe</option>
            <option value="Anglais" <?= $avocat['langues'] === 'Anglais' ? 'selected' : '' ?>>Anglais</option>
            <option value="Français, Arabe" <?= $avocat['langues'] === 'Français, Arabe' ? 'selected' : '' ?>>Français, Arabe</option>
            <option value="Français, Anglais" <?= $avocat['langues'] === 'Français, Anglais' ? 'selected' : '' ?>>Français, Anglais</option>
            <option value="Arabe, Anglais" <?= $avocat['langues'] === 'Arabe, Anglais' ? 'selected' : '' ?>>Arabe, Anglais</option>
            <option value="Français, Arabe, Anglais" <?= $avocat['langues'] === 'Français, Arabe, Anglais' ? 'selected' : '' ?>>Français, Arabe, Anglais</option>
        </select>

        <input type="submit" value="Modifier">
    </form>
</div>

</body>
</html>
