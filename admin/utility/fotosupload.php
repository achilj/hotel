<?php
include '../../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $target_dir = "../../img/uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["file"]["tmp_name"]);

    if ($check !== false && in_array($imageFileType, ['png', 'jpeg', 'webp', 'jpg', 'gif', 'svg', 'avif'])) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $fotoNaam = $_POST['fotoname'];
            $fotoUrl = basename($_FILES["file"]["name"]);
            $FotoSoort = $_POST['fotosoort'];
            $kamerfk = $_POST['kamerfk'];

            $sql = "INSERT INTO tblimg (KamerFK, FotoNaam, FotoUrl, FotoSoort) VALUES ('$kamerfk', '$fotoNaam', '$fotoUrl', '$FotoSoort')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Foto succesvol geupload.');</script>";
                echo "<script>window.location = '../index.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "<script>alert('Er is een fout opgetreden bij het uploaden van de foto.');</script>";
        }
    } else {
        echo "<script>alert('Het bestand is geen afbeelding of het bestandstype is niet toegestaan.');</script>";
    }
}
?>