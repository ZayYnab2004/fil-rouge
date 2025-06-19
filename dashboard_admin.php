<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'config.php';

$total_avocats = $pdo->query("SELECT COUNT(*) FROM avocat")->fetchColumn();
$total_clients = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
$total_rdv = $pdo->query("SELECT COUNT(*) FROM rendezvous")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <h2>Admin</h2>
        <a href="admin_gestion_avocats.php">Gérer les avocats</a>
        <a href="admin_gestion_clients.php">Gérer les utilisateurs</a>
        <a href="admin_suivi_rendezvous.php">Suivi des rendez-vous</a>
        <a href="logoutAdmin.php">Déconnexion</a>

    </aside>

    <main class="main-content">
        <h1>Tableau de bord Administrateur</h1>

        <div class="stat-box">
            <div class="box">
                <h2><?= $total_avocats ?></h2>
                <p>Avocats</p>
            </div>
            <div class="box">
                <h2><?= $total_clients ?></h2>
                <p>Utilisateurs</p>
            </div>
            <div class="box">
                <h2><?= $total_rdv ?></h2>
                <p>Rendez-vous</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
