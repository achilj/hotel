<?php
include '../config/conn.php';

// Wanneer de persoon op de checkout pagina komt en de persoonlijke info invult wordt deze info in de database gestoken en wordt de persoon gekoppeld aan de boeking die vanaf dan betaald is.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['voornaam'])) {
    $voornaam = $_POST['voornaam'];
    $naam = $_POST['naam'];
    $geboortedatum = $_POST['geboortedatum'];
    $straat = $_POST['straat'];
    $gemeente = $_POST['gemeente'];
    $postcode = $_POST['postcode'];
    $telefoon = $_POST['telefoon'];
    $email = $_POST['email'];

    $sql = "INSERT INTO tblpersoon (Voornaam, Naam, Geboortedatum, Straat, GemeenteFK, Postcode, Telefoon, Email) VALUES ('$voornaam', '$naam', '$geboortedatum', '$straat', '$gemeente', '$postcode', '$telefoon', '$email')";
    if ($conn->query($sql) === TRUE) {
        $persoonFK = $conn->insert_id;
        // Update de boeking met de persoonFK en zet de boeking op betaald (betaald = 1)
        $sql = "UPDATE tblboeking SET PersoonFK = $persoonFK, betaald = 1 WHERE SessionID = '" . session_id() . "' AND betaald IS NULL AND PersoonFK IS NULL";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Bedankt voor uw reservering.');</script>";
            echo "<script>window.location = '../index.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>