<?php 
session_start();
require 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Récupération des rendez-vous
$query = "
    SELECT r.*, 
           c.nom AS client_nom, c.prenom AS client_prenom,
           a.nom AS avocat_nom, a.prenom AS avocat_prenom
    FROM rendezvous r
    LEFT JOIN client c ON r.id_client = c.id_client
    LEFT JOIN avocat a ON r.id_avocat = a.id_avocat
    ORDER BY r.date_rdv DESC, r.heure_rdv DESC
";

$stmt = $pdo->query($query);
$rendezvous = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Suivi des rendez-vous</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
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
            text-align: center;
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 32px;
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
        }

        td {
            color: var(--text-color);
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

<h2>Suivi des rendez-vous</h2>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Avocat</th>
                <th>Sujet</th>
                <th>Description</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rendezvous)): ?>
                <tr><td colspan="8">Aucun rendez-vous pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($rendezvous as $rdv): ?>
                    <tr>
                        <td><?= $rdv['id_rdv'] ?></td>
                        <td><?= htmlspecialchars($rdv['client_prenom'] . ' ' . $rdv['client_nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['avocat_prenom'] . ' ' . $rdv['avocat_nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['sujet']) ?></td>
                        <td><?= nl2br(htmlspecialchars($rdv['description'])) ?></td>
                        <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                        <td><?= htmlspecialchars($rdv['heure_rdv']) ?></td>
                        <td><?= htmlspecialchars($rdv['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
