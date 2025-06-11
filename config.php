<?php
$localhost = "localhost";
$dbname = "avocat_website";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$localhost;dbname=$dbname;charset=UTF8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>