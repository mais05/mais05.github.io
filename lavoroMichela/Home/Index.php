<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /lavoroMichela/Login/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolo Tempo</title>
</head>
<body>

<h1>Calcolo Tempo</h1>
<form id="tempoForm" method="post" action="/lavoroMichela/db/chiamateDB.php">
    <label for="oraInizio">Ora di inizio:</label>
    <input type="time" id="oraInizio" name="oraInizio" required>

    <label for="oraFine">Ora di fine:</label>
    <input type="time" id="oraFine" name="oraFine" required>

    <label for="giorno">Giorno:</label>
    <input type="date" id="giorno" name="giorno" required>

    <button type="submit" name="invia_ore">Invia Ore</button>
</form>

<div class="form-buttons">
    <form method="post" action="/lavoroMichela/db/chiamateDB.php">
        <button type="submit" id="but1" name="imp">Impostazioni</button>
    </form>
    
    <form method="post" action="/lavoroMichela/Home/Rend.php">      
        <button type="submit" id="but2" name="rend">Rendimento</button>
    </form>
</div>

<script>
    // Controlla se c'è un messaggio di risposta dalla sessione
    <?php
    if (isset($_SESSION['insert_response'])) {
        $message = $_SESSION['insert_response'];
        // Elimina il messaggio dalla sessione per evitare di mostrare lo stesso messaggio più volte
        unset($_SESSION['insert_response']);
    ?>
        // Visualizza un alert con il messaggio ricevuto
        alert("<?php echo $message; ?>");
    <?php } ?>
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
        padding: 20px; /* Aggiunge padding al body per migliorare la leggibilità */
        box-sizing: border-box; /* Assicura che il padding sia incluso nella larghezza */
    }

    h1 {
        color: #007BFF;
        margin-bottom: 20px;
        text-align: center;
    }

    form {
        width: 80%;
        max-width: 400px;
        text-align: left;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input {
        width: calc(100%);
        padding: 8px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* Assicura che il padding sia incluso nella larghezza */
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
        box-sizing: border-box; /* Assicura che il padding sia incluso nella larghezza */
        margin-bottom: 10px; /* Aggiunge margine inferiore per separare i pulsanti */
    }

    button:hover {
        background-color: #0056b3;
    }

    .form-buttons {
        display: flex;
        justify-content: space-between;
        width: 80%;
        max-width: 400px;
    }

    .form-buttons form {
        width: 48%; /* Larghezza dei form, con spazio per margine */
    }

    .form-buttons button {
        width: 100%; /* Larghezza dei pulsanti all'interno dei form */
    }

    /* Media query per dispositivi con larghezza massima di 600px (es. telefoni) */
    @media screen and (max-width: 600px) {
        form, .form-buttons {
            width: 100%; /* Imposta la larghezza del form al 100% dello schermo */
        }

        input {
            width: 100%; /* Fai sì che gli input occupino tutta la larghezza disponibile */
            padding: 8px; /* Mantieni il padding */
            margin-bottom: 16px; /* Mantieni il margine inferiore */
        }

        .form-buttons {
            flex-direction: column; /* Imposta i pulsanti su colonne invece di righe */
        }

        .form-buttons form {
            width: 100%; /* Fai sì che i form occupino tutta la larghezza disponibile */
            margin: 5px 0; /* Aggiunge margine verticale per separare i form */
        }

        .form-buttons button {
            width: 100%; /* Fai sì che i pulsanti occupino tutta la larghezza disponibile */
        }

        button {
            width: 100%; /* Fai sì che i pulsanti occupino tutta la larghezza disponibile */
        }
    }
</style>

</body>
</html>
