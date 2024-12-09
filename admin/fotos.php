<?php
include '../config/conn.php';



if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM tblimg WHERE PKImg = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Foto succesvol verwijderd.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | Fotos</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <h1>Fotos Upload</h1>
        <form action="utility/fotosupload.php" method="post" enctype="multipart/form-data">
            <label for="fotoname">Naam van de foto:</label>
            <input type="text" name="fotoname" id="fotoname" required>
            <label for="file">Kies een bestand:</label>
            <input type="file" name="file" id="file" required>
            <label for="kamerfk">Kies een kamer:</label>
            <select name="kamerfk" id="kamerfk" required>
                <option value="">Kies een kamer</option>
                <?php
                $sql = "SELECT * FROM tblkamer";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['PKKamer'] . "'>" . $row['KamerNaam'] . "</option>";
                    }
                } else {
                    echo "Geen kamers gevonden";
                }
                ?>
            </select>
            <select name="fotosoort" id="fotosoort">
                <option value="main">Hoofdfoto</option>
                <option value="extra">Extra foto</option>
            </select>
            <button type="submit">Upload</button>
        </form>
        <h2>Geuploade fotos</h2>
        <div class="fotos">
            <?php
            $sql = "SELECT * FROM tblimg";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='foto'>";
                    echo "<img src='../img/uploads/" . $row['FotoUrl'] . "' alt='Foto van " . $row['FotoNaam'] . "'>";
                    echo "<p>" . $row['FotoNaam'] . "</p>";
                    echo "<p>" . $row['FotoSoort'] . "</p>";
                    echo "<a href='fotos.php?delete=" . $row['PKIMG'] . "'>Verwijder</a>";
                    echo "</div>";
                }
            } else {
                echo "Geen fotos gevonden";
            }
            ?>
        </div>
    </section>
</body>
</html>