<?php
include('../config/conn.php');

session_start();
// Check of gebruiker is ingelogd
if(!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];

    $sql = "INSERT INTO tblblockdagen (Startdatum, Einddatum) 
            VALUES ('$startdate', '$enddate')";
    $conn->query($sql);
    echo "<script>alert('Sluitingsdatum toegevoegd: $startdate - $enddate');</script>";
    echo "<script>window.location = 'block.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | Gesloten</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="dashcontainer">
        <h1>SLuitingsdagen</h1>
        <a class="terugknop" href="index.php">Terug</a>
        <form method="post" action="block.php">
            <label for="startdate">Datum:</label>
            <input type="date" id="startdate" name="startdate" required>
            <br>
            <label for="enddate">Datum:</label>
            <input type="date" id="enddate" name="enddate" required>
            <br>
            <button type="submit">Block</button>
        </form>

        <table>
            <tr>
                <th>Startdatum</th>
                <th>Einddatum</th>
            </tr>
            <?php
            $sql = "SELECT * FROM tblblockdagen";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Startdatum'] . "</td>";
                    echo "<td>" . $row['Einddatum'] . "</td>";
                    echo "<td><a href='utility/deleteblock.php?id=" . $row['PKBlockdag'] . "'>Delete</a></td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </section>
</body>
</html>