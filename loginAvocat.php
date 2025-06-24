<?php
session_start();
require 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

   
    $stmt = $pdo->prepare("SELECT * FROM avocat WHERE email = ?");
    $stmt->execute([$email]);
    $avocat = $stmt->fetch();

    if ($avocat && password_verify($mot_de_passe, $avocat['mot_de_passe'])) {
        $_SESSION['avocat_id'] = $avocat['id_avocat'];
        $_SESSION['avocat_nom'] = $avocat['nom'];
        header("Location: home.php"); 
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Avocat</title>
    <link rel="stylesheet" href="loginAvocat.css">
</head>
<body>
    <div class="form-section">
    <h2>Welcome back Avocat!</h2>
    <p>Welcome all your data! Login to access all your data</p>

        <?php if ($erreur): ?>
            <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Adresse Email</label>
            <input type="email" id="email" name="email" required placeholder="Entrez votre email">

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required placeholder="Entrez votre mot de passe">

            <button type="submit">Se connecter</button>
        </form>

        <div class="register-link">
        Don't have an account? <a href="singupAvocat.php">Register</a>
        </div>
    </div>
</body>
</html>
