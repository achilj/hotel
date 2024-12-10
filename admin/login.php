<?php
session_start();
include('../config/conn.php');

//Check of gebruiker al is ingelogd
if(isset($_SESSION["loggedin"])) {
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check gebruikersnaam en wachtwoord in database en haal gebruiker op als deze bestaat, prepare statement
    $stmt = $conn->prepare('SELECT * FROM tbluser WHERE username = ? AND Wachtwoord = ?');
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check of er een gebruiker is gevonden
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['loggedin'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Gebruikersnaam en/of wachtwoord is onjuist.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <section class="loginform">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
    </section>
</body>
</html>