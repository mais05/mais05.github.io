<?php
session_start();

// Verifica che l'utente sia autenticato
if (!isset($_SESSION['username'])) {
    // Redirect alla pagina di login se l'utente non è autenticato
    header("Location: /lavoroMichela/Login/login.html");
    exit();
}

// Configurazione del database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ore_lavoro";

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera l'ID dell'utente dalla sessione
$user_id = $_SESSION['user_id'];

// Imposta il mese selezionato, se presente
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
$show_all = isset($_POST['show_all']) ? $_POST['show_all'] == 'true' : false;

// Query SQL per recuperare i record di 'time' per l'utente e il mese selezionato o tutti i record se show_all è attivo
if ($show_all) {
    $sqlTime = "SELECT * FROM time WHERE user_id = $user_id";
    $sqlTotalTime = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(time))) AS total_time FROM time WHERE user_id = $user_id";
} else {
    $sqlTime = "SELECT * FROM time WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'";
    $sqlTotalTime = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(time))) AS total_time FROM time WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'";
}

$resultTime = $conn->query($sqlTime);

$timeData = [];
if ($resultTime->num_rows > 0) {
    while($row = $resultTime->fetch_assoc()) {
        $timeData[] = $row;
    }
} else {
    echo "Nessun record trovato nella tabella 'time'.";
}

$resultTotalTime = $conn->query($sqlTotalTime);

$totalTime = "N/A";
if ($resultTotalTime->num_rows > 0) {
    $row = $resultTotalTime->fetch_assoc();
    $totalTime = $row['total_time'];
}

// Chiudi la connessione al database
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dati delle Ore Lavorate e Somma</title>
</head>
<body>

<h1>Dati delle Ore Lavorate e Somma</h1>
<form id="filterForm" method="post" action="">
    <label for="month">Seleziona Mese:</label>
    <input type="month" id="month" name="month" value="<?php echo $selected_month; ?>" required>
    <input type="hidden" id="showAllInput" name="show_all" value="<?php echo $show_all ? 'true' : 'false'; ?>">
    <button type="button" id="toggleButton" onclick="toggleShowAll()"><?php echo $show_all ? 'Mostra per Mese' : 'Mostra Tutto'; ?></button>
</form>

<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Ora di Inizio</th>
            <th>Ora di Fine</th>
            <th>Ore Lavorate</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($timeData as $row): ?>
            <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['ora_inizio']; ?></td>
                <td><?php echo $row['ora_fine']; ?></td>
                <td><?php echo $row['time']; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Somma Totale delle Ore</strong></td>
            <td><strong><?php echo $totalTime; ?></strong></td>
        </tr>
    </tbody>
</table>

<script>
    document.getElementById('month').addEventListener('change', function() {
        document.getElementById('showAllInput').value = 'false';
        document.getElementById('filterForm').submit();
    });

    function toggleShowAll() {
        const showAllInput = document.getElementById('showAllInput');
        showAllInput.value = showAllInput.value === 'true' ? 'false' : 'true';
        document.getElementById('filterForm').submit();
    }

    // Aggiorna lo stile del pulsante al caricamento della pagina
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggleButton');
        toggleButton.style.backgroundColor = toggleButton.innerHTML === 'Mostra per Mese' ? 'green' : 'red';
    });
</script>

<style>
    /* Stile generale */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    h1 {
        color: #007BFF;
        margin-bottom: 20px;
        text-align: center;
    }

    form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        width: 80%;
        max-width: 400px;
        text-align: left;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input[type="month"] {
        width: calc(100% - 100px);
        padding: 8px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        padding: 10px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
        margin-left: 8px;
        box-sizing: border-box;
    }

    button:hover {
        background-color: #0056b3;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 800px;
        background-color: white;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #007BFF;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }

    @media screen and (max-width: 600px) {
        form {
            flex-direction: column;
        }

        input[type="month"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
        }

        button {
            width: 100%;
            margin-left: 0;
        }
    }
</style>

</body>
</html>
