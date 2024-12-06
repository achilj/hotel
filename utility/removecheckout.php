<?php
session_start();
include '../config/conn.php';

$boeking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($boeking_id == 0) {
    die("Ongeldige boeking ID.");
}

$sql = "DELETE FROM tblboeking WHERE PKBoeking = $boeking_id";
$conn->query($sql);

header("Location: ../checkout.php");
?>