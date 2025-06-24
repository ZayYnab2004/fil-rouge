<?php
session_start();

require 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM client WHERE email = ?");
    $stmt->execute([$email]);
    $client = $stmt->fetch();

    if ($client && password_verify($mot_de_passe, $client['mot_de_passe'])) {
        $_SESSION['client_id'] = $client['id_client'];
        $_SESSION['client_nom'] = $client['nom'];
        header("Location: displayavocat.php");
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
    <title>Connexion Client</title>
   <link rel="stylesheet" href="loginClient.css">
</head>
<body>
    <div class="form-section">
        <h2>Welcome back!</h2>
        <p>Welcome all your data! Login to access all your data</p>

        <?php if ($erreur): ?>
            <p class="error"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email address"><br>

            <label for="mot_de_passe">Password</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required placeholder="Enter your password"><br>

            <button type="submit">login</button>
        </form>
        <div class="register-link">Don't have an account? <a href="singupclient.php">Register</a></div>
    </div>
</body>
</html>