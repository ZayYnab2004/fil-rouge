<?php 
session_start();
require 'config.php';


if (!isset($_SESSION['client_id'])) {
    die("Veuillez vous connecter pour accéder à votre tableau de bord.");
}

$client_id = $_SESSION['client_id'];

$stmtClient = $pdo->prepare("SELECT nom, prenom FROM client WHERE id_client = ?");
$stmtClient->execute([$client_id]);
$client = $stmtClient->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Client introuvable.");
}


$stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE id_client = ? ORDER BY date_rdv DESC, heure_rdv DESC");
$stmt->execute([$client_id]);
$rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Mes Rendez-vous</title>
    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAF9F4;
            color: #604B33;
            margin: 0;
            padding: 30px 15px;
        }

        h1, h2 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.2rem;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 30px;
        }

      
        .table-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(96, 75, 51, 0.1);
            padding: 20px 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #E2DCC8;
            font-size: 1rem;
        }

        th {
            background-color: #604B33;
            color: #FAF9F4;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        tr:hover {
            background-color: #FAF9F4;
            cursor: default;
        }

      
        .status-confirmed {
            color: #1B6B1B; 
            font-weight: 700;
        }

        .status-canceled {
            color: #B03A3A; 
            font-weight: 700;
        }

        .status-pending {
            color: #B07B3A; 
            font-weight: 700;
        }

        
        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }

            h1 {
                font-size: 1.6rem;
            }

            h2 {
                font-size: 1.2rem;
                margin-bottom: 20px;
            }

            th, td {
                padding: 12px 8px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <h1>Bonjour <?= htmlspecialchars($client['nom']) . ' ' . htmlspecialchars($client['prenom']); ?>, bienvenue dans votre tableau de bord</h1>

    <h2>Vos rendez-vous</h2>

    <div class="table-container">
    <?php if (count($rendezvous) === 0): ?>
        <p style="text-align:center; font-size:1.2rem;">Vous n'avez aucun rendez-vous.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Sujet</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezvous as $rdv): ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['sujet']) ?></td>
                        <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                        <td><?= htmlspecialchars($rdv['heure_rdv']) ?></td>
                        <td>
                            <?php
                            $status = $rdv['statut'];
                            if ($status === 'confirmé') {
                                echo '<span class="status-confirmed">Confirmé</span>';
                            } elseif ($status === 'annulé') {
                                echo '<span class="status-canceled">Annulé</span>';
                            } else {
                                echo '<span class="status-pending">En attente</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>

</body>
</html>
