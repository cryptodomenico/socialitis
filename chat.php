
<?php
//session_start(); // Inizializzazione della sessione

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialitis";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
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
    $idmittente = $_SESSION['mioid'];
    $miousername = $_SESSION['username'];
    echo "<div class='profile-info'>";
echo "    <div class='profile-image'>";
$image_path = 'profile_images/' . htmlspecialchars($miousername) . '.jpg';
if (file_exists($image_path)) {
    echo "        <img src='$image_path' alt='Profile Image'>";
} else {
    echo "        <img src='profile_images/guest.jpg' alt='negro'>";
}
echo "    </div>";
echo "    <div class='username-container'>";
echo "        <span class='username'>" . htmlspecialchars($miousername) . "</span>";

echo "    </div>";
echo '  <a href="logout.php" class="btn-logout" style="border-radius: 0; height: 20px;"><img src="icons/logout_icon.png" alt="btn-logout" style="border-radius: 0; height: 37px;"></a>';
echo '      <a href="main.php" class="btn-home"  style="border-radius: 0;"><img src="icons/home_icon.png" alt="Home"  style="border-radius: 0;"></a>';
echo '      <a href="profile.php" class="btn-profile"  style="border-radius: 0;"><img src="icons/profile.png" alt="Home"  style="border-radius: 0;"></a>';

echo "</div>";

   // echo "<img src='profile_images/" .htmlspecialchars($miousername) .".jpg' style='width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;' alt='Bruh'>";
    //echo "<span style='align-items: center;'>" . htmlspecialchars($miousername) . "</span>";
}
 if (isset($_GET['interlocutore'])) {
    $interlocutore = $_GET['interlocutore'];
 }
// Funzione per ottenere l'elenco degli utenti con cui l'utente corrente ha avuto una conversazione
/*if (isset($_POST['invia_messaggio'])) {
    
    // Verifica se i campi sono stati compilati
    if (isset($_POST['destinatario'], $_POST['contenuto'])) {
        // Prendi i dati dal modulo
        $idmittente = $_SESSION['mioid'];
        $destinatario_id = $_POST['destinatario'];
        $contenuto = $_POST['contenuto'];
        // Invia il messaggio
        inviaMessaggio($idmittente, $destinatario_id, $contenuto); // Il mittente è fisso come ID 1 per ora
        // Ora puoi ricaricare la pagina per visualizzare i nuovi messaggi o aggiornare dinamicamente la visualizzazione tramite JavaScript
       header("Location: chat.php?interlocutore=$destinatario_id"); // Ricalcola la pagina
        
        exit; // Termina lo script
    } else {
        echo "Compilare tutti i campi!";
    }
}*/
// Se è stata inviata una richiesta AJAX per inviare un messaggio
if (isset($_POST['invia_messaggio']) && isset($_POST['destinatario']) && isset($_POST['contenuto'])) {
    $destinatario_id = $_POST['destinatario'];
    $contenuto = $_POST['contenuto'];
    inviaMessaggio($idmittente, $destinatario_id, $contenuto); // Invia il messaggio

    // Ottieni i messaggi aggiornati per l'interlocutore
    $messaggi_aggiornati = getMessaggi($destinatario_id);
    
    // Converti i messaggi in formato JSON e inviali come risposta
    echo json_encode($messaggi_aggiornati);
    exit; // Termina lo script dopo aver inviato la risposta AJAX
}

function getInterlocutori() {
    global $conn;
    $idmittente = $_SESSION['mioid']; // Ottieni l'ID del mittente dalla sessione
    $sql = "SELECT DISTINCT u.id, u.username AS interlocutore
            FROM messages m
            JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id)
            WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $idmittente, $idmittente, $idmittente);
    $stmt->execute();
    $result = $stmt->get_result();
    $interlocutori = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $interlocutori[] = $row;
        }
    }
    $stmt->close();
    return $interlocutori;
}

/*function getInterlocutori() {
    global $conn;
    $idmittente = $_SESSION['mioid'];//$_SESSION['mioid']; // Ottieni l'ID del mittente dalla sessione
    $sql = "SELECT DISTINCT u.id, u.username AS interlocutore
            FROM messages m
            JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id)
            WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $idmittente, $idmittente, $idmittente);
    $stmt->execute();
    $result = $stmt->get_result();
    $interlocutori = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $interlocutori[] = $row;
        }
    }
    $stmt->close();
    return $interlocutori;
}*/

