<html>
    <head>
        <title>Cerca utente</title>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
    // Aggiungi un gestore di eventi a tutti i link con la classe "profileLink"
    var profileLinks = document.querySelectorAll(".profileLink");
    profileLinks.forEach(function(link) {
        link.addEventListener("click", function(event) {
            event.preventDefault(); // Previeni il comportamento predefinito del link

            var username = this.getAttribute("data-username"); // Ottieni l'username dal attributo "data-username"
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "profile.php");

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "username_destinatario");
            hiddenField.setAttribute("value", username);

            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .post {
            background-color: #fff;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .post-header {
            padding: 10px;
            display: flex;
            align-items: center;
        }
        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .post-header .username {
            font-weight: bold;
        }
        .post-image {
            width: 100%;
            height: auto;
        }
        .post-comments {
            padding: 10px;
        }
        .comment {
            margin-bottom: 5px;
        }
        .comment .username {
            font-weight: bold;
            margin-right: 5px;
        }
        .btn-logout {
            position: absolute;
            top: 20px;
            right: 70px;
            cursor: pointer;
            border-radius: 0;
            width: 28px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        .btn-home {
            position: absolute;
            /* top: 10px;*/
            right: 7px;
            border-radius: 0;
            cursor: pointer;
            width: 35px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        .btn-back {
            position: absolute;
            top: 10px;
            right: 55px;
            border-radius: 0;
            cursor: pointer;
            width: 40px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
    </style>
    </head>
    <body>
    <?php
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
   
    if (isset($_POST['nuovo_username']))
    {
        $username = $_POST['nuovo_username'];
        search_user($username);
    }
}
function search_user($username)
{
    echo "<div class='container'>";
    echo '  <a href="logout.php" class="btn-logout" style="border-radius: 0; height: 20px;"><img src="icons/logout_icon.png" alt="btn-logout" style="border-radius: 0; height: 37px;"></a>';
echo '      <a href="main.php" class="btn-home"  style="border-radius: 0;"><img src="icons/home_icon.png" class="btn-home"  alt="Home"  style="border-radius: 0;"></a>';
echo '      <a href="chat.php" class="btn-back"  style="border-radius: 0;"><img src="icons/back-arrow.png" class="btn-back"  alt="Back"  style="border-radius: 0;"></a>';
    global $conn;
    // Query per selezionare tutti i post ordinati per data decrescente
    $sql = "SELECT username,profile_picture,nome,cognome FROM users WHERE username LIKE ? AND username != ?";
    $param = "%$username%";
    $stmt = $conn->prepare($sql);
    $username = $_SESSION['username'];
$stmt->bind_param("ss", $param, $username);
$stmt->execute();
$result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="post">';
            echo '<div class="post-header">';
            
            // Controlla se esiste un'immagine del profilo per l'utente
            if (file_exists("profile_images/" . $row["username"] . ".jpg")) {
                // Se esiste, mostra l'immagine del profilo dell'utente
                echo '<img src="profile_images/' . $row["username"] . '.jpg" alt="Foto profilo di ' . $row["username"] . '">';
            } else {
                // Se non esiste, mostra la foto predefinita "guest.jpg"
                echo '<img src="profile_images/guest.jpg" alt="Foto profilo predefinita">';
            }
            
            echo '<span class="username"><a href="profile.php" class="profileLink" data-username="' . $row["username"] . '">' . $row["username"] . '</a></span>';
            echo " &nbsp; &nbsp;<p style='font-style: italic; font-size: 12px;'>". $row['nome'] ."&nbsp;" . $row["cognome"]. "</p>";
            echo '</div>';
         
            echo '</div>';
        }
    } else {
        echo "Nessun post trovato";
    }
    echo "</div>";
    $conn->close();
}
?>
    </body>
</html>