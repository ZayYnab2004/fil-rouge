<?php
session_start();
require 'config.php'; 


if (!isset($_SESSION['client_id'])) {
    die("Vous devez être connecté pour ajouter ou voir les avis.");
}

$client_id = $_SESSION['client_id'];
$errors = [];
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_avocat = intval($_POST['id_avocat'] ?? 0);
    $note = intval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($id_avocat <= 0) $errors[] = "Veuillez choisir un avocat.";
    if ($note < 1 || $note > 5) $errors[] = "La note doit être comprise entre 1 et 5.";
    if (empty($commentaire)) $errors[] = "Veuillez écrire un commentaire.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO avis (id_client, id_avocat, note, commentaire, date_avis) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$client_id, $id_avocat, $note, $commentaire]);
        $success = "Votre avis a été envoyé avec succès.";
    }
}


$sql = "SELECT a.*, c.nom AS client_nom, av.nom AS avocat_nom 
        FROM avis a 
        JOIN client c ON a.id_client = c.id_client 
        JOIN avocat av ON a.id_avocat = av.id_avocat
        ORDER BY a.date_avis DESC";
$avis = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les avis</title>
    <link rel="stylesheet" href="avis.css">
</head>
<body>

<h1>Ajouter un avis</h1>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul class="error">
        <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="">
    <label>Choisir un avocat :</label><br>
    <select name="id_avocat" required>
        <option value="">-- Sélectionner --</option>
        <?php
        $stmtAvocat = $pdo->query("SELECT id_avocat, nom FROM avocat ORDER BY nom");
        foreach ($stmtAvocat as $avocat) {
            $selected = (isset($_POST['id_avocat']) && $_POST['id_avocat'] == $avocat['id_avocat']) ? 'selected' : '';
            echo "<option value=\"{$avocat['id_avocat']}\" $selected>" . htmlspecialchars($avocat['nom']) . "</option>";
        }
        ?>
    </select><br><br>

    <label>Note :</label><br>
    <div class="star-rating">
        <?php for ($i = 5; $i >= 1; $i--): 
            $checked = (isset($_POST['note']) && $_POST['note'] == $i) ? 'checked' : '';
        ?>
            <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" <?= $checked ?> required />
            <label for="star<?= $i ?>">★</label>
        <?php endfor; ?>
    </div><br><br>

    <label>Commentaire :</label><br>
    <textarea name="commentaire" rows="4" required><?= htmlspecialchars($_POST['commentaire'] ?? '') ?></textarea><br><br>

    <button type="submit">Envoyer l’avis</button>
</form>

<hr>
<h2>Liste des avis</h2>

<?php if ($avis): ?>
    <?php foreach ($avis as $item): ?>
        <div class="avis-item">
            <strong>Avocat :</strong> <?= htmlspecialchars($item['avocat_nom']) ?><br>
            <strong>Client :</strong> <?= htmlspecialchars($item['client_nom']) ?><br>
            <strong>Note :</strong> 
            <span class="stars-view">
                <?php
                $note = intval($item['note']);
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $note 
                        ? '<span>★</span>' 
                        : '<span class="empty">★</span>';
                }
                ?>
            </span><br>
            <strong>Commentaire :</strong> <?= nl2br(htmlspecialchars($item['commentaire'])) ?><br>
            <small>Le : <?= htmlspecialchars($item['date_avis']) ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun avis pour le moment.</p>
<?php endif; ?>

</body>
</html>
