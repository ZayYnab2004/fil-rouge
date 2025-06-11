<?php
require 'config.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if ($nom === '') $errors['nom'] = 'Le nom est requis.';
    if ($prenom === '') $errors['prenom'] = 'Le prénom est requis.';
    if ($email === '') {
        $errors['email'] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide.';
    }
    if ($mot_de_passe === '') {
        $errors['mot_de_passe'] = 'Le mot de passe est requis.';
    } elseif (strlen($mot_de_passe) < 6) {
        $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($mot_de_passe !== $confirm_password) {
        $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_client FROM client WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO client (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $hashed_password]);

        $success_message = "Inscription réussie !";
        $_POST = []; // Clear form
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Client</title>
</head>
<body>
<div class="container">
    <h2>Inscription Client</h2>

    <?php if ($success_message): ?>
        <p class="success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>
    <div class="user-type">
        <label><input type="radio" name="user_type" value="avocat"> As a lawyer</label>
        <label><input type="radio" name="user_type" value="client" checked> As a client</label>
    </div>

    <form action="" method="POST">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        <?php if (!empty($errors['nom'])): ?><div class="error"><?= $errors['nom'] ?></div><?php endif; ?>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        <?php if (!empty($errors['prenom'])): ?><div class="error"><?= $errors['prenom'] ?></div><?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">
        <?php if (!empty($errors['mot_de_passe'])): ?><div class="error"><?= $errors['mot_de_passe'] ?></div><?php endif; ?>

        <label for="confirm_password">Confirmer mot de passe</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <?php if (!empty($errors['confirm_password'])): ?><div class="error"><?= $errors['confirm_password'] ?></div><?php endif; ?>

        <button type="submit">S'inscrire</button>
    </form>
</div>
</body>
</html>
