<?php
session_start();
include 'config/conn.php';
include 'utility/bedrijfsinfo.php';

$kamer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($kamer_id == 0) {
    die("Ongeldige kamer ID.");
}

// Oude tijdelijke reserveringen checken en verwijderen
$sql = "DELETE FROM tblboeking 
        WHERE betaald IS NULL 
        AND PersoonFK IS NULL 
        AND Reservatie < DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
$conn->query($sql);

// Haal kamer informatie op
$sql = "SELECT * FROM tblkamer WHERE PKKamer = $kamer_id";
$result = $conn->query($sql);
$kamer = $result->fetch_assoc();

if (!$kamer) {
    die("Kamer niet gevonden.");
}

// Haal bezette periodes op
function haalBezettingenOp($conn, $kamer_id) {
    $bezettingen = [];
    // Haal boekingen op
    $sql = "
        SELECT Check_in, Check_out, KamerFK 
        FROM tblboeking
        WHERE KamerFK = $kamer_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $bezettingen[] = [
            'start' => $row['Check_in'],
            'end' => $row['Check_out']
        ];
    }

    // Haal blokkeerdagen op
    $sql = "SELECT Startdatum, Einddatum FROM tblblockdagen";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $bezettingen[] = [
            'start' => $row['Startdatum'],
            'end' => $row['Einddatum']
        ];
    }

    return $bezettingen;
}

// Haal bezettingen voor deze kamer op
$bezettingen = haalBezettingenOp($conn, $kamer_id);

// Reservering verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aankomst = $_POST['aankomst'] ?? '';
    $vertrek = $_POST['vertrek'] ?? '';
    $aantal_personen = isset($_POST['aantal_personen']) ? intval($_POST['aantal_personen']) : 0;

    if (!$aankomst || !$vertrek || $aantal_personen <= 0) {
        die("Ongeldige gegevens voor reservering.");
    }

    // Bereken het aantal nachten
    $aankomst_date = new DateTime($aankomst);
    $vertrek_date = new DateTime($vertrek);
    $interval = $aankomst_date->diff($vertrek_date);
    $aantal_nachten = $interval->days;

    // Bereken de totaalprijs
    $kamer_prijs = $kamer['Prijs'];
    $totaalprijs = $kamer_prijs * $aantal_nachten;

    // Voeg reservering toe aan de database
    $session_id = session_id(); // Huidige sessie-ID gebruiken
    $persoon_fk = null; // PersoonFK is voorlopig NULL, zoals in jouw oorspronkelijke query
    $sql = "INSERT INTO tblboeking 
            (PersoonFK, AantalPersonen, KamerFK, Check_in, Check_out, betaald, SessionID, TotaalPrijs) 
            VALUES 
            (?, ?, ?, ?, ?, NULL, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Voorbereiden van query mislukt: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("iiisssd", $persoon_fk, $aantal_personen, $kamer_id, $aankomst, $vertrek, $session_id, $totaalprijs);

    // Voer de query uit
    if ($stmt->execute()) {
        header("Location: checkout.php");
    } else {
        echo "<script>alert('Reservering mislukt.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo "<title>" . $info['Bedrijfsnaam'] . " | KAMER | " . strtoupper($kamer['KamerNaam']) . "</title>" ?>
    <link rel="stylesheet" href="style/styles.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr Theme Dark CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'utility/header.php'; ?>
    <main>
        <section class="reserveren">
            <form method="post">
                <h1>Reserveren Kamer: <?php echo htmlspecialchars($kamer['KamerNaam']); ?></h1>
                <label for="aankomst">Aankomst Datum:</label>
                <div class="datepicker-wrapper">
                    <input type="text" id="aankomst" name="aankomst" class="flatpickr-input" readonly>
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <br>
                <label for="vertrek">Vertrek Datum:</label>
                <div class="datepicker-wrapper">
                    <input type="text" id="vertrek" name="vertrek" class="flatpickr-input" readonly>
                    <i class="fas fa-calendar-alt"></i>
                </div>

                <br>
                <label for="aantal_personen">Aantal Personen:</label>
                <select id="aantal_personen" name="aantal_personen" required>
                    <?php for ($i = 1; $i <= $kamer['Capaciteit']; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <br>
                <button type="submit">Reserveren</button>
            </form>
        </section>
    </main>
    <?php include 'utility/footer.php'; ?>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const bezettingen = <?php echo json_encode($bezettingen); ?>;

        // Converteer bezette periodes naar een Flatpickr-compatibel formaat
        const disabledDates = bezettingen.map(b => ({
            from: b.start,
            to: b.end
        }));

        document.addEventListener('DOMContentLoaded', function() {
            const aankomstPicker = flatpickr("#aankomst", {
                disable: disabledDates,
                minDate: "today",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates) {
                    const vertrekPicker = flatpickr("#vertrek", {
                        disable: disabledDates,
                        minDate: new Date(selectedDates[0]).fp_incr(1), // Een dag na aankomst
                        dateFormat: "Y-m-d"
                    });
                }
            });
        });
    </script>
</body>
</html>
