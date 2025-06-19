<?php 
session_start();
require 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['avocat_id'])) {
    header("Location: loginAvocat.php");
    exit;
}

$avocat_id = $_SESSION['avocat_id'];


if ($_POST['action'] ?? false) {
    $rdv_id = $_POST['rdv_id'] ?? 0;
    $action = $_POST['action'];
    
    $new_status = '';
    switch($action) {
        case 'confirm':
            $new_status = 'confirmé';
            break;
        case 'cancel':
            $new_status = 'annulé';
            break;
        case 'pending':
            $new_status = 'en attente';
            break;
    }
    
    if ($new_status && $rdv_id) {
        $update_query = "UPDATE rendezvous SET statut = ? WHERE id_rdv = ? AND id_avocat = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$new_status, $rdv_id, $avocat_id]);
        
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


if ($_POST['edit_action'] ?? false) {
    $rdv_id = $_POST['rdv_id'] ?? 0;
    $new_date = $_POST['new_date'] ?? '';
    $new_time = $_POST['new_time'] ?? '';
    $new_subject = $_POST['new_subject'] ?? '';
    $new_description = $_POST['new_description'] ?? '';
    
    if ($rdv_id && $new_date && $new_time) {
        $edit_query = "UPDATE rendezvous SET date_rdv = ?, heure_rdv = ?, sujet = ?, description = ? WHERE id_rdv = ? AND id_avocat = ?";
        $edit_stmt = $pdo->prepare($edit_query);
        $edit_stmt->execute([$new_date, $new_time, $new_subject, $new_description, $rdv_id, $avocat_id]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


$lawyer_query = "SELECT nom, prenom FROM avocat WHERE id_avocat = ?";
$lawyer_stmt = $pdo->prepare($lawyer_query);
$lawyer_stmt->execute([$avocat_id]);
$lawyer_info = $lawyer_stmt->fetch();

$query = "
    SELECT r.*, 
           c.nom AS client_nom, c.prenom AS client_prenom
    FROM rendezvous r
    LEFT JOIN client c ON r.id_client = c.id_client
    WHERE r.id_avocat = ?
    ORDER BY r.date_rdv DESC, r.heure_rdv DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$avocat_id]);
$rendezvous = $stmt->fetchAll();

// Calculate stats
$total_appointments = count($rendezvous);
$pending_count = count(array_filter($rendezvous, fn($r) => $r['statut'] === 'en attente'));
$confirmed_count = count(array_filter($rendezvous, fn($r) => $r['statut'] === 'confirmé'));
$canceled_count = count(array_filter($rendezvous, fn($r) => $r['statut'] === 'annulé'));
$today_appointments = count(array_filter($rendezvous, fn($r) => $r['date_rdv'] === date('Y-m-d')));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord avocat - Rendez-vous</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FAF9F4 0%, #E8E2D4 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: #FAF9F4;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(96, 75, 51, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border: 2px solid #604B33;
        }

        .header-left h1 {
            color: #604B33;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-left .subtitle {
            color: #8B7355;
            font-size: 1.1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #F5F3ED;
            padding: 15px 20px;
            border-radius: 50px;
            border: 1px solid #604B33;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #604B33, #8B7355);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FAF9F4;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #FAF9F4;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(96, 75, 51, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid #604B33;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(96, 75, 51, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #FAF9F4;
        }

        .stat-icon.total { background: linear-gradient(135deg, #604B33, #8B7355); }
        .stat-icon.pending { background: linear-gradient(135deg, #D4A574, #B8956A); }
        .stat-icon.confirmed { background: linear-gradient(135deg, #7A9A65, #5F7A4F); }
        .stat-icon.canceled { background: linear-gradient(135deg, #C85450, #A44440); }
        .stat-icon.today { background: linear-gradient(135deg, #6B8E9A, #547A85); }

        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #604B33;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #8B7355;
            font-size: 0.9rem;
        }

        .main-content {
            background: #FAF9F4;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(96, 75, 51, 0.1);
            border: 2px solid #604B33;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .content-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #604B33;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .filter-controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #604B33;
            background: #FAF9F4;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            color: #604B33;
            font-weight: 600;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #604B33;
            color: #FAF9F4;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .appointments-table th {
            background: #F5F3ED;
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            color: #604B33;
            border-bottom: 2px solid #604B33;
        }

        .appointments-table th:first-child {
            border-radius: 10px 0 0 0;
        }

        .appointments-table th:last-child {
            border-radius: 0 10px 0 0;
        }

        .appointments-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #E8E2D4;
            vertical-align: top;
        }

        .appointments-table tr:hover {
            background: #F5F3ED;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #604B33, #8B7355);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FAF9F4;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .client-details h4 {
            color: #604B33;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .appointment-date {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .date-text {
            font-weight: 600;
            color: #604B33;
        }

        .time-text {
            color: #8B7355;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-waiting {
            background: #FFF8DC;
            color: #B8860B;
            border: 1px solid #D4A574;
        }

        .status-confirmed {
            background: #F0FFF0;
            color: #006400;
            border: 1px solid #7A9A65;
        }

        .status-canceled {
            background: #FFE4E1;
            color: #8B0000;
            border: 1px solid #C85450;
        }

        .subject-cell {
            max-width: 200px;
            font-weight: 600;
            color: #604B33;
        }

        .description-cell {
            max-width: 250px;
            color: #8B7355;
            line-height: 1.4;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-edit {
            background: #604B33;
            color: #FAF9F4;
        }

        .btn-edit:hover {
            background: #8B7355;
        }

        .btn-confirm {
            background: #7A9A65;
            color: #FAF9F4;
        }

        .btn-confirm:hover {
            background: #5F7A4F;
        }

        .btn-cancel {
            background: #C85450;
            color: #FAF9F4;
        }

        .btn-cancel:hover {
            background: #A44440;
        }

        .btn-pending {
            background: #D4A574;
            color: #604B33;
        }

        .btn-pending:hover {
            background: #B8956A;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #8B7355;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #D4C5B0;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #604B33;
        }

        .logout-btn {
            background: #C85450;
            color: #FAF9F4;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #A44440;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(200, 84, 80, 0.3);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(96, 75, 51, 0.5);
        }

        .modal-content {
            background-color: #FAF9F4;
            margin: 10% auto;
            padding: 30px;
            border: 2px solid #604B33;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(96, 75, 51, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E8E2D4;
        }

        .modal-header h3 {
            color: #604B33;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .close {
            color: #8B7355;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #604B33;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #604B33;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E8E2D4;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: #FAF9F4;
            color: #604B33;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #604B33;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #E8E2D4;
        }

        .btn-save {
            background: #7A9A65;
            color: #FAF9F4;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: #5F7A4F;
        }

        .btn-cancel-modal {
            background: #E8E2D4;
            color: #604B33;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel-modal:hover {
            background: #D4C5B0;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .header-left h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .appointments-table {
                font-size: 0.9rem;
            }

            .appointments-table th,
            .appointments-table td {
                padding: 15px 10px;
            }

            .actions-cell {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
       
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-balance-scale"></i> Tableau de Bord</h1>
                <p class="subtitle">Gestion des rendez-vous clients</p>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($lawyer_info['prenom'] ?? 'A', 0, 1) . substr($lawyer_info['nom'] ?? 'L', 0, 1)) ?>
                    </div>
                    <div>
                        <strong><?= htmlspecialchars(($lawyer_info['prenom'] ?? '') . ' ' . ($lawyer_info['nom'] ?? '')) ?></strong>
                        <br><small>Avocat</small>
                    </div>
                </div>
                <a href="logoutavocat.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>

            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $total_appointments ?></h3>
                    <p>Total des rendez-vous</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $pending_count ?></h3>
                    <p>En attente</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $confirmed_count ?></h3>
                    <p>Confirmés</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon canceled">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $canceled_count ?></h3>
                    <p>Annulés</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $today_appointments ?></h3>
                    <p>Aujourd'hui</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h2 class="content-title">
                    <i class="fas fa-calendar-check"></i>
                    Mes Rendez-vous
                </h2>
                <div class="filter-controls">
                    <button class="filter-btn active" onclick="filterAppointments('all')">Tous</button>
                    <button class="filter-btn" onclick="filterAppointments('en attente')">En attente</button>
                    <button class="filter-btn" onclick="filterAppointments('confirmé')">Confirmés</button>
                    <button class="filter-btn" onclick="filterAppointments('annulé')">Annulés</button>
                    <button class="filter-btn" onclick="filterAppointments('today')">Aujourd'hui</button>
                </div>
            </div>

            <?php if (empty($rendezvous)): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucun rendez-vous</h3>
                    <p>Vous n'avez aucun rendez-vous programmé pour le moment.</p>
                </div>
            <?php else: ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Sujet</th>
                            <th>Description</th>
                            <th>Date & Heure</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rendezvous as $rdv): ?>
                            <tr data-status="<?= htmlspecialchars($rdv['statut']) ?>" data-date="<?= htmlspecialchars($rdv['date_rdv']) ?>">
                                <td><strong>#<?= htmlspecialchars($rdv['id_rdv']) ?></strong></td>
                                <td>
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            <?= strtoupper(substr($rdv['client_prenom'] ?? 'C', 0, 1) . substr($rdv['client_nom'] ?? 'L', 0, 1)) ?>
                                        </div>
                                        <div class="client-details">
                                            <h4><?= htmlspecialchars($rdv['client_prenom'] . ' ' . $rdv['client_nom']) ?></h4>
                                        </div>
                                    </div>
                                </td>
                                <td class="subject-cell"><?= htmlspecialchars($rdv['sujet']) ?></td>
                                <td class="description-cell"><?= nl2br(htmlspecialchars($rdv['description'])) ?></td>
                                <td>
                                    <div class="appointment-date">
                                        <span class="date-text"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></span>
                                        <span class="time-text"><?= htmlspecialchars($rdv['heure_rdv']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $rdv['statut'] === 'en attente' ? 'waiting' : ($rdv['statut'] === 'confirmé' ? 'confirmed' : 'canceled') ?>">
                                        <?= htmlspecialchars($rdv['statut']) ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button class="action-btn btn-edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($rdv), ENT_QUOTES) ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    
                                    <?php if ($rdv['statut'] !== 'confirmé'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['id_rdv']) ?>">
                                            <input type="hidden" name="action" value="confirm">
                                            <button type="submit" class="action-btn btn-confirm">
                                                <i class="fas fa-check"></i> Confirmer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($rdv['statut'] !== 'annulé'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['id_rdv']) ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <button type="submit" class="action-btn btn-cancel" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                                <i class="fas fa-times"></i> Annuler
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($rdv['statut'] === 'annulé'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="rdv_id" value="<?= htmlspecialchars($rdv['id_rdv']) ?>">
                                            <input type="hidden" name="action" value="pending">
                                            <button type="submit" class="action-btn btn-pending">
                                                <i class="fas fa-undo"></i> Remettre
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier le rendez-vous</h3>
                <span class="close" onclick="closeModal()">×</span>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="edit_action" value="1">
                <input type="hidden" name="rdv_id" id="edit_rdv_id">
                
                <div class="form-group">
                    <label for="edit_date">Date:</label>
                    <input type="date" id="edit_date" name="new_date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_time">Heure:</label>
                    <input type="time" id="edit_time" name="new_time" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_subject">Sujet:</label>
                    <input type="text" id="edit_subject" name="new_subject" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description:</label>
                    <textarea id="edit_description" name="new_description"></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterAppointments(filter, event) {
            const rows = document.querySelectorAll('.appointments-table tbody tr');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to the clicked button
            if (event && event.target) {
                event.target.classList.add('active');
            } else {
                // Fallback: manually set active class for the first button if event is undefined
                document.querySelector(`.filter-btn[onclick="filterAppointments('${filter}')"]`)?.classList.add('active');
            }
            
            // Filter rows
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const date = row.getAttribute('data-date');
                const today = new Date().toISOString().split('T')[0];
                
                let show = false;
                
                switch(filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'today':
                        show = date === today;
                        break;
                    default:
                        show = status === filter;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }

        function openEditModal(rdv) {
            document.getElementById('edit_rdv_id').value = rdv.id_rdv;
            document.getElementById('edit_date').value = rdv.date_rdv;
            document.getElementById('edit_time').value = rdv.heure_rdv;
            document.getElementById('edit_subject').value = rdv.sujet;
            document.getElementById('edit_description').value = rdv.description || '';
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filter to show all appointments
            filterAppointments('all');

            // Add click handlers for modal close
            document.querySelector('.close').addEventListener('click', closeModal);
            document.querySelector('.btn-cancel-modal').addEventListener('click', closeModal);

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === document.getElementById('editModal')) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>