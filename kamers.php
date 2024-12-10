<?php
include 'config/conn.php';
include 'utility/bedrijfsinfo.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo "<title>" . $info['Bedrijfsnaam'] . " | ONZE KAMERS</title>" ?>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include 'utility/header.php'; ?>

    <main>
        <section class="kamerssec">
            <h2>Boek hier onze gezellige kamers</h2>
            <p>Onze kamers zijn van alle gemakken voorzien en zijn geschikt voor zowel zakelijke als particuliere gasten.</p>
            <article class="datumfilter">
                <!-- Een form waar je optioneel een checkin en checkout datum in stopt als filter zodat wanneer dit ingevuld is enkel de beschikbare kamers getoond worden -->
                <form method="get">
                    <label for="checkin">Check-in Datum:</label>
                    <input type="date" id="checkin" name="checkin">
                    <label for="checkout">Check-out Datum:</label>
                    <input type="date" id="checkout" name="checkout">
                    <button type="submit">Zoek</button>
                </form>
                <?php
                // Als er een checkin en checkout datum is ingevuld, dan worden enkel de kamers getoond die beschikbaar zijn tussen deze data anders worden alle kamers getoond
                if (isset($_GET['checkin']) && isset($_GET['checkout'])) {
                    $checkin = $_GET['checkin'];
                    $checkout = $_GET['checkout'];
                    $sql = "SELECT * FROM tblkamer
                            WHERE NOT EXISTS
                            (SELECT 1 FROM tblboeking WHERE KamerFK = PKKamer AND (
                            ('$checkin' < Check_out AND '$checkout' > Check_in)
                            ))";

                }
                else {
                    $sql = "SELECT * FROM tblkamer";
                }
                $result = $conn->query($sql);
                
                // Als er een checkin en checkout datum is ingevuld, dan wordt er een tekst getoond met de data die ingevuld zijn en een link om de filter te wissen
                if (isset($checkin) && isset($checkout)) {
                    echo "<p>Kamers beschikbaar tussen " . $checkin . " en " . $checkout . "</p>";
                    echo "<a href='kamers.php'>Wis filter</a>";
                }
                ?>
            </article>

            <!-- Hier worden de kamers getoond -->
            <article class="kamerlist">
            <?php
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
                    // Als een kamer wifi, televisie of airco heeft, laat dan een icoontje zien (van Font Awesome), anders niet (leeg) deze haal je uit de database met de kolommen Wifi, Televisie en Airco
                    echo "<p>";
                    if ($row['Wifi'] == 1) {
                        echo "<i class='fas fa-wifi'></i> ";
                    }
                    if ($row['Televisie'] == 1) {
                        echo "<i class='fas fa-tv'></i> ";
                    }
                    if ($row['Airco'] == 1) {
                        echo "<i class='fas fa-fan'></i>";
                    }
                    echo "</p>";
                    echo "<p>€" . $row['Prijs'] . " per nacht</p>";
                    echo "<p>Maximum " . $row['Capaciteit'] . " Personen</p>";
                    echo "<a href='reserveren.php?id=" . $row['PKKamer'] . "'>Reserveer nu</a>";
                    echo "</div>";
                }
            } else {
                echo "Geen kamers gevonden";
            }
            ?>
            </article>
        </section>
    </main>
    <?php include 'utility/footer.php'; ?>

    <script>
        // Script dat ervoor zorgt dat je niet in het verleden kan selecteren in de datumpickers en minstens 1 nacht moet verblijven dus + 1 dag
        let checkin = document.getElementById('checkin');
        let checkout = document.getElementById('checkout');
        let today = new Date().toISOString().split('T')[0];
        checkin.setAttribute('min', today);
        checkin.addEventListener('change', function() {
            let minDate = new Date(checkin.value);
            minDate.setDate(minDate.getDate() + 1);
            checkout.setAttribute('min', minDate.toISOString().split('T')[0]);
        });
    </script>
</body>
</html>