<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html>
    <head>
    <title>Profilo di <?= isset($_POST['username_destinatario']) ? htmlspecialchars($_POST['username_destinatario']) : htmlspecialchars($_SESSION['username']); ?></title>
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
            width:500px;
            height:500px;
            height: auto;
            display: block;
    margin: 0 auto;
    object-fit: cover; /* ridimensiona l'immagine per adattarla all'area mantenendo le proporzioni */
    
        }
.post-video {
    display: block;
    margin: 0 auto;
}
.contents {
    padding-left: 10px; /* Imposta il padding a sinistra del testo */
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
            top: 7px;
            right: 50px;
            cursor: pointer;
            border-radius: 0;
            width: 25px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        .btn-chat {
            position: absolute;
            top: 4px;
            right: 85px;
            cursor: pointer;
            border-radius: 0;
            width: 30px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
        
        .btn-main {
            position: absolute;
            top: 4px;
            right: 115px;
            cursor: pointer;
            border-radius: 0;
            width: 30px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
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
    
    <a href="upload_post.php"><img src="icons/upload.png" width="25px" height="25px" style="float: right; margin-right: 15px; margin-top: 7px"></a>
<a href="logout.php" class="btn-logout" style="border-radius: 0; height: 20px;"><img src="icons/logout_icon.png" alt="btn-logout" style="border-radius: 0; height: 25px;"></a>   
<a href="chat.php" class="btn-chat" style="border-radius: 0; height: 30px;"><img src="icons/chat.png" alt="btn-chat" style="border-radius: 0; height: 30px;"></a>   
<a href="main.php" class="btn-main" style="border-radius:0; height: 30px;"><img src="icons/back-arrow.png" alt="Torna indietro" style="border-radius: 0; height: 30px;"></a>
<?php

function get_own_account_info($username)
{
  global $conn;
  $sql = "SELECT id,nome,cognome,username,bio,profile_picture,ruolo FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
   echo '<div class="post">';
   echo '<div class="post-header">';

      while ($row = $result->fetch_assoc()) {
        //  echo '<div class="post">';
          //echo '<div class="post-header">';
          // Controlla se esiste un'immagine del profilo per l'utente
          if (file_exists("profile_images/" . $row["profile_picture"]) && $row['profile_picture'] != null) {
              // Se esiste, mostra l'immagine del profilo dell'utente               
             // echo "<script>alert(1);</script>";
              echo '<img src="profile_images/' . $row["profile_picture"] . '" alt="Foto profilo di ' . $row["username"] . '">';
              //echo "<script>". $row["username"]. "</h1>";
          } else {
              // Se non esiste, mostra la foto predefinita "guest.jpg"
              echo '<img src="profile_images/guest.jpg" alt="Foto profilo predefinita">';
          }
          echo '<h2>'. $row["username"] . '</h2>';
          echo '</div>';
          echo "<h3 style='font-style: italic; font-size: 12px;'>Descrizione:</h3>";
          $bio = $row["bio"];

// Rimuovi i tag HTML e PHP per evitare script dannosi
$allowed_tags = '<a><h1><h2><abbr><acronym><address><b><blockquote><code><del><em><i><ins><li><ol><p><pre><span><strong><ul>';

// Rimuovi i tag HTML e PHP per evitare script dannosi
$bio_safe = strip_tags($bio, $allowed_tags);

// Rimuovi i tag specifici non desiderati
$bio_safe = str_replace(['<br>', '<script>'], '', $bio_safe);
// Visualizza la descrizione del profilo
//echo '<div class="bio">' . $bio_safe . '</div>';
          echo '<p>' . $bio_safe/*htmlspecialchars($row["bio"])*/ .'</p><br>';
      }
      echo '<a href="modifica_profilo.php" class="btn-modifica">Modifica profilo</a><br><br>';
      echo '</div>';
}
}
function get_account_info($username)
 {
   global $conn;
   $sql = "SELECT id,nome,cognome,username,bio,profile_picture,ruolo FROM users WHERE username = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("s", $username);
   $stmt->execute();
   $result = $stmt->get_result();
   if ($result->num_rows > 0) {
    echo '<div class="post">';
    echo '<div class="post-header">';

       while ($row = $result->fetch_assoc()) {
         //  echo '<div class="post">';
           //echo '<div class="post-header">';
           // Controlla se esiste un'immagine del profilo per l'utente
           if (file_exists("profile_images/" . $row["profile_picture"]) && $row['profile_picture'] != null) {
               // Se esiste, mostra l'immagine del profilo dell'utente               
              // echo "<script>alert(1);</script>";
               echo '<img src="profile_images/' . $row["profile_picture"] . '" alt="Foto profilo di ' . $row["username"] . '">';
               //echo "<script>". $row["username"]. "</h1>";
           } else {
               // Se non esiste, mostra la foto predefinita "guest.jpg"
               echo '<img src="profile_images/guest.jpg" alt="Foto profilo predefinita">';
           }
           echo '<h2>'. $row["username"] . '</h2>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
           echo '  <form action="start_new_chat.php" method="post">
           <input type="hidden" name="destinatario" value="' . $row['id']. '">
           <input type="submit" class="btn-modifica" style="float: right; position:right;" value="Avvia Conversazione">
       </form>';
       // test per vedere se già si segue
  
       echo '  <form id="friendshipForm" action="add_friend.php" method="post">
       <input type="hidden" name="destinatario" value="' . $row['id']. '">
       <input type="submit" class="btn-modifica" style="float: right; position:right;" value="Segui">
   </form>';
   // fine check
           echo '</div>';
           echo "<h3 style='font-style: italic; font-size: 12px;'>&nbsp; &nbsp; &nbsp; Descrizione:</h3>";
           $bio = $row["bio"];

// Rimuovi i tag HTML e PHP per evitare script dannosi
$allowed_tags = '<a><h1><h2><abbr><acronym><address><b><blockquote><code><del><em><i><ins><li><ol><p><pre><span><strong><ul>';

// Rimuovi i tag HTML e PHP per evitare script dannosi
$bio_safe = strip_tags($bio, $allowed_tags);

// Rimuovi i tag specifici non desiderati
$bio_safe = str_replace(['<br>', '<script>'], '', $bio_safe);
// Visualizza la descrizione del profilo
//echo '<div class="bio">' . $bio_safe . '</div>';
           echo '<p>' . $bio_safe/*htmlspecialchars($row["bio"])*/ .'</p>';
       }
       
       echo '</div>';
 }
}
?>
    <div class="container">

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
 // session_start();
  // Controlla se l'utente è loggato
  if (!isset($_SESSION['mioid'])) {
      // Reindirizza l'utente alla pagina di accesso se non è loggato
      header("Location: login.html");
      exit();
  }
  else
  {
    // siamo loggati
      global $mio_id,$mio_username;
      $mio_id = $_SESSION['mioid'];
      $mio_username = $_SESSION['username'];
      if (isset($_POST['username_destinatario']))
      {
        if ($_POST['username_destinatario'] == $mio_username)
        {
           //apriamo il nostro profilo
            $mio_username_tmp = $_SESSION['username'];
            get_own_account_info($mio_username_tmp);
            get_own_post($mio_username_tmp);
        }
        else
        {
            global $username_destinatario;
            $username_destinatario = htmlspecialchars($_POST['username_destinatario']);
           /* if ($mio_username == $username_destinatario)
            {
                get_own_post();
            }*/
            get_account_info($username_destinatario);
            get_post($username_destinatario);
      
        }
        }
      else
      {
       //apriamo il nostro profilo
            $mio_username_tmp = $_SESSION['username'];
            get_own_account_info($mio_username_tmp);
            get_own_post($mio_username_tmp);
      }
  }
 
  function get_own_post($username_tmp)
  {
    global $conn;
            
    // Query per selezionare tutti i post ordinati per data decrescente
    $sql = "SELECT posts.*, users.username,users.profile_picture FROM posts INNER JOIN users ON posts.user_id = users.id WHERE username = ? ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username_tmp);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="post">';
            echo '<div class="post-header">';
            
            // Controlla se esiste un'immagine del profilo per l'utente
            if (file_exists("profile_images/" . $row["username"] . ".jpg")) {
                // Se esiste, mostra l'immagine del profilo dell'utente
                echo '<img src="profile_images/' . $row["profile_picture"] . '" alt="Foto profilo di ' . $row["username"] . '">';
            } else {
                // Se non esiste, mostra la foto predefinita "guest.jpg"
                echo '<img src="profile_images/guest.jpg" alt="Foto profilo predefinita">';
            }
            
            echo '<span class="username">'. $row["username"] . '</span>';
            echo '</div>';
            echo '<span class="contents">' . $row["content"] . '</span><br> <br>';
          // Verifica se il file caricato è un'immagine o un video
$file_extension = pathinfo($row["uploaded"], PATHINFO_EXTENSION);
if (in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
// Se il file è un'immagine, mostra un elemento img
echo '<img class="post-image" src="post_images/' . $row["uploaded"] . '" alt="Post Image">';
} elseif (in_array($file_extension, ["mp4", "mov", "avi"])) {
// Se il file è un video, mostra un elemento video
echo '<video class="post-video" controls width="500" height="auto">';
echo '<source src="post_images/' . $row["uploaded"] . '" type="video/' . $file_extension . '">';
echo 'Il tuo browser non supporta la riproduzione di video.';
echo '</video>';
}
echo '<div class="post-comments">';
            
            // Query per recuperare i commenti
            $post_id = $row["id"];
            $comment_sql = "SELECT comments.*, users.username AS comment_username FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY comments.created_at DESC";
            $comment_stmt = $conn->prepare($comment_sql);
            $comment_stmt->bind_param("i", $post_id);
            $comment_stmt->execute();
            $comment_result = $comment_stmt->get_result();
            
            if ($comment_result->num_rows > 0) {
                while ($comment_row = $comment_result->fetch_assoc()) {
                    echo '<div class="comment">';
                    echo '<span class="username">' . $comment_row["comment_username"] . ':</span>';
                    echo '<span class="content">' . $comment_row["content"] . '</span>';
                    echo '<p style="font-size:80%"><em>'. $comment_row["created_at"] . '</em></p>';
                    echo '</div>';
                   
                }
               
                //  <input type="text" class="input-message" name="contenuto" placeholder="Commenta come ' . $_SESSION['username'] . '" required width="100px">
                
            }
            echo '<form action="comment.php" method="post" class="comment-form">';
            echo '<input type="hidden" name="post_id" value="'. $post_id . '">';
            echo '<textarea name="contenuto" rows="5" cols="70" placeholder="Commenta come ' . $_SESSION['username'] . '" required></textarea><br>';
            echo '<button type="submit" class="btn-send" name="invia_messaggio">Invia</button>';
            echo '</form>';
           
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "Nessun post trovato";
    }
    
    $conn->close();
}
function get_post($username) // post di altre persone
{
    global $conn;
            
    // Query per selezionare tutti i post ordinati per data decrescente
    $sql = "SELECT posts.*, users.username,users.profile_picture FROM posts INNER JOIN users ON posts.user_id = users.id WHERE username = ? ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="post">';
            echo '<div class="post-header">';
            
            // Controlla se esiste un'immagine del profilo per l'utente
            if (file_exists("profile_images/" . $row["username"] . ".jpg")) {
                // Se esiste, mostra l'immagine del profilo dell'utente
                echo '<img src="profile_images/' . $row["profile_picture"] . '" alt="Foto profilo di ' . $row["username"] . '">';
            } else {
                // Se non esiste, mostra la foto predefinita "guest.jpg"
                echo '<img src="profile_images/guest.jpg" alt="Foto profilo predefinita">';
            }
            
            echo '<span class="username">'. $row["username"] . '</span>';
            echo '</div>';
            echo '<span class="contents">' . $row["content"] . '</span><br> <br>';
          // Verifica se il file caricato è un'immagine o un video
$file_extension = pathinfo($row["uploaded"], PATHINFO_EXTENSION);
if (in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
// Se il file è un'immagine, mostra un elemento img
echo '<img class="post-image" src="post_images/' . $row["uploaded"] . '" alt="Post Image">';
} elseif (in_array($file_extension, ["mp4", "mov", "avi"])) {
// Se il file è un video, mostra un elemento video
echo '<video class="post-video" controls width="500" height="auto">';
echo '<source src="post_images/' . $row["uploaded"] . '" type="video/' . $file_extension . '">';
echo 'Il tuo browser non supporta la riproduzione di video.';
echo '</video>';
}
echo '<div class="post-comments">';
            
            // Query per recuperare i commenti
            $post_id = $row["id"];
            $comment_sql = "SELECT comments.*, users.username AS comment_username FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY comments.created_at DESC";
            $comment_stmt = $conn->prepare($comment_sql);
            $comment_stmt->bind_param("i", $post_id);
            $comment_stmt->execute();
            $comment_result = $comment_stmt->get_result();
            
            if ($comment_result->num_rows > 0) {
                while ($comment_row = $comment_result->fetch_assoc()) {
                    echo '<div class="comment">';
                    echo '<span class="username">' . $comment_row["comment_username"] . ':</span>';
                    echo '<span class="content">' . $comment_row["content"] . '</span>';
                    echo '<p style="font-size:80%"><em>'. $comment_row["created_at"] . '</em></p>';
                    
                   
                }
               
                //  <input type="text" class="input-message" name="contenuto" placeholder="Commenta come ' . $_SESSION['username'] . '" required width="100px">
                
            } 
            //action="comment.php"
            echo '<form action="comment.php" method="post" class="comment-form">';
            echo '<input type="hidden" name="post_id" value="'. $post_id . '">';
            echo '<textarea name="contenuto" rows="5" cols="70" placeholder="Commenta come ' . $_SESSION['username'] . '" required></textarea><br>';
            echo '<button type="submit" class="btn-send" name="invia_messaggio">Invia</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "Nessun post trovato";
    }
    
    $conn->close();
}
?>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#friendshipForm').submit(function(e) {
        e.preventDefault(); // Evita il comportamento predefinito di inviare il form

        $.ajax({
            type: 'POST',
            url: 'add_friend.php',
            data: $(this).serialize(), // Invia i dati del form
            success: function(response) {
                if (response === 'success') {
                    alert('Amicizia inviata!');
                    // Reindirizza l'utente dopo aver mostrato l'alert
                    //window.location.href = 'main.php';
                } else if (response === 'already_added') {
                    alert('L\'amicizia è già stata aggiunta.');
                } else if (response === 'error') {
                    alert('Errore nell\'invio della richiesta di amicizia.');
                } else {
                    alert('Risposta non valida.');
                }
            },
            error: function(xhr, status, error) {
                // Gestisci eventuali errori di connessione o di server
                console.error('Errore nella richiesta AJAX:', error);
            }
        });
    });

    // Gestisci l'invio del modulo dei commenti
    $('.comment-form').submit(function(e) {
        e.preventDefault(); // Evita il comportamento predefinito di inviare il form
        // Ottieni i dati del form
        var formData = $(this).serialize();
        var $form = $(this);
        // Invia il commento tramite AJAX
        $.ajax({
            type: 'POST',
            url: 'comment.php',
            data: formData,
            dataType: 'json', // Indica che ci si aspetta una risposta JSON
            success: function(response) {
                // Aggiungi il nuovo commento all'elenco dei commenti del post corrispondente
                var newComment = '<div class="comment">' +
                                    '<span class="username">' + response[0].comment_username + ':</span>' +
                                    '<span class="content">' + response[0].content + '</span>' +
                                    '<p style="font-size:80%"><em>' + response[0].created_at + '</em></p>' +
                                 '</div>';
                // Inserisci il nuovo commento prima del form di commento
                $form.closest('.post').find('.post-comments').prepend(newComment);
                // Pulisci il campo del commento dopo l'invio
                $form.find('textarea').val('');
            },
            error: function(xhr, status, error) {
                // Gestisci eventuali errori di connessione o di server
                console.error('Errore nella richiesta AJAX:', error);
            }
        });
    });
});
</script>

    </body>
</html>