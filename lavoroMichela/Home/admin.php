<?php
session_start();

// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['username']) || $_SESSION['admin'] !== 'capo') {
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

// Funzione per ottenere il totale delle ore di lavoro di un utente per il mese corrente
function getTotalHoursForUser($conn, $userId) {
    // Ottieni il primo e l'ultimo giorno del mese corrente
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');

    // Query per ottenere il totale delle ore di lavoro per il mese corrente
    $sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) AS total_hours 
            FROM `time` 
            WHERE `user_id` = $userId 
            AND `date` BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_hours'];
    } else {
        return "0:00";
    }
}

// Query per ottenere tutti gli utenti dipendenti
$sqlUsers = "SELECT * FROM `users` WHERE `admin` = 'dipendente'";
$resultUsers = $conn->query($sqlUsers);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Stili CSS */
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

        .user-list {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }

        .user-list li {
            margin-bottom: 10px;
        }

        .total-hours {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Admin Panel</h1>

<ul class="user-list">
    <?php
    if ($resultUsers && $resultUsers->num_rows > 0) {
        while ($row = $resultUsers->fetch_assoc()) {
            $userId = $row['id'];
            $userName = $row['name'] . " " . $row['surname'];
            $totalHours = getTotalHoursForUser($conn, $userId);
            echo "<li>$userName - Total hours: <span class='total-hours'>$totalHours</span></li>";
        }
    } else {
        echo "<li>No users found.</li>";
    }
    ?>
</ul>

</body>
</html>

<?php
// Chiudi la connessione al database
$conn->close();
?>
