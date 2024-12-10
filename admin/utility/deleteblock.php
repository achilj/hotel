<?php
include('../../config/conn.php');
session_start();
// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}

// Verkrijg id uit URL
$id = $_GET['id'];


// Verwijderen van sluitingsdatum
if (isset($id)) {
    $sql = "DELETE FROM tblblockdagen WHERE PKBlockdag = '$id'";
    $conn->query($sql);
    echo "<script>alert('Datum succesvol verwijderd.');</script>";
    echo "<script>window.location = '../block.php';</script>";
}
else {
    header('Location: ../index.php');
    echo "<script>alert('Datum verwijderen mislukt.');</script>";
    echo "<script>window.location = '../block.php';</script>";
}
?>