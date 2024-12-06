<?php
session_start();
//Verwijder reservering uit de sessie
unset($_SESSION['boekingen']);
echo "<script>alert('Reservering is verwijderd.');window.location.href='../index.php';</script>";
?>