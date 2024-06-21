<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram-like Posts</title>
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
    </style>


</head>
<body>
<a href="upload_post.php"><img src="icons/upload.png" width="25px" height="25px" style="float: right; margin-right: 15px; margin-top: 7px"></a>
<a href="logout.php" class="btn-logout" style="border-radius: 0; height: 20px;"><img src="icons/logout_icon.png" alt="btn-logout" style="border-radius: 0; height: 25px;"></a>   
<a href="chat.php" class="btn-chat" style="border-radius: 0; height: 30px;"><img src="icons/chat.png" alt="btn-chat" style="border-radius: 0; height: 30px;"></a>   

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
        session_start();
        // Controlla se l'utente è loggato
        if (!isset($_SESSION['mioid'])) {
            // Reindirizza l'utente alla pagina di accesso se non è loggato
            header("Location: login.html");
            exit();
        }
        else
        {
            global $mio_id,$username;
            $mio_id = $_SESSION['mioid'];
            $username = $_SESSION['username'];
            if (isset($_POST['post_id']))
            {
                $id_post = $_POST['post_id'];
                getpost_full($id_post);
            }
            else
            {
                header("Location: main.php");
            }
            
        }
        // Connessione al database (omessa per brevità)
        function getpost_full($id_post) {
            global $conn;
            
            // Query per selezionare tutti i post ordinati per data decrescente
            $sql = "SELECT posts.*, users.username FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
            $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_post);
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
                    
                    echo '<span class="username">' . $row["username"] . '</span>';
                    echo '</div>';
                    echo '<span class="contents">' . $row["content"] . '</span><br>';
                    echo '<img class="post-image" src="post_images/' . $row["uploaded"] . '" alt="">';
                    echo '<div class="post-comments">';
                    
                    // Query per recuperare i commenti
                  //  $post_id = $row["id"];
                    $comment_sql = "SELECT comments.*, users.username AS comment_username FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = ?";
                    $comment_stmt = $conn->prepare($comment_sql);
                    $comment_stmt->bind_param("i", $id_post);
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
                      
                    }
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

<?php
//session_start(); // Inizializzazione della sessione

// Connessione al database

?>
</body>
</html>
