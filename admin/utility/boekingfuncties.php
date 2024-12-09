<?php
include '../../config/conn.php';

// Als er een boeking wordt ingecheckt via de knop op de vorige pagina (post request) dan wordt de boeking ingecheckt door checked_in op 1 te zetten
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['checkin'])) {
    $sql = "UPDATE tblboeking SET Checked_in = 1 WHERE PKBoeking = " . $_GET['checkin'];
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Boeking succesvol ingecheckt.');</script>";
        echo "<script>window.location = '../overzicht.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Als er een boeking wordt verwijderd via de knop op de vorige pagina (post request) dan wordt de boeking verwijderd
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $sql = "DELETE FROM tblboeking WHERE PKBoeking = " . $_GET['delete'];
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Boeking succesvol verwijderd.');</script>";
        echo "<script>window.location = '../overzicht.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>