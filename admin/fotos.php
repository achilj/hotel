<?php
include '../config/conn.php';


if (isset($_GET['delete'])) {
    // Check of de foto bestaat in de map, en verwijder bestand als deze nog bestaat voordat de database entry wordt verwijderd
    $sql = "SELECT * FROM tblimg WHERE PKIMG = " . $_GET['delete'];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file = "../img/uploads/" . $row['FotoUrl'];
        if (file_exists($file)) {
            unlink($file);
        }
        
        $sql = "DELETE FROM tblimg WHERE PKIMG = " . $_GET['delete'];
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Foto succesvol verwijderd.');</script>";
            echo "<script>window.location = 'fotos.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "<script>alert('Foto niet gevonden.');</script>";
    }
}

$filterKamer = "";
if (isset($_GET['kamerfilter']) && $_GET['kamerfilter'] !== "") {
    $filterKamer = intval($_GET['kamerfilter']);
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
        <label for="filter">Filter op kamer:</label>
        <form method="GET" action="fotos.php">
            <select name="kamerfilter" id="kamerfilter" onchange="this.form.submit()">
                <option value="">Alle kamers</option>
                <?php
                $sql = "SELECT * FROM tblkamer";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $selected = ($filterKamer == $row['PKKamer']) ? "selected" : "";
                        echo "<option value='" . $row['PKKamer'] . "' $selected>" . $row['KamerNaam'] . "</option>";
                    }
                } else {
                    echo "Geen kamers gevonden";
                }
                ?>
            </select>
        </form>
        <div class="fotos">
            <?php
            $sql = "SELECT * FROM tblimg";
            if ($filterKamer) {
                $sql .= " WHERE KamerFK = $filterKamer";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='foto'>";
                    echo "<img src='../img/uploads/" . $row['FotoUrl'] . "' alt='Foto van " . $row['FotoNaam'] . "'>";
                    echo "<h3>" . $row['FotoNaam'] . "</h3>";
                    echo "<p>Kamer: " . $row['KamerFK'] . "</p>";
                    echo "<p>Soort: " . $row['FotoSoort'] . "</p>";
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