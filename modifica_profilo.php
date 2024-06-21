<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
if (!isset($_SESSION['mioid'])) {
    // Reindirizza l'utente alla pagina di accesso se non è loggato
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialitis";

$conn = new mysqli($servername, $username, $password, $dbname);
// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
$mio_id = $_SESSION['mioid'];


// Funzione per modificare il profilo
function modifica_profilo($conn, $mio_id, $nuovo_nome, $nuovo_cognome, $nuovo_bio, $nuova_email, $nuovo_username, $nuova_password)
{
    // Query SQL di base per l'aggiornamento del profilo
    $sql = "UPDATE users SET";

    // Verifica quali campi sono stati forniti e aggiungili alla query SQL
    $aggiornamenti = array();
    if (!empty($nuovo_nome)) {
        $aggiornamenti[] = " nome = ?";
    }
    if (!empty($nuovo_cognome)) {
        $aggiornamenti[] = " cognome = ?";
    }
    if (!empty($nuovo_bio)) {
        $aggiornamenti[] = " bio = ?";
    }
    if (!empty($nuova_email)) {
        $aggiornamenti[] = " email = ?";
    }
    if (!empty($nuovo_username)) {
        $aggiornamenti[] = " username = ?";
    }
    if (!empty($nuova_password)) {
        // Assicurati di crittografare la nuova password prima di aggiungerla alla query SQL
        $password_hash = password_hash($nuova_password, PASSWORD_DEFAULT);
        $aggiornamenti[] = " password = ?";
    }

    // Unisci gli aggiornamenti nella query SQL
    $sql .= implode(",", $aggiornamenti);

    // Aggiungi la clausola WHERE per identificare l'utente da aggiornare
    $sql .= " WHERE id = ?";

    // Prepara la query SQL
    $stmt = $conn->prepare($sql);

    // Associa i parametri della query
    $param_tipi = "";
    $param_valori = array();
    if (!empty($nuovo_nome)) {
        $param_tipi .= "s";
        $param_valori[] = $nuovo_nome;
    }
    if (!empty($nuovo_cognome)) {
        $param_tipi .= "s";
        $param_valori[] = $nuovo_cognome;
    }
    if (!empty($nuovo_bio)) {
        $param_tipi .= "s";
        $param_valori[] = $nuovo_bio;
    }
    if (!empty($nuova_email)) {
        $param_tipi .= "s";
        $param_valori[] = $nuova_email;
    }
    if (!empty($nuovo_username)) {
        $param_tipi .= "s";
        $param_valori[] = $nuovo_username;
    }
    if (!empty($nuova_password)) {
        $param_tipi .= "s";
        $param_valori[] = $password_hash;
    }
    $param_tipi .= "i"; // Aggiungi il tipo di parametro per l'ID
    $param_valori[] = $mio_id;

    // Bind dei parametri
    $bind_params = array_merge(array($param_tipi), $param_valori);
    $stmt->bind_param(...$bind_params);

    // Esegui la query preparata
    if ($stmt->execute()) {
        $stmt->close();
        return true; // Ritorna true se l'aggiornamento è avvenuto con successo 
            }
    else {
        $stmt->close();
        return false; // Ritorna false se c'è stato un errore durante l'aggiornamento
    }

    // Chiudi lo statement
  
}

// Utilizzo della funzione per modificare il profilo
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nuovo_username'])) {
    // Prendi i valori inviati dal modulo
    $nuovo_nome = isset($_POST['nuovo_nome']) ? $_POST['nuovo_nome'] : '';
    $nuovo_cognome = isset($_POST['nuovo_cognome']) ? $_POST['nuovo_cognome'] : '';
    $nuovo_bio = isset($_POST['nuovo_bio']) ? $_POST['nuovo_bio'] : '';
    $nuova_email = isset($_POST['nuova_email']) ? $_POST['nuova_email'] : '';
    $nuovo_username = isset($_POST['nuovo_username']) ? $_POST['nuovo_username'] : '';
    $nuova_password = isset($_POST['nuova_password']) ? $_POST['nuova_password'] : '';

    // Chiamata alla funzione modifica_profilo
    if (modifica_profilo($conn, $mio_id, $nuovo_nome, $nuovo_cognome, $nuovo_bio, $nuova_email, $nuovo_username, $nuova_password)) {
        echo "Profilo aggiornato con successo!";
    } else {
        echo "Errore durante l'aggiornamento del profilo.";
    }
} else {
    // Il modulo non è stato inviato o non contiene dati effettivi
    echo "Nessun dato inviato per l'aggiornamento del profilo.";
}

?>

<html>
<html>
    <head>
    <title>Profilo di <?= isset($_POST['username_destinatario']) ? htmlspecialchars($_POST['username_destinatario']) : htmlspecialchars($_SESSION['username']); ?></title>
        <style>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center; /* Centra tutto il contenuto all'interno del container */
}

.post {
    background-color: #fff;
    border: 1px solid #ccc;
    margin-bottom: 20px;
    padding: 20px;
    text-align: left; /* Allinea il testo del post a sinistra */
}

label {
    display: block;
    margin-bottom: 10px;
}

input[type="text"] {
    display: block;
    margin: 0 auto 10px;
    width: 100%; /* Utilizza tutto lo spazio disponibile */
}

.btn-modifica {
  font-size:16px;
  font-family:Arial;
  padding: 5px 8px;
  border-width:1px;
  color:#fff;
  border-color:rgba(74, 144, 226, 1);
  border-top-left-radius:28px;
  border-top-right-radius:28px;
  border-bottom-left-radius:28px;
  border-bottom-right-radius:28px;
  text-shadow: 1px 1px 0px rgba(74, 144, 226, 1);
  background:rgba(74, 144, 226, 1);
}

.btn-modifica:hover {
  background: rgba(74, 144, 226, 1)
}
    </style>
    </head>
 <body>
    <div class="container">
        <div class="post">
        <h1 style="text-align: center;">Modifica profilo</h1>
        <div class="post-header">
        <form action="modifica_profilo.php" method="post">
        <label for="Nome">Nome:</label>
        <input type="text" placeholder="Nome" id="nuovo_nome" name="nuovo_nome">
     
        <label for="Cognome" style="text-align: left;">Cognome:</label>
        <input type="text" placeholder="Cognome" id="nuovo_cognome" name="nuovo_cognome">
         
        <label for="Bio" style="text-align: left;">Bio:</label>
        <input type="text" placeholder="Bio" id="nuovo_bio" name="nuovo_bio">
        
        <label for="Email" style="text-align: left;">Email:</label>
        <input type="text" placeholder="E-mail" id="nuovo_email" name="nuovo_email">
        
        <label for="Username" style="text-align: left;">Username:</label>
        <input type="text" placeholder="Username" id="nuovo_username" name="nuovo_username">
        
        <label for="Password" style="text-align: left;">Password:</label>
        <input type="text" placeholder="Password" id="nuovo_password" name="nuovo_password">
        <input type="submit" class="btn-modifica"  value="Conferma modifiche">
        </form>
        </div>
        </div>
    </div>
 </body>
</html>