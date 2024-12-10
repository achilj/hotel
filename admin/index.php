<?php 
session_start(); 

// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | Dashboard</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <h1>Dashboard</h1>
        <a class="dashknop" href="overzicht.php">Boeking</a>
        <a class="dashknop" href="kamer.php">Kamers</a>
        <a class="dashknop" href="fotos.php">Mediabibliotheek</a>
        <a class="dashknop" href="block.php">Sluitingsdagen</a>
        <a class="dashknop" href="logout.php">Uitloggen</a>
    </section>
</body>
</html>