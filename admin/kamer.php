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
    <title>ADMIN | Kamers</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <h1>Kamers Aanpassen</h1>
        <a class="terugknop" href="index.php">Terug</a>
        <!-- Formulier om nieuwe kamer toe te voegen -->
        <form action="utility/kamertoevoegen.php" method="post" class="kamertoevoegen">
            <label for="kamer">Kamer naam:</label>
            <input type="text" name="kamer" id="kamer" required>
            <label for="capaciteit">Capaciteit</label>
            <input type="number" name="capaciteit" id="capaciteit" required>
            <label for="prijs">Prijs per nacht:</label>
            <input type="number" name="prijs" id="prijs" step="0.01" required>
            <label>Faciliteiten:</label>
            <label for="wifi">Wifi</label>
            <input type="checkbox" name="wifi" id="wifi">
            <label for="tv">TV</label>
            <input type="checkbox" name="tv" id="tv">
            <label for="airco">Airco</label>
            <input type="checkbox" name="airco" id="airco">
            <button type="submit">Toevoegen</button>
        </form>
        <table>
            <tr>
                <th>Naam</th>
                <th>Acties</th>
            </tr>
            <?php
            $sql = "SELECT * FROM tblkamer";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['KamerNaam'] . "</td>";
                    echo "<td><a href='utility/kamerfuncties.php?id=" . $row['PKKamer'] . "'>Wijzigen</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Geen kamers gevonden.</td></tr>";
            }
            ?>
        </table>
    </section>
</body>
</html>