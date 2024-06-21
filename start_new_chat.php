<?php
// Connessione al database
$conn = new mysqli("localhost", "root", "", "socialitis");

// Verifica connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
session_start();
// Controlla se l'utente è loggato
if (!isset($_SESSION['mioid'])) {
    // Reindirizza l'utente alla pagina di accesso se non è loggato
    header("Location: login.html");
    exit();
}
else
{
    if (!isset($_POST))
    {
        header("Location: chat.php");
        exit();
    }
    else
    {
    $sender_id = $_SESSION['mioid']; 
    $recipient_id = $_POST['destinatario'];
}
}
// Ottieni l'ID del mittente (puoi sostituire con il metodo corretto per ottenere l'ID dell'utente loggato)
// Esempio: ID dell'utente loggato

// Ottieni l'ID del destinatario dalla richiesta POST
$contenuto="";
// Query per inserire una nuova conversazione nel database
/*$sql = "INSERT INTO conversazioni (mittente_id, destinatario_id, data_inizio) VALUES ($sender_id, $recipient_id, NOW())";*/
$sql_inserimento_messaggio = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
$stmt_inserimento_messaggio = $conn->prepare($sql_inserimento_messaggio);
$stmt_inserimento_messaggio->bind_param("iis", $sender_id, $recipient_id, $contenuto);
$stmt_inserimento_messaggio->execute();
        $stmt_inserimento_messaggio->close();
        
        //echo "Messaggio inviato con successo a $destinatario_username.";
$conn->close();
header("Location: chat.php");
exit();
?>
