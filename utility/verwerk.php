<?php
session_start();
include '../config/conn.php';

// Check of er minstens 1 kamer is geselecteerd om te reserveren op de sessionid in tblboeking where betaald is NULL
$sql = "SELECT * FROM tblboeking WHERE SessionID = '" . session_id() . "' AND betaald IS NULL";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<script>alert('U heeft geen kamers geselecteerd om te reserveren.');</script>";
    echo "<script>window.location = '../index.php';</script>";
    die();
}


// Wanneer de persoon op de checkout pagina komt en de persoonlijke info invult, wordt deze info in de database gestoken en wordt de persoon gekoppeld aan de boeking die vanaf dan betaald is.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['voornaam'])) {
    $voornaam = $_POST['voornaam'];
    $naam = $_POST['naam'];
    $geboortedatum = $_POST['geboortedatum'];
    $straat = $_POST['straat'];
    $huisnummer = $_POST['huisnummer'];
    $bus = $_POST['bus'];
    $gemeente = $_POST['gemeenteNaam'];
    $telefoon = $_POST['telefoon'];
    $email = $_POST['email'];

    // Haal gemeente uit data-list (data-pk is de value van de option)
    $gemeente = explode(" ", $gemeente);
    $gemeente = $gemeente[0];

    // Prepared statement om gemeente op te halen
    $stmt = $conn->prepare("SELECT PKGemeente FROM tblgemeente WHERE Gemeente = ?");
    $stmt->bind_param("s", $gemeente);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pkgemeente = $row['PKGemeente'];
    $stmt->close();

    // Prepared statement om gegevens in tblpersoon in te voegen
    $stmt = $conn->prepare("INSERT INTO tblpersoon (Voornaam, Naam, Geboortedatum, Straat, Huisnummer, Bus, GemeenteFK, Email, Telefoon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $voornaam, $naam, $geboortedatum, $straat, $huisnummer, $bus, $pkgemeente, $email, $telefoon);
    if ($stmt->execute()) {
        $persoonFK = $conn->insert_id;
        $stmt->close();

        // Prepared statement om tblboeking te updaten
        $stmt = $conn->prepare("UPDATE tblboeking SET PersoonFK = ?, betaald = 1 WHERE SessionID = ? AND betaald IS NULL AND PersoonFK IS NULL");
        $session_id = session_id();
        $stmt->bind_param("is", $persoonFK, $session_id);
        if ($stmt->execute()) {
            echo "<script>alert('Bedankt voor uw reservering.');</script>";
            echo "<script>window.location = '../index.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>