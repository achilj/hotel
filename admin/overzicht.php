<?php
include('../config/conn.php');

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
    <title>ADMIN | Boekingen</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <h1>Boekingen</h1>
        <a class="terugknop" href="index.php">terug</a>
        <!-- Tabel met boekingen, boekingen waarvan de aankomstdatum niet in het verleden mag liggen deze worden binnengehaald via tblersoon voor het geval dat er een persoon is met meerdere boekingen !VOOR EXACT DEZELFDE DAGEN AANKOMST EN VERTREK) -->
        <table class="boekingen">
            <tr>
                <th>Boeking ID</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Telefoonnummer</th>
                <th>Aankomstdatum</th>
                <th>Vertrekdatum</th>
                <th>Kamer</th>
                <th>Acties</th>
            </tr>
            <?php
            $sql = "SELECT * 
                    FROM tblboeking 
                    INNER JOIN tblpersoon ON tblboeking.PersoonFK = tblpersoon.PKPersoon
                    INNER JOIN tblkamer ON tblboeking.KamerFK = tblkamer.PKKamer
                    WHERE tblboeking.Check_out >= CURDATE() AND Checked_in = '0'
                    ORDER BY tblboeking.Check_in ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#" . $row['PKBoeking'] . "</td>";
                    echo "<td>" . $row['Voornaam'] . " " . $row['Naam'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['Telefoon'] . "</td>";
                    echo "<td>" . $row['Check_in'] . "</td>";
                    echo "<td>" . $row['Check_out'] . "</td>";
                    echo "<td>" . $row['KamerNaam'] . "</td>";
                    echo "<td><a href='utility/boekingfuncties.php?checkin=" . $row['PKBoeking'] . "'>Inchecken</a></td>";
                    echo "<td><a href='utility/boekingfuncties.php?delete=" . $row['PKBoeking'] . "'>Verwijderen</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Geen boekingen gevonden.</td></tr>";
            }
            ?>
        </table>
    </section>
    <section class="dashcontainer">
        <!-- Als een boeking is ingechecked en nog niet voorbij de Check_out datum is zet deze hier neer -->
        <h1>Ingecheckte boekingen</h1>
        <table>
            <tr>
                <th>Boeking ID</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Telefoonnummer</th>
                <th>Aankomstdatum</th>
                <th>Vertrekdatum</th>
                <th>Kamer</th>
                <th>Acties</th>
            </tr>
            <?php
            $sql = "SELECT * FROM tblboeking
                    INNER JOIN tblpersoon ON tblboeking.PersoonFK = tblpersoon.PKPersoon
                    WHERE tblboeking.Check_out >= CURDATE() AND Checked_in = '1'
                    ORDER BY tblboeking.Check_in ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#" . $row['PKBoeking'] . "</td>";
                    echo "<td>" . $row['Voornaam'] . " " . $row['Naam'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['Telefoon'] . "</td>";
                    echo "<td>" . $row['Check_in'] . "</td>";
                    echo "<td>" . $row['Check_out'] . "</td>";
                    echo "<td>" . $row['KamerFK'] . "</td>";
                    echo "<td><a href='utility/boekingfuncties.php?delete=" . $row['PKBoeking'] . "'>Verwijderen</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Geen boekingen gevonden.</td></tr>";
            }
            ?>
    </section>
</body>
</html>