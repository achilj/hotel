<?php
include 'config/conn.php';
//Inladen Bedrijfsinformatie
include 'utility/bedrijfsinfo.php';

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo "<title>" . $info['Bedrijfsnaam'] . " | ONZE KAMERS</title>" ?>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <?php include 'utility/header.php'; ?>

    <main>
        <section class="kamerssec">
            <h2>Boek hier onze gezellige kamers</h2>
            <p>Onze kamers zijn van alle gemakken voorzien en zijn geschikt voor zowel zakelijke als particuliere gasten.</p>
            <?php
            $sql = "SELECT * FROM tblkamer";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='kamer'>";
                    //Selecteer foto van kamer met fotosoort 'main' (hoofdfoto) KamerFK = PKKamer als er geen foto is, laat dan een standaard foto zien
                    $sqlFoto = "SELECT * FROM tblimg WHERE KamerFK = " . $row['PKKamer'] . " AND FotoSoort = 'main'";
                    $resultFoto = $conn->query($sqlFoto);
                    $rowFoto = $resultFoto->fetch_assoc();
                    if ($resultFoto->num_rows > 0) {
                        echo "<img src='img/uploads/" . $rowFoto['FotoUrl'] . "' alt='Foto van " . $rowFoto['FotoNaam'] . "'>";
                    } else {
                        echo "<img src='img/placeholder.webp' alt='Standaard kamerfoto'>";
                    }
                    echo "<h3>" . $row['KamerNaam'] . "</h3>";
                    echo "<p>" . $row['Capaciteit'] . " Personen</p>";

                    echo "<p>€" . $row['Prijs'] . " per nacht</p>";
                    echo "<a href='reserveren.php?id=" . $row['PKKamer'] . "'>Reserveer nu</a>";
                    echo "</div>";
                }
            } else {
                echo "Geen kamers gevonden";
            }
            ?>
        </section>
    </main>

    <?php include 'utility/footer.php'; ?>
</body>
</html>