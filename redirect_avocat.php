<?php
session_start();


if (isset($_SESSION['client_id'])) {
    header("Location: displayAvocat.php");
    exit;
}


header("Location: loginClient.php?redirect=displayAvocat.php");
exit;
?>
