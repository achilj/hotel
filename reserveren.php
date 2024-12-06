<?php
session_start();
include 'config/conn.php';
include 'utility/bedrijfsinfo.php';

$kamer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($kamer_id == 0) {
    die("Ongeldige kamer ID.");
}

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
        FROM tblboeking bk
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aankomst = $_POST['aankomst'];
    $vertrek = $_POST['vertrek'];
    $aantal_personen = $_POST['aantal_personen'];

    // Sla boeking op in de sessie
    if (!isset($_SESSION['boekingen'])) {
        $_SESSION['boekingen'] = [];
    }

    $_SESSION['boekingen'][] = [
        'kamer_id' => $kamer['PKKamer'],
        'kamer_naam' => $kamer['KamerNaam'],
        'aankomst' => $aankomst,
        'vertrek' => $vertrek,
        'aantal_personen' => $aantal_personen,
        'boekingsdatum' => date('Y-m-d H:i:s')
    ];

    // Redirect naar de winkelwagen
    header('Location: checkout.php');
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