// Funzione per ottenere i messaggi tra l'utente corrente e un interlocutore specifico
function inviaMessaggio($mittente_id, $destinatario_id, $contenuto) {
    global $conn;
    
    // Trova l'username del destinatario basato sull'ID
    $sql_username_destinatario = "SELECT username FROM users WHERE id = ?";
    $stmt_username_destinatario = $conn->prepare($sql_username_destinatario);
    $stmt_username_destinatario->bind_param("i", $destinatario_id);
    $stmt_username_destinatario->execute();
    $result_username_destinatario = $stmt_username_destinatario->get_result();
    
    // Se l'utente con l'ID fornito esiste
    if ($result_username_destinatario->num_rows > 0) {
        $row = $result_username_destinatario->fetch_assoc();
        $destinatario_username = $row['username'];
        
        // Inserisci il messaggio nel database
        $sql_inserimento_messaggio = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        $stmt_inserimento_messaggio = $conn->prepare($sql_inserimento_messaggio);
        $stmt_inserimento_messaggio->bind_param("iis", $mittente_id, $destinatario_id, $contenuto);
        $stmt_inserimento_messaggio->execute();
        $stmt_inserimento_messaggio->close();
        
        echo "Messaggio inviato con successo a $destinatario_username.";
    } else {
        echo htmlspecialchars("L'utente con ID $destinatario_id non esiste.");
    }

    $stmt_username_destinatario->close();
    getMessaggi($destinatario_id);
}

/*function inviaMessaggio($mittente_id, $destinatario_id, $contenuto) {
    global $conn;
    
    // Trova l'ID del destinatario basato sull'username
    $sql_username_destinatario = "SELECT username FROM users WHERE ID = ?";
    $stmt_username_destinatario = $conn->prepare($sql_username_destinatario);
    $stmt_username_destinatario->bind_param("i", $destinatario_id);
    $stmt_username_destinatario->execute();
    $result_username_destinatario = $stmt_username_destinatario->get_result();
    $destinatario_username = $result_username_destinatario;
    // Se l'utente con l'username fornito esiste
    if ($result_username_destinatario->num_rows > 0) {
        $row = $result_username_destinatario->fetch_assoc();
        $destinatario_id = $row['id'];
        
        // Inserisci il messaggio nel database
        $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $mittente_id, $destinatario_id, $contenuto);
        $stmt->execute();
        $stmt->close();
        
        echo "Messaggio inviato con successo a $destinatario_username.";
    } else {
        echo "L'utente con username $destinatario_username non esiste.";
    }
}*/
function getMessaggi($interlocutore_id) {
    global $conn;
    $idmittente = $_SESSION['mioid'];//$_SESSION['mioid']; // Ottieni l'ID del mittente dalla sessione
    $sql = "SELECT m.*, sender.username AS sender_username, receiver.username AS receiver_username
            FROM messages m
            JOIN users sender ON m.sender_id = sender.id
            JOIN users receiver ON m.receiver_id = receiver.id
            WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
            ORDER BY sent_at";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $idmittente, $interlocutore_id, $interlocutore_id, $idmittente);
    $stmt->execute();
    $result = $stmt->get_result();
    $messaggi = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messaggi[] = $row;
            
        }
    }
    $stmt->close();
    return $messaggi;
}

// Se è stato specificato un interlocutore, otteniamo i messaggi per quella conversazione
$interlocutore_id = null;
if (isset($_GET['interlocutore'])) {
    $interlocutore_id = $_GET['interlocutore'];
    $messaggi = getMessaggi($interlocutore_id);

}

// Ottieni l'elenco degli utenti con cui l'utente corrente ha avuto una conversazione
$interlocutori = getInterlocutori();

// Mostra l'elenco degli utenti con cui l'utente corrente ha avuto una conversazione
//echo "<h2>Elenco delle Conversazioni</h2>";
//echo "<ul>";
foreach ($interlocutori as $interlocutore) {
    
   // echo "<li><a href='chat.php?interlocutore={$interlocutore['id']}'>{$interlocutore['interlocutore']}</a></li>";
}
//echo "</ul>";

// Se è stato specificato un interlocutore, mostriamo i messaggi per quella conversazione
if ($interlocutore_id) {
    foreach ($interlocutori as $interlocutore) {
        if ($interlocutore['id'] == $interlocutore_id) {
          //  echo "<h2>Conversazione con {$interlocutore['interlocutore']}</h2>";
          
            break;
        }
    }

    foreach ($messaggi as $messaggio) {
    //    echo "Messaggio da " . $messaggio['sender_username'] . " a " . $messaggio['receiver_username'] . ": " . $messaggio['content'] . "<br>";
    }
}
// Chiudere la connessione
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}
.contenitore-conversazione {
    display: flex;
    flex: 1; /* Flessibilità per riempire lo spazio disponibile */
}

