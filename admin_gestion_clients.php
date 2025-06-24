<?php
session_start();
require 'config.php';


if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM client WHERE id_client = ?");
    $stmt->execute([$id]);
    header("Location: admin_gestion_clients.php");
    exit;
}


$clients = $pdo->query("SELECT id_client, nom, prenom, email FROM client ORDER BY id_client DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des clients</title>
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

        .table-box {
            max-width: 100%;
            background: var(--surface);
            border-radius: 16px;
            box-shadow: var(--box-shadow-lg);
            overflow: hidden;
            border: 1px solid var(--border-color);
            position: relative;
            overflow-x: auto;
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
        }

        td {
            color: var(--text-secondary);
            font-weight: 400;
            transition: all 0.3s ease;
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

        /* Enhanced delete button */
        .delete-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--danger) 0%, #9E453F 100%);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(184, 84, 80, 0.2);
        }

        .delete-link:hover {
            background: linear-gradient(135deg, #9E453F 0%, #8A3D38 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(184, 84, 80, 0.3);
        }

        .delete-link:active {
            transform: translateY(0);
        }

        /* Add some style to table data */
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
        }

        tbody td:nth-child(4) {
            position: relative;
        }

        tbody td:nth-child(4)::before {
            content: "🏷️";
            margin-right: 8px;
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

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 25px;
                flex-direction: column;
                gap: 15px;
            }
            
            .navbar h1 {
                font-size: 1.5rem;
            }
            
            h2 {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            
            .main-content {
                padding: 20px 15px;
            }
            
            th, td {
                padding: 15px 12px;
                font-size: 0.875rem;
            }
            
            .delete-link {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
        }

        /* Loading animation for when page loads */
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

        .table-box {
            animation: fadeInUp 0.6s ease-out;
        }

        .sidebar a {
            animation: fadeInUp 0.4s ease-out;
        }

        .sidebar a:nth-child(1) { animation-delay: 0.1s; }
        .sidebar a:nth-child(2) { animation-delay: 0.2s; }
        .sidebar a:nth-child(3) { animation-delay: 0.3s; }
        .sidebar a:nth-child(4) { animation-delay: 0.4s; }
        .sidebar a:nth-child(5) { animation-delay: 0.5s; }

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
<div class="dashboard-containers">



   
    <div class="main-content">
        <h1>Liste des clients</h1>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clients) === 0): ?>
                        <tr><td colspan="5">Aucun client trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['id_client']) ?></td>
                            <td><?= htmlspecialchars($client['nom']) ?></td>
                            <td><?= htmlspecialchars($client['prenom']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td>
                                <a class="delete-link" href="?delete=<?= $client['id_client'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">🗑 Supprimer</a>
                            </td>
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
