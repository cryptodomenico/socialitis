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
/*if (isset($_POST['destinatario']))
{
    $sender_id = $_SESSION['mioid']; 
$recipient_id = $_POST['destinatario'];
$status = 'pending'; // Stato iniziale della richiesta di amicizia
$action_user_id = $sender_id;
addfriend($sender_id,$recipient_id,$status,$action_user_id);
}*/
if (isset($_POST['destinatario'])) {
    $sender_id = $_SESSION['mioid']; 
    $recipient_id = $_POST['destinatario'];
    $status = 'pending'; // Stato iniziale della richiesta di amicizia
    $action_user_id = $sender_id;

    if (!friendshipExists($sender_id, $recipient_id)) {
        // Aggiungi l'amicizia solo se non esiste già
        if (addFriend($sender_id, $recipient_id, $status, $action_user_id)) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        // Amicizia già aggiunta
        echo 'already_added';
    }
}    

    /*if (!friendshipExists($sender_id, $recipient_id)) {
        // Aggiungi l'amicizia solo se non esiste già
        addFriend($sender_id, $recipient_id, $status, $action_user_id);
        echo "<script>alert('Amicizia inviata!'); window.location.href = 'main.php';</script>";
        exit;
    } else {
        echo "<script>alert('Errore nell'invio della richiesta di amicizia.');</script>";
        exit;
    }
} else {
    // Amicizia già aggiunta, mostra un messaggio all'utente
    echo "<script>alert('L\'amicizia è già stata aggiunta.'); window.location.href = 'main.php';</script>";
    exit;
}
/*} else {
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    header("Location:".  htmlspecialchars($referer));
    exit();
}*/

function friendshipExists($sender_id, $recipient_id) {
    // Implementa il controllo per verificare se l'amicizia esiste già nel database
    global $conn;
    $sql = "SELECT COUNT(*) AS count FROM friendship WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $sender_id, $recipient_id, $recipient_id, $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
   // echo '<script>alert("niggers");</script>';
    return $count > 0;
}
function addfriend($id_mittente,$id_destinatario,$status,$action_user_id)
{
    global $conn;
    $sql = "INSERT INTO friendship (user1_id, user2_id, status, action_user_id) VALUES (?, ?, ?, ?)";

// Preparazione della statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi",$id_mittente,$id_destinatario,$status,$action_user_id);
if ($stmt->execute()) {
  //  echo '<script>alert("fuck niggers lol")</script>';
    $stmt->close();
    return true;
} else {
    $stmt->close();
    return false;
}
}
?>