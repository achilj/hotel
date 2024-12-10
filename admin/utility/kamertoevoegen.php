<?php
include '../../config/conn.php';

session_start();
// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}

// Als er een kamer wordt toegevoegd via het formulier op de vorige pagina (post request) dan wordt de kamer toegevoegd aan de database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kamer = $_POST['kamer'];
    $capaciteit = $_POST['capaciteit'];
    $prijs = $_POST['prijs'];
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $tv = isset($_POST['tv']) ? 1 : 0;
    $airco = isset($_POST['airco']) ? 1 : 0;

    $sql = "INSERT INTO tblkamer (KamerNaam, Capaciteit, Prijs, Wifi, Televisie, Airco) VALUES ('$kamer', $capaciteit, $prijs, $wifi, $tv, $airco)";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Kamer succesvol toegevoegd.');</script>";
        echo "<script>window.location = '../kamer.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>