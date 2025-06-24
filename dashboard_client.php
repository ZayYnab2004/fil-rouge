<?php 
session_start();
require 'config.php';

if (!isset($_SESSION['client_id'])) {
    header('Location: loginClient.php'); // Redirect to login page
    exit;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Mes Rendez-vous</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #FAF9F4 0%, #F5F3E9 100%);
            color: #604B33;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header Section */
        .header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(96, 75, 51, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #604B33, #8B7355, #604B33);
            border-radius: 20px 20px 0 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-section {
            flex: 1;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #604B33;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: #8B7355;
            font-weight: 400;
        }

        .logout-btn {
            background: linear-gradient(135deg, #604B33, #8B7355);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(96, 75, 51, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(96, 75, 51, 0.4);
        }

        /* Appointments Section */
        .appointments-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(96, 75, 51, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #604B33;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .appointments-grid {
            display: grid;
            gap: 1rem;
        }

        .appointment-card {
            background: rgba(250, 249, 244, 0.8);
            border-radius: 16px;
            padding: 1.5rem;
            border-left: 4px solid #604B33;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: rgba(96, 75, 51, 0.05);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        .appointment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(96, 75, 51, 0.15);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .appointment-subject {
            font-size: 1.2rem;
            font-weight: 700;
            color: #604B33;
            margin-bottom: 0.5rem;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #8B7355;
            font-weight: 500;
        }

        .detail-icon {
            width: 18px;
            height: 18px;
            color: #604B33;
        }

        /* Status Badges */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            min-width: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .status-confirmed {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .status-canceled {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #8B7355;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #C4B59A;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #604B33;
        }

        .empty-description {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .welcome-title {
                font-size: 2rem;
                justify-content: center;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.4rem;
            }

            .appointment-card {
                padding: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .appointment-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .appointment-card:nth-child(1) { animation-delay: 0.1s; }
        .appointment-card:nth-child(2) { animation-delay: 0.2s; }
        .appointment-card:nth-child(3) { animation-delay: 0.3s; }
        .appointment-card:nth-child(4) { animation-delay: 0.4s; }
        .appointment-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="welcome-section">
                    <h1 class="welcome-title">
                        <i class="fas fa-user-circle"></i>
                        Bonjour <?= htmlspecialchars($client['nom']) . ' ' . htmlspecialchars($client['prenom']); ?>
                    </h1>
                    <p class="welcome-subtitle">Bienvenue dans votre tableau de bord</p>
                </div>
                <a href="logoutclient.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="appointments-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Vos rendez-vous
            </h2>

            <div class="appointments-grid">
                <?php if (count($rendezvous) === 0): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3 class="empty-title">Aucun rendez-vous</h3>
                        <p class="empty-description">Vous n'avez aucun rendez-vous programmé pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($rendezvous as $rdv): ?>
                        <div class="appointment-card">
                            <div class="card-header">
                                <div>
                                    <div class="appointment-subject"><?= htmlspecialchars($rdv['sujet']) ?></div>
                                </div>
                                <span class="status-badge <?php
                                    $status = $rdv['statut'];
                                    if ($status === 'confirmé') {
                                        echo 'status-confirmed';
                                    } elseif ($status === 'annulé') {
                                        echo 'status-canceled';
                                    } else {
                                        echo 'status-pending';
                                    }
                                ?>">
                                    <?php
                                    if ($status === 'confirmé') {
                                        echo '<i class="fas fa-check-circle"></i> Confirmé';
                                    } elseif ($status === 'annulé') {
                                        echo '<i class="fas fa-times-circle"></i> Annulé';
                                    } else {
                                        echo '<i class="fas fa-hourglass-half"></i> En attente';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="appointment-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar detail-icon"></i>
                                    <span><?= htmlspecialchars($rdv['date_rdv']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock detail-icon"></i>
                                    <span><?= htmlspecialchars($rdv['heure_rdv']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>