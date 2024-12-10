<?php
include '../../config/conn.php';

session_start();
// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}

// Als er een kamer wordt aangepast via het formulier op de vorige pagina (post request) dan wordt de kamer aangepast in de database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pkkamer = $_POST['pkkamer'];
    $kamer = $_POST['kamer'];
    $capaciteit = $_POST['capaciteit'];
    $prijs = $_POST['prijs'];
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $tv = isset($_POST['tv']) ? 1 : 0;
    $airco = isset($_POST['airco']) ? 1 : 0;

    $sql = "UPDATE tblkamer SET KamerNaam = '$kamer', Capaciteit = $capaciteit, Prijs = $prijs, Wifi = $wifi, Televisie = $tv, Airco = $airco WHERE PKKamer = $pkkamer";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Kamer succesvol aangepast.');</script>";
        echo "<script>window.location = '../kamer.php';</script>";
    } else {
        echo "<script>alert('Er is iets misgegaan.');</script>";
        echo "<script>window.location = '../kamer.php';</script>";
    }
}

// Haal kamer op uit de url en zet deze in een variabele
$kamerid = $_GET['id'];

// Geef de kamer weer in een formulier zodat deze kan worden aangepast en opgeslagen voeg onderaan ook een knop toe om de kamer te verwijderen
$sql = "SELECT * FROM tblkamer WHERE PKKamer = $kamerid";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Kamer aanpassen</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <?php
        if ($result->num_rows > 0) {
            echo "<h1>Kamer aanpassen: " . $row['KamerNaam'] . "</h1>";
        }
        else {
            echo "<script>alert('Kamer niet gevonden.');</script>";
            echo "<script>window.location = '../kamer.php';</script>";
        }
        ?>
        <a class="terugknop" href="../kamer.php">terug</a>

        <form action="kamerfuncties.php" method="post" class="kamertoevoegen">
            <input type="hidden" name="pkkamer" id="pkkamer" value="<?php echo $kamerid; ?>">
            <label for="kamer">Kamer naam:</label>
            <input type="text" name="kamer" id="kamer" value="<?php echo $row['KamerNaam']; ?>" required>
            <label for="capaciteit">Capaciteit</label>
            <input type="number" name="capaciteit" id="capaciteit" value="<?php echo $row['Capaciteit']; ?>" required>
            <label for="prijs">Prijs per nacht:</label>
            <input type="number" name="prijs" id="prijs" step="0.01" value="<?php echo $row['Prijs']; ?>" required>
            <label>Faciliteiten:</label>
            <label for="wifi">Wifi</label>
            <input type="checkbox" name="wifi" id="wifi" <?php if ($row['Wifi'] == 1) { echo "checked"; } ?>>
            <label for="tv">TV</label>
            <input type="checkbox" name="tv" id="tv" <?php if ($row['Televisie'] == 1) { echo "checked"; } ?>>
            <label for="airco">Airco</label>
            <input type="checkbox" name="airco" id="airco" <?php if ($row['Airco'] == 1) { echo "checked"; } ?>>
        <button type="submit">Opslaan</button>
    </section>
</body>
</html>