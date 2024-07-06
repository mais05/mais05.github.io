<?php
session_start();

//--------------------------------- Connessione al Database --------------------------------------------

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

//--------------------------------- Funzione di Autenticazione Utente ----------------------------------

function authenticateUser($conn, $user, $pass) {
    // Query per verificare l'utente
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login riuscito
        $userData = $result->fetch_assoc();
        $_SESSION['username'] = $user;
        $_SESSION['user_id'] = $userData['id']; // Memorizza l'ID dell'utente nella sessione
        $_SESSION['admin'] = $userData['admin']; // Memorizza il ruolo dell'utente nella sessione

        return true;
    } else {
        // Login fallito
        $_SESSION['login_error'] = 'Username o password errati';
        return false;
    }
}

//--------------------------------- Controllo del Login --------------------------------------------

// Sezione per gestire il login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Raccolta dei dati dal modulo
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Chiamata alla funzione di autenticazione
    $loginSuccess = authenticateUser($conn, $user, $pass);

    if ($loginSuccess) {
        // Verifica del ruolo dell'utente
        $isAdmin = $_SESSION['admin'] === 'capo';

        // Redirect in base al ruolo dell'utente
        if ($isAdmin) {
            header("Location: /lavoroMichela/Home/admin.php"); // Pagina per gli admin
        } else {
            header("Location: /lavoroMichela/Home/index.php"); // Pagina per i dipendenti
        }
        exit();
    } else {
        // Redirect alla pagina di login in caso di fallimento
        header("Location: /lavoroMichela/Login/login.html");
        exit();
    }
}

// Chiudi la connessione al database
$conn->close();

//------------------------invio ore---------------------------------------------------------------------

// Sezione per gestire l'invio delle ore
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['invia_ore'])) {
    // Verifica che l'utente sia autenticato
    if (!isset($_SESSION['username'])) {
        // Redirect alla pagina di login se l'utente non è autenticato
        header("Location: /lavoroMichela/Login/login.html");
        exit();
    }

    // Raccolta dei dati dal modulo
    $oraInizio = $_POST['oraInizio'];
    $oraFine = $_POST['oraFine'];
    $giorno = $_POST['giorno'];
    $userId = $_SESSION['user_id']; // Prendi l'ID dell'utente dalla sessione

    // Calcolo delle ore trascorse
    $datetimeInizio = new DateTime($giorno . ' ' . $oraInizio);
    $datetimeFine = new DateTime($giorno . ' ' . $oraFine);
    $interval = $datetimeInizio->diff($datetimeFine);
    $oreTrascorse = $interval->format('%H:%I'); // Formato HH:MM

    // Query per inserire i dati nella tabella time
    $sqlInsert = "INSERT INTO time (ora_inizio, ora_fine, date, time, user_id)
                  VALUES ('$oraInizio', '$oraFine', '$giorno','$oreTrascorse', '$userId')";

    if ($conn->query($sqlInsert) === TRUE) {
        $_SESSION['insert_response'] = "Ore inserite correttamente";
    } else {
        $_SESSION['insert_response'] = "Errore nell'inserimento delle ore: " . $conn->error;
    }

    // Redirect alla pagina index.php
    header("Location: /lavoroMichela/Home/index.php");
    exit();
}

//----------------------------------stampa delle ore----------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rend'])) {
    
    // Recupera l'ID dell'utente dalla sessione
    $user_id = $_SESSION['user_id'];

    // Query SQL per recuperare i dati dalla tabella 'time'
    $sql = "SELECT date, time FROM time WHERE user_id = $user_id";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        echo "Nessun risultato trovato.";
    }

    // Salva i dati nella sessione
    $_SESSION['db_data'] = $data;

    // Debug: stampa i dati salvati nella sessione per verifica
    echo "<pre>";
    print_r($_SESSION['db_data']);
    echo "</pre>";

    // Chiudi la connessione al database dopo aver recuperato i dati necessari
    $conn->close();

    // Redirect alla pagina Rend.php per visualizzare il grafico
    header("Location: /lavoroMichela/Home/Rend.php");
    exit();
}

$conn->close();
?>
