<?php
require 'config.php';

$errors = [];
$success_message = '';

if (isset($_POST['create_account'])) {
    
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // التحقق من الحقول
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

    // التحقق من البريد الإلكتروني
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_client FROM client WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
    }

    // الإدخال في قاعدة البيانات
    if (empty($errors)) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO client (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $hashed_password]);

        $success_message = "Inscription réussie !";
        $_POST = []; 
        header("Location: loginClient.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Client</title>
    <link rel="stylesheet" href="singupclient.css">
</head>
<body>
    <div class="wrapper">
        <div class="image-section"></div>
        <div class="form-section">
            <h2>Create your account</h2>
            <p>Already have an account? <a href="loginClient.php">Sign in</a></p>

            <?php if ($success_message): ?>
                <p class="success"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>

            <div class="user-type">
                <label><input type="radio" name="user_type" value="avocat"> As a lawyer</label>
                <label><input type="radio" name="user_type" value="client" checked> As a client</label>
            </div>

            <form action="" method="POST">
                <label for="nom">Last Name</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Dolmatch">
                <?php if (!empty($errors['nom'])): ?><div class="error"><?= $errors['nom'] ?></div><?php endif; ?>

                <label for="prenom">First Name</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" placeholder="David">
                <?php if (!empty($errors['prenom'])): ?><div class="error"><?= $errors['prenom'] ?></div><?php endif; ?>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="Enter your email">
                <?php if (!empty($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>

                <label for="mot_de_passe">Create your password</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••">
                <?php if (!empty($errors['mot_de_passe'])): ?><div class="error"><?= $errors['mot_de_passe'] ?></div><?php endif; ?>

                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                <?php if (!empty($errors['confirm_password'])): ?><div class="error"><?= $errors['confirm_password'] ?></div><?php endif; ?>

                <!-- الزر بعد التعديل -->
                <button type="submit" name="create_account">Create account</button>
            </form>
        </div>
    </div>
    <script src="switchUserType.js"></script>
</body>
</html>
