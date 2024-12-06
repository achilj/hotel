<?php
include 'config/conn.php';

//Inladen Bedrijfsinformatie
include 'utility/bedrijfsinfo.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo "<title>" . $info['Bedrijfsnaam'] . " | BESTE ACCOMODATIES | LAAGSTE PRIJS</title>" ?>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <header class="absolute">
        <a href="index.php"><img src="img/SKYBNBLOGO.png" alt="Logo SKYBNB"></a>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="kamers.php">Kamers</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="homesec">
            <?php echo "<h1>WELKOM BIJ " . $info['Bedrijfsnaam'] . "</h1>" ?>
            <a href="kamers.php">BOEK NU</a>
        </section>
        <section class="homeinfo" id="contact">
            <article>
                <h2>Waar?!</h2>
            </article>
            <article>
                <address>
                    <?php
                    echo "<p>" . $info['Straat'] . " " . $info['Huisnummer'] . "</p>";
                    echo "<p>" . $info['Postcode'] . " " . $info['Gemeente'] . "</p>";
                    ?>
                </address>
                <p><?php echo $info['Telefoon'] ?></p>
            </article>
        </section>
    </main>

    <?php include 'utility/footer.php'; ?>
</body>
</html>