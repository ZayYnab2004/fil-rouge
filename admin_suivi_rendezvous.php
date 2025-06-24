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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --primary-color: #604B33;
            --primary-light: #8B7355;
            --primary-dark: #4A3829;
            --background-color: #FAF9F4;
            --accent-color: #604B33;
            --accent-hover: #4A3829;
            --text-color: #2D2520;
            --text-secondary: #5D5248;
            --text-muted: #8B7355;
            --table-header-bg: #F5F3ED;
            --box-shadow: 0 4px 6px -1px rgba(96, 75, 51, 0.1), 0 2px 4px -2px rgba(96, 75, 51, 0.1);
            --box-shadow-lg: 0 10px 15px -3px rgba(96, 75, 51, 0.1), 0 4px 6px -4px rgba(96, 75, 51, 0.1);
            --border-color: #E8E3D7;
            --surface: #FFFFFF;
            --surface-alt: #F8F6F0;
            --success: #2D7A2D;
            --warning: #D97706;
            --info: #2563EB;
            --danger: #B85450;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #F5F3ED 100%);
            color: var(--text-color);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* NAVBAR */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--box-shadow-lg);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .navbar h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #F5F3ED 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar h1::before {
            content: "⚖️";
            font-size: 1.5rem;
            -webkit-text-fill-color: initial;
        }

        .navbar a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Sidebar + Dashboard */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .sidebar {
            width: 280px;
            background: var(--surface);
            color: var(--text-color);
            padding: 40px 0;
            box-shadow: var(--box-shadow);
            border-right: 1px solid var(--border-color);
        }

        .sidebar h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 40px;
            padding: 0 30px;
            color: var(--primary-color);
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            text-decoration: none;
            padding: 16px 30px;
            margin: 4px 20px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.4s ease-out;
        }

        .sidebar a::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--accent-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            color: white;
            transform: translateX(8px);
            box-shadow: var(--box-shadow);
        }

        .sidebar a:hover::before {
            transform: scaleY(1);
        }

        /* Add icons to sidebar links */
        .sidebar a[href*="dashboard"]::after { content: "📊"; margin-left: auto; }
        .sidebar a[href*="avocats"]::after { content: "⚖️"; margin-left: auto; }
        .sidebar a[href*="clients"]::after { content: "👥"; margin-left: auto; }
        .sidebar a[href*="rendezvous"]::after { content: "📅"; margin-left: auto; }
        .sidebar a[href*="logout"]::after { content: "🚪"; margin-left: auto; }

        .sidebar a:nth-child(1) { animation-delay: 0.1s; }
        .sidebar a:nth-child(2) { animation-delay: 0.2s; }
        .sidebar a:nth-child(3) { animation-delay: 0.3s; }
        .sidebar a:nth-child(4) { animation-delay: 0.4s; }
        .sidebar a:nth-child(5) { animation-delay: 0.5s; }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 40px 50px;
            background: var(--background-color);
        }

        h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 40px;
            font-weight: 700;
            position: relative;
            padding-left: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        h2::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 60%;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            border-radius: 3px;
        }

        h2::after {
            content: "📅";
            font-size: 2rem;
            margin-left: auto;
        }

        .table-box {
            max-width: 100%;
            background: var(--surface);
            border-radius: 16px;
            box-shadow: var(--box-shadow-lg);
            overflow: hidden;
            border: 1px solid var(--border-color);
            position: relative;
            overflow-x: auto;
            animation: fadeInUp 0.6s ease-out;
        }

        .table-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color) 0%, var(--accent-hover) 50%, var(--accent-color) 100%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead {
            background: var(--table-header-bg);
            position: relative;
        }

        th, td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            white-space: nowrap;
        }

        td {
            color: var(--text-secondary);
            font-weight: 400;
            transition: all 0.3s ease;
            max-width: 200px;
            word-wrap: break-word;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: linear-gradient(135deg, rgba(96, 75, 51, 0.05) 0%, rgba(96, 75, 51, 0.02) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(96, 75, 51, 0.1);
        }

        tbody tr:hover td {
            color: var(--text-color);
        }

        /* Enhanced table styling */
        tbody td:first-child {
            font-weight: 600;
            color: var(--text-color);
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        tbody td:nth-child(2) {
            font-weight: 600;
            color: var(--text-color);
            position: relative;
        }

        tbody td:nth-child(2)::before {
            content: "👤";
            margin-right: 8px;
        }

        tbody td:nth-child(3) {
            font-weight: 600;
            color: var(--primary-color);
            position: relative;
        }

        tbody td:nth-child(3)::before {
            content: "⚖️";
            margin-right: 8px;
        }

        tbody td:nth-child(4) {
            position: relative;
            font-weight: 500;
        }

        tbody td:nth-child(4)::before {
            content: "📋";
            margin-right: 8px;
        }

        tbody td:nth-child(5) {
            font-size: 0.9rem;
            line-height: 1.5;
            max-width: 250px;
        }

        tbody td:nth-child(6) {
            position: relative;
            font-weight: 500;
            color: var(--info);
        }

        tbody td:nth-child(6)::before {
            content: "📅";
            margin-right: 8px;
        }

        tbody td:nth-child(7) {
            position: relative;
            font-weight: 500;
            color: var(--warning);
        }

        tbody td:nth-child(7)::before {
            content: "🕐";
            margin-right: 8px;
        }

        /* Status styling */
        tbody td:nth-child(8) {
            position: relative;
            font-weight: 600;
            text-transform: capitalize;
            padding: 8px 16px;
            border-radius: 20px;
            text-align: center;
            min-width: 120px;
        }

        tbody td:nth-child(8)::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        /* Status colors - you can customize these based on your status values */
        tbody tr:has(td:nth-child(8):contains("confirmé")) td:nth-child(8) {
            background: linear-gradient(135deg, var(--success) 0%, #22C55E 100%);
            color: white;
        }

        tbody tr:has(td:nth-child(8):contains("confirmé")) td:nth-child(8)::before {
            background: #22C55E;
        }

        tbody tr:has(td:nth-child(8):contains("en attente")) td:nth-child(8) {
            background: linear-gradient(135deg, var(--warning) 0%, #F59E0B 100%);
            color: white;
        }

        tbody tr:has(td:nth-child(8):contains("en attente")) td:nth-child(8)::before {
            background: #F59E0B;
        }

        tbody tr:has(td:nth-child(8):contains("annulé")) td:nth-child(8) {
            background: linear-gradient(135deg, var(--danger) 0%, #EF4444 100%);
            color: white;
        }

        tbody tr:has(td:nth-child(8):contains("annulé")) td:nth-child(8)::before {
            background: #EF4444;
        }

        /* Default status styling */
        tbody td:nth-child(8):not([style]) {
            background: linear-gradient(135deg, var(--text-muted) 0%, #9CA3AF 100%);
            color: white;
        }

        tbody td:nth-child(8):not([style])::before {
            background: #9CA3AF;
        }

        /* Empty state styling */
        tbody tr:has(td[colspan="8"]) {
            background: linear-gradient(135deg, rgba(96, 75, 51, 0.05) 0%, rgba(96, 75, 51, 0.02) 100%);
        }

        tbody tr:has(td[colspan="8"]) td {
            text-align: center;
            font-style: italic;
            color: var(--text-muted);
            font-size: 1.1rem;
            padding: 60px 20px;
            position: relative;
        }

        tbody tr:has(td[colspan="8"]) td::before {
            content: "📅";
            font-size: 3rem;
            display: block;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Responsive improvements */
        @media (max-width: 1024px) {
            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                padding: 20px 0;
            }
            .sidebar a {
                margin: 2px 20px;
                padding: 12px 20px;
            }
            .main-content {
                padding: 30px 25px;
            }
        }

        @media (max-width: 1200px) {
            .main-content {
                padding: 30px 25px;
            }
            
            h2 {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            
            th, td {
                padding: 15px 12px;
                font-size: 0.875rem;
            }
            
            tbody td:nth-child(5) {
                max-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 25px;
                flex-direction: column;
                gap: 15px;
            }
            
            .navbar h1 {
                font-size: 1.5rem;
            }

            .main-content {
                padding: 20px 15px;
            }
            
            h2 {
                font-size: 1.75rem;
            }
            
            .table-box {
                border-radius: 12px;
            }
            
            th, td {
                padding: 12px 8px;
                font-size: 0.8rem;
            }
            
            tbody td:nth-child(5) {
                max-width: 150px;
                font-size: 0.8rem;
            }
            
            tbody td:nth-child(8) {
                min-width: 100px;
                font-size: 0.75rem;
                padding: 6px 12px;
            }
        }

        /* Loading animation */
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

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--accent-hover) 0%, var(--primary-color) 100%);
        }

        /* Additional hover effects for better UX */
        table {
            transition: all 0.3s ease;
        }

        thead th {
            transition: all 0.3s ease;
        }

        thead th:hover {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            color: white;
            transform: translateY(-2px);
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 20px;
            }
            
            .table-box {
                box-shadow: none;
                border: 1px solid #ccc;
            }
            
            tbody tr:hover {
                background: none;
                transform: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <h1>Admin - Gestion des Avocats</h1>
    <a href="logoutAdmin.php">Déconnexion</a>
</nav>

<div class="dashboard-container">
    <aside class="sidebar">
        <a href="dashboard_admin.php">Tableau de bord</a>
        <a href="admin_gestion_avocats.php">Gérer les avocats</a>
        <a href="admin_gestion_clients.php">Gérer les utilisateurs</a>
        <a href="admin_suivi_rendezvous.php">Suivi des rendez-vous</a>
        <a href="logoutAdmin.php">Déconnexion</a>
    </aside>

    <div class="main-content">
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
    </div>
</div>

</body>
</html>