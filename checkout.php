<?php
session_start();

include 'config/conn.php';
include 'utility/bedrijfsinfo.php';

// Gemeentes binnenhalen
$sqlGemeente = "SELECT * FROM tblgemeente";
$resultGemeente = mysqli_query($conn, $sqlGemeente);
$GemeneeteRow = mysqli_fetch_assoc($resultGemeente);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo "<title>" . $info['Bedrijfsnaam'] . " | BOEKING AFRONDEN</title>" ?>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <?php include"utility/header.php";
     ?>
    <section class="checkoutcontainer">
    <h2>Kamers:</h2>
    <?php
    //Haal alle kamers uit de database van die session id en zet ze in een array enkel als tblboeking.betaald NULL is. Wanneer je de resevatie afrond wordt deze op betaald gezet. Koppel de reservatie aan de persoon die eerst wordt aangemaakt.
    
    ?>
    </section>

    <section class="checkoutcontainer">
        <h2>Persoonlijke Info:</h2>
        <article class="persinfo">
            <form method="post" action="utility/verwerk.php">
                <label for="voornaam">Vooraam:</label>
                <input type="text" name="voornaam" id="voornaam" required>
                <label for="naam">Naam:</label>
                <input type="text" name="naam" id="naam" required>
                <label for="geboortedatum">Geboortedatum:</label>
                <input type="date" name="geboortedatum" id="geboortedatum" required>
                <label for="straat">Straat:</label>
                <input type="text" name="straat" id="straat" required>
                <label for="huisnummer">Huisnummer:</label>
                <input type="text" name="huisnummer" id="huisnummer" required>
                <label for="busnummer">Busnummer:</label>
                <input type="text" name="busnummer" id="busnummer">
                <label for="gemeente">Gemeente:</label>
                <input list="gemeenteLijst" id="gemeenteNaam" name="gemeenteNaam" 
                value="<?php echo $GemeneeteRow['Gemeente'] . ' ' . $GemeneeteRow['Postcode']; ?>" 
                oninput="updateGemeenteID()" autocomplete="off">
                <datalist id="gemeenteLijst">
                <?php
                    while ($r = mysqli_fetch_assoc($resultGemeente)) {
                        echo "<option value='" . $r["Gemeente"] . " " . $r["Postcode"] . "' data-pk='" . $r["PKGemeente"] . "'>";
                    }
                ?>
                </datalist>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <label for="telefoon">Telefoon:</label>
                <input type="tel" name="telefoon" id="telefoon" required>
                <button type="submit">Reservering Afronden</button>
            </form>
        </article>
    </section>
    <?php include 'utility/footer.php'; ?>

    <script>
        function updateGemeenteID() {
            const gemeenteNaam = document.getElementById('gemeenteNaam');
            const gemeenteID = document.getElementById('gemeenteID');
            const option = Array.from(document.getElementById('gemeenteLijst').options)
                .find(option => option.value === gemeenteNaam.value);
            if (option) {
                gemeenteID.value = option.getAttribute('data-pk');
            } else {
                gemeenteID.value = '';
            }
        }
    </script>
</body>
</html>
