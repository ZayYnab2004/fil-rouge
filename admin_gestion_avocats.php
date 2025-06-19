<?php
session_start();
require 'config.php';


if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM avocat WHERE id_avocat = ?");
    $stmt->execute([$id]);
    header("Location: admin_gestion_avocats.php");
    exit;
}


$avocats = $pdo->query("
    SELECT a.*, s.nom_specialite 
    FROM avocat a
    LEFT JOIN specialite s ON a.id_Specialite = s.id_Specialite
    ORDER BY a.id_avocat DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des avocats</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --primary-color: #604B33;
            --background-color: #FAF9F4;
            --accent-color: #bc9f6a;
            --text-color: #222;
            --table-header-bg: #eae3d2;
            --box-shadow: rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 40px;
        }

        h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 32px;
            text-align: center;
        }

        .table-box {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--box-shadow);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--table-header-bg);
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-weight: 600;
            color: var(--primary-color);
            background-color: var(--table-header-bg);
        }

        td {
            color: var(--text-color);
        }

        .delete-link {
            color: #c43a31;
            text-decoration: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            background-color: #ffe5e2;
            transition: background 0.3s ease, color 0.3s ease;
            display: inline-block;
        }

        .delete-link:hover {
            background-color: #f7c5c0;
            color: #911b14;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            h2 {
                font-size: 2rem;
            }

            th, td {
                font-size: 0.95rem;
                padding: 12px 10px;
            }

            .delete-link {
                font-size: 0.9rem;
                padding: 5px 10px;
            }
        }

        @media (max-width: 480px) {
            th, td {
                font-size: 0.85rem;
                padding: 10px 8px;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<h2>Liste des avocats</h2>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Spécialité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avocats as $avocat): ?>
            <tr>
                <td><?= $avocat['id_avocat'] ?></td>
                <td><?= htmlspecialchars($avocat['prenom'] . ' ' . $avocat['nom']) ?></td>
                <td><?= htmlspecialchars($avocat['email']) ?></td>
                <td><?= htmlspecialchars($avocat['nom_specialite'] ?? 'Non défini') ?></td>
                <td>
                    <a class="delete-link" href="?delete=<?= $avocat['id_avocat'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avocat ?')">
                        🗑 Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
