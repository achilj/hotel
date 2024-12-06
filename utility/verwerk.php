<?php
include '../config/conn.php';
session_start();

// Controleer of de sessie variabele 'boekingen' bestaat
if (!isset($_SESSION['boekingen']) || empty($_SESSION['boekingen'])) {
    echo "Er zijn geen reserveringen om te bevestigen.";
    exit;
}

// Zet autocommit uit voor transacties
mysqli_autocommit($conn, false);

try {
    // Stap 1: Voeg de persoon toe aan tblpersoon
    if (isset($_POST['voornaam'], $_POST['naam'], $_POST['geboortedatum'], $_POST['straat'], $_POST['huisnummer'], $_POST['gemeenteNaam'], $_POST['email'], $_POST['telefoon'])) {
        // Verkrijg de gegevens van het formulier
        $voornaam = $_POST['voornaam'];
        $naam = $_POST['naam'];
        $geboortedatum = $_POST['geboortedatum'];
        $straat = $_POST['straat'];
        $huisnummer = $_POST['huisnummer'];
        $busnummer = $_POST['busnummer'] ?? ''; // Optioneel
        $gemeenteNaam = $_POST['gemeenteNaam'];
        $email = $_POST['email'];
        $telefoon = $_POST['telefoon'];

        // Verkrijg de GemeenteFK door de naam en postcode op te splitsen
        $gemeente_onderdelen = explode(' ', $gemeenteNaam);
        $gemeente_postcode = array_pop($gemeente_onderdelen);
        $gemeente_naam = implode(' ', $gemeente_onderdelen);

        $sql_gemeente_fk = "SELECT PKGemeente FROM tblgemeente WHERE Gemeente = ? AND Postcode = ?";
        $stmt_gemeente = mysqli_prepare($conn, $sql_gemeente_fk);
        mysqli_stmt_bind_param($stmt_gemeente, "si", $gemeente_naam, $gemeente_postcode);
        mysqli_stmt_execute($stmt_gemeente);
        mysqli_stmt_bind_result($stmt_gemeente, $gemeente_fk);
        mysqli_stmt_fetch($stmt_gemeente);
        mysqli_stmt_close($stmt_gemeente);

        if (!$gemeente_fk) {
            throw new Exception("Gemeente niet gevonden: $gemeenteNaam");
        }

        // Voeg de persoon toe aan de database
        $sql_persoon = "INSERT INTO tblpersoon (Voornaam, Naam, Geboortedatum, Straat, Huisnummer, Bus, GemeenteFK, Email, Telefoon) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_persoon = mysqli_prepare($conn, $sql_persoon);
        mysqli_stmt_bind_param($stmt_persoon, "ssssssiss", $voornaam, $naam, $geboortedatum, $straat, $huisnummer, $busnummer, $gemeente_fk, $email, $telefoon);

        if (!mysqli_stmt_execute($stmt_persoon)) {
            throw new Exception("Fout bij het toevoegen van persoon: " . mysqli_error($conn));
        }

        // Verkrijg de laatst toegevoegde persoon ID
        $persoon_id = mysqli_insert_id($conn);
    }

    // Stap 2: Voeg reserveringen toe aan tblboeking
    foreach ($_SESSION['boekingen'] as $boeking) {
        $checked_in = 0; // Default value
        $sql_boeking = "INSERT INTO tblboeking (PersoonFK, AantalPersonen, TotaalPrijs, Boekingsdatum, Check_in, Check_out, Checked_in) 
                        VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
        $stmt_boeking = mysqli_prepare($conn, $sql_boeking);
        mysqli_stmt_bind_param($stmt_boeking, "iiissis", $persoon_id, $boeking['aantal_personen'], $boeking['totaal_prijs'], $boeking['aankomst'], $boeking['vertrek'], $checked_in);

        if (!mysqli_stmt_execute($stmt_boeking)) {
            throw new Exception("Fout bij het toevoegen van boeking: " . mysqli_error($conn));
        }

        // Verkrijg de laatst toegevoegde boeking ID
        $boeking_id = mysqli_insert_id($conn);

        // Stap 3: Koppel de boeking aan de kamers in tblboekingkamers
        $sql_boeking_kamers = "INSERT INTO tblboekingkamers (KamerFK, BoekingFK, AantalPersonen) 
                               VALUES (?, ?, ?)";
        $stmt_boeking_kamers = mysqli_prepare($conn, $sql_boeking_kamers);

        foreach ($boeking['kamers'] as $kamer_id) {
            mysqli_stmt_bind_param($stmt_boeking_kamers, "iii", $kamer_id, $boeking_id, $boeking['aantal_personen']);
            if (!mysqli_stmt_execute($stmt_boeking_kamers)) {
                throw new Exception("Fout bij het koppelen van kamer: " . mysqli_error($conn));
            }
        }
    }

    // Commit de transactie
    mysqli_commit($conn);

    // Verwijder de boekingen uit de sessie
    unset($_SESSION['boekingen']);
    echo "<script>alert('Reservering succesvol afgerond!'); window.location.href='index.php';</script>";
} catch (Exception $e) {
    // Rol terug bij fouten
    mysqli_rollback($conn);
    echo "Er is een fout opgetreden: " . $e->getMessage();
}

// Zet autocommit weer aan
mysqli_autocommit($conn, true);
?>
