<?php
require 'config.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and fetch input
    $name = trim($_POST['name'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $biographie = trim($_POST['biographie'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $annees_experience = $_POST['annees_experience'] ?? '';
    $langues = $_POST['langues'] ?? [];
    $diplome = trim($_POST['diplome'] ?? '');
    $id_Specialite = $_POST['id_Specialite'] ?? '';

    // Validate inputs
    if ($name === '') $errors['name'] = 'Le nom est requis.';
    if ($prenom === '') $errors['prenom'] = 'Le prénom est requis.';
    if (empty($_FILES['photo']['name'])) $errors['photo'] = 'La photo est requise.';
    if ($biographie === '') $errors['biographie'] = 'La biographie est requise.';
    if ($adresse === '') $errors['adresse'] = 'L\'adresse est requise.';
    if ($telephone === '') $errors['telephone'] = 'Le téléphone est requis.';
    if ($email === '') $errors['email'] = 'L\'email est requis.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide.';
    if ($mot_de_passe === '') $errors['mot_de_passe'] = 'Le mot de passe est requis.';
    elseif (strlen($mot_de_passe) < 6) $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    if ($confirm_password !== $mot_de_passe) $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
    if ($annees_experience === '' || !is_numeric($annees_experience)) $errors['annees_experience'] = 'Veuillez entrer un nombre valide pour les années d\'expérience.';
    if (empty($langues)) $errors['langues'] = 'Veuillez sélectionner au moins une langue.';
    if ($id_Specialite === '') $errors['id_Specialite'] = 'La spécialité est requise.';

    // Handle photo upload
    if (!isset($errors['photo'])) {
        $photo = $_FILES['photo'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo['type'], $allowed_types)) {
            $errors['photo'] = 'Format de photo non autorisé (jpeg, png, gif uniquement).';
        } elseif ($photo['size'] > 2 * 1024 * 1024) {
            $errors['photo'] = 'La photo doit faire moins de 2Mo.';
        } else {
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $photo_name = time() . '_' . basename($photo['name']);
            $photo_path = $upload_dir . $photo_name;

            if (!move_uploaded_file($photo['tmp_name'], $photo_path)) {
                $errors['photo'] = 'Erreur lors de l\'upload de la photo.';
            }
        }
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id_avocat FROM avocat WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        } else {
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $langues_str = implode(', ', $langues);
            $photo_db_path = 'uploads/' . $photo_name;

            $sql = "INSERT INTO avocat (nom, prenom, photo, biographie, adresse, telephone, email, mot_de_passe, annees_experience, langues, diplome, id_Specialite)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $name, $prenom, $photo_db_path, $biographie, $adresse, $telephone, $email, $hashed_password,
                $annees_experience, $langues_str, $diplome, $id_Specialite
            ]);

            $success_message = "Inscription réussie !";
            $_POST = []; // Clear form data
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Inscription Avocat</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f0f0f0; padding:20px; }
        .container { background:#fff; padding:20px; max-width:500px; margin:0 auto; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
        label { display:block; margin-top:15px; font-weight:bold; }
        input[type=text], input[type=email], input[type=password], input[type=number], textarea, select { width:100%; padding:8px; margin-top:5px; box-sizing:border-box; }
        .checkbox-group label { font-weight: normal; margin-right: 10px; }
        .error { color:red; font-size:0.9em; margin-top:3px; }
        .success { color:green; font-size:1em; text-align:center; margin-bottom:15px; }
        button { margin-top:20px; padding:10px 15px; background:#2c3e50; color:#fff; border:none; width:100%; font-size:16px; cursor:pointer; border-radius:5px; }
        button:hover { background:#34495e; }
    </style>
</head>
<body>
<div class="container">
    <h2>Inscription Avocat</h2>

    <?php if ($success_message): ?>
        <p class="success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

 <div class="user-type">
                    <label><input type="radio" name="user_type" value="avocat"> As a lawyer</label>
                    <label><input type="radio" name="user_type" value="client" checked> As a client</label>
                  </div>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Nom</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <?php if (!empty($errors['name'])): ?><div class="error"><?= $errors['name'] ?></div><?php endif; ?>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        <?php if (!empty($errors['prenom'])): ?><div class="error"><?= $errors['prenom'] ?></div><?php endif; ?>

        <label for="photo">Photo (jpg, png, gif, max 2Mo)</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        <?php if (!empty($errors['photo'])): ?><div class="error"><?= $errors['photo'] ?></div><?php endif; ?>

        <label for="biographie">Biographie</label>
        <textarea id="biographie" name="biographie"><?= htmlspecialchars($_POST['biographie'] ?? '') ?></textarea>
        <?php if (!empty($errors['biographie'])): ?><div class="error"><?= $errors['biographie'] ?></div><?php endif; ?>

        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
        <?php if (!empty($errors['adresse'])): ?><div class="error"><?= $errors['adresse'] ?></div><?php endif; ?>

        <label for="telephone">Téléphone</label>
        <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
        <?php if (!empty($errors['telephone'])): ?><div class="error"><?= $errors['telephone'] ?></div><?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">
        <?php if (!empty($errors['mot_de_passe'])): ?><div class="error"><?= $errors['mot_de_passe'] ?></div><?php endif; ?>

        <label for="confirm_password">Confirmer mot de passe</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <?php if (!empty($errors['confirm_password'])): ?><div class="error"><?= $errors['confirm_password'] ?></div><?php endif; ?>

        <label for="annees_experience">Années d'expérience</label>
        <input type="number" id="annees_experience" name="annees_experience" value="<?= htmlspecialchars($_POST['annees_experience'] ?? '') ?>">
        <?php if (!empty($errors['annees_experience'])): ?><div class="error"><?= $errors['annees_experience'] ?></div><?php endif; ?>

        <label>Langues</label>
        <div class="checkbox-group">
            <?php
            $available_langs = ['Français', 'Arabe', 'Anglais'];
            $selected_langs = $_POST['langues'] ?? [];
            foreach ($available_langs as $lang) {
                $checked = in_array($lang, $selected_langs) ? 'checked' : '';
                echo '<label><input type="checkbox" name="langues[]" value="'.htmlspecialchars($lang).'" '.$checked.'> '.htmlspecialchars($lang).'</label>';
            }
            ?>
        </div>
        <?php if (!empty($errors['langues'])): ?><div class="error"><?= $errors['langues'] ?></div><?php endif; ?>

        <label for="diplome">Diplôme</label>
        <textarea id="diplome" name="diplome"><?= htmlspecialchars($_POST['diplome'] ?? '') ?></textarea>

        <label for="id_Specialite">Spécialité</label>
        <select id="id_Specialite" name="id_Specialite">
            <option value="">-- Sélectionnez --</option>
            <option value="1" <?= (($_POST['id_Specialite'] ?? '') === '1') ? 'selected' : '' ?>>Droit Civil</option>
            <option value="2" <?= (($_POST['id_Specialite'] ?? '') === '2') ? 'selected' : '' ?>>Droit Pénal</option>
            <option value="3" <?= (($_POST['id_Specialite'] ?? '') === '3') ? 'selected' : '' ?>>Droit du Travail</option>
        </select>
        <?php if (!empty($errors['id_Specialite'])): ?><div class="error"><?= $errors['id_Specialite'] ?></div><?php endif; ?>

        <button type="submit">S'inscrire</button>
    </form>
</div>
</body>
</html>