.contatti {
    flex: 1;
    background-color: #f0f0f0;
    overflow-y: auto;
    padding: 20px;
}
        .contatti ul {
            list-style-type: none;
            padding: 0;
        }
        .contatti ul li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .contatti ul li a {
            text-decoration: none;
            color: #333;
        }
        .contatti ul li a:hover {
            background-color: #ddd;
        }
        .conversazione-attiva {
    flex: 3;
    background-color: #fff;
    border-left: 1px solid #ccc;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}
.messaggio {
    margin-bottom: 20px;
}

.messaggio .mittente {
    font-weight: bold;
}

.input-message-container {
    padding: 20px;
    background-color: #f0f0f0;
    border-top: 1px solid #ccc;
}
    .input-message-container .input-message {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 10px;
    }
    .input-message-container .btn-send {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .profile-info {
    display: flex;
    align-items: center;
}

.profile-info .profile-image {
    position: relative;
    margin-right: 10px; /* Aggiungi margine tra l'immagine e l'username */
}

.profile-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.profile-info .username-container {
    flex: 1;
    display: flex;
    align-items: center;
}

.profile-info .username {
    font-size: 16px;
    font-weight: bold;
    margin-left: 5px; /* Aggiungi margine tra l'immagine e l'username */
}
.btn-logout {
            position: absolute;
            top: 3px;
            right: 55px;
            cursor: pointer;
            border-radius: 0;
            width: 28px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        .btn-home {
            position: absolute;
            top: 3px;
             right: 10px;
            border-radius: 0;
            cursor: pointer;
            width: 30px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        .btn-profile {
            position: absolute;
            top: 3px;
            right: 100px;
            border-radius: 0;
            cursor: pointer;
            width: 30px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
<div class="container">
    <div class="contenitore-conversazione">
        <div class="contatti">
            <h2>Elenco delle Conversazioni</h2>
            <form action="search.php" method="post">
                <input type="text" id="nuovo_username" name="nuovo_username" placeholder="Cerca username">
                <button type="submit" name="cerca">Cerca</button>
            </form>
            <ul>
                <?php foreach ($interlocutori as $interlocutore) : ?>
                    <li><a href="chat.php?interlocutore=<?= $interlocutore['id'] ?>"><?= $interlocutore['interlocutore'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="conversazione-attiva" id="conversazione-attiva">
        <?php if ($interlocutore_id) : ?>
            <?php
            // Ottieni il nome dell'interlocutore selezionato dalla tabella degli utenti
            $sql_nome_interlocutore = "SELECT username FROM users WHERE id = ?";
            $stmt_nome_interlocutore = $conn->prepare($sql_nome_interlocutore);
            $stmt_nome_interlocutore->bind_param("i", $interlocutore_id);
            $stmt_nome_interlocutore->execute();
            $result_nome_interlocutore = $stmt_nome_interlocutore->get_result();
            if ($result_nome_interlocutore->num_rows > 0) {
                $row_nome_interlocutore = $result_nome_interlocutore->fetch_assoc();
                $nome_interlocutore = $row_nome_interlocutore['username'];
            } else {
                $nome_interlocutore = "Utente sconosciuto"; // Se l'interlocutore non è trovato nel database
            }
            $stmt_nome_interlocutore->close();
            ?>
            <h2>Conversazione con <?= htmlspecialchars($nome_interlocutore) ?></h2>
            <?php foreach ($messaggi as $messaggio) : ?>
                <div class="messaggio">
                    <span class="mittente"><?= htmlspecialchars($messaggio['sender_username']) ?>:</span>
                    <span class="testo"><?= strip_tags($messaggio['content'] , '<a><h1><img><h3><h4><h2><abbr><acronym><address><b><blockquote><code><del><em><i><ins><li><ol><p><pre><span><strong><ul>');?></span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Scegli una conversazione a sinistra</p>
        <?php endif; $conn->close(); ?>
    </div>
    
    <div class="input-message-container">
        <form action="chat.php" method="post" id="form-messaggio">
            <input type="hidden" name="destinatario" value="<?= htmlspecialchars($interlocutore_id) ?>">
            <input type="text" class="input-message" name="contenuto" placeholder="Scrivi un messaggio..." required>
            <button type="submit" class="btn-send" name="invia_messaggio">Invia</button>
        </form>
    </div>
    </div>
    
  
</div>
<!-- <a href="logout.php" class="btn-logout"><img src="icons/logout_icon.png" alt="Logout" width="25" height="25"></a>-->
<script>
$(document).ready(function() {
    // Submit del form per inviare il messaggio
    $('#form-messaggio').submit(function(event) {
        event.preventDefault(); // Evita il submit del form tradizionale
        
        var destinatario = $('#destinatario').val();
        var contenuto = $('#contenuto').val();
        
        // Invia il messaggio via AJAX
        $.post('chat.php', { invia_messaggio: true, destinatario: destinatario, contenuto: contenuto })
            .done(function(response) {
                // Dopo aver inviato il messaggio, aggiorna la conversazione
                aggiornaMessaggi(JSON.parse(response));
                
                // Pulisce il campo del messaggio
                $('#contenuto').val('');
            })
            .fail(function() {
                alert('Errore durante l\'invio del messaggio.');
            });
    });

    // Funzione per aggiornare i messaggi nella conversazione
    function aggiornaMessaggi(messaggi) {
        $('#conversazione-attiva').empty();
        $.each(messaggi, function(index, messaggio) {
            var html = '<div class="messaggio">';
            html += '<span class="mittente">' + messaggio.sender_username + ':</span> ';
            html += '<span class="testo">' + messaggio.content + '</span>';
            html += '</div>';
            $('#conversazione-attiva').append(html);
        });
    }
});
</script>

</body>
</html>
