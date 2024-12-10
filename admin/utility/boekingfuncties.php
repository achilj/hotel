<?php
include '../../config/conn.php';

session_start();
// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}

// Als er een boeking wordt ingecheckt via de knop op de vorige pagina (post request) dan wordt de boeking ingecheckt door checked_in op 1 te zetten en door de checkin datum + tijd in te vullen met de huidige datum + tijd
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['checkin'])) {
    $sql = "UPDATE tblboeking SET Checked_in = 1, Check_in = NOW() WHERE PKBoeking = " . $_GET['checkin'];
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Boeking succesvol ingecheckt.');</script>";
        echo "<script>window.location = '../overzicht.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Vraag eerst of persoon zeker is dat hij/zij de boeking wil verwijderen voordat de boeking wordt verwijderd zorg ervoor dat de boeking niet per ongeluk wordt verwijderd
// Zorg er ook voor dat wanneer er op annuleren wordt gedrukt de boeking niet wordt verwijderd, maar de persoon wordt teruggestuurd naar de overzichtspagina
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    echo "<script>
    if (confirm('Weet je zeker dat je deze boeking wilt verwijderen?')) {
        window.location = 'boekingfuncties.php?deleteconfirmed=" . $_GET['delete'] . "';
    } else {
        window.location = '../overzicht.php';
    }
    </script>";
}

// Als de boeking wordt verwijderd dan wordt de boeking verwijderd uit de database
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['deleteconfirmed'])) {
    $sql = "DELETE FROM tblboeking WHERE PKBoeking = " . $_GET['deleteconfirmed'];
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Boeking succesvol verwijderd.');</script>";
        echo "<script>window.location = '../overzicht.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>