<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram-like Posts</title>
   
   
     <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"> -->
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
        .btn-profile {
            position: absolute;
            top: 4px;
            right: 115px;
            cursor: pointer;
            border-radius: 0;
            width: 33px; /* Dimensioni desiderate per l'icona */
            height: auto; /* L'altezza si adatterà proporzionalmente alla larghezza */
        }
       /* .slick-prev,
        .slick-next {
            font-size: 24px;
            color: white;
            z-index: 100;
        }

        .slick-prev {
            left: -40px;
        }

        .slick-next {
            right: -40px;
        }*/
    </style>
</head>
<body>
<a href="upload_post.php"><img src="icons/upload.png" width="25px" height="25px" style="float: right; margin-right: 15px; margin-top: 7px"></a>
<a href="logout.php" class="btn-logout" style="border-radius: 0; height: 20px;"><img src="icons/logout_icon.png" alt="btn-logout" style="border-radius: 0; height: 25px;"></a>   
<a href="chat.php" class="btn-chat" style="border-radius: 0; height: 30px;"><img src="icons/chat.png" alt="btn-chat" style="border-radius: 0; height: 30px;"></a>   
<a href="profile.php" class="btn-profile" style="border-radius: 0; height: 30px;"><img src="icons/profile.png" alt="btn-profile" style="border-radius: 0; height: 30px;"></a>   

<div class="container">
        
        <?php
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
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
            getallpost();
        }
        
        // Connessione al database (omessa per brevità)
        /*function getallpost() {
            global $conn;
            
            // Query per selezionare tutti i post ordinati per data decrescente
            $sql = "SELECT posts.*, users.username,users.profile_picture FROM posts INNER JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
            $stmt = $conn->prepare($sql);
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
                    
                    echo '<span class="username"><a href="profile.php" class="profileLink" data-username="' . $row["username"] . '">' . $row["username"] . '</a></span>';
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
                    

                   /* echo '<form action="comment.php" method="post">
                    <input type="hidden" name="post_id" value="'. $post_id . '">
                    <textarea name="contenuto" rows="5" cols="70"  placeholder="Commenta come ' . $_SESSION['username'] . '" required></textarea><br>
                        <button type="submit" class="btn-send" name="invia_messaggio">Invia</button>
                    </form>';* /
                   
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "Nessun post trovato";
            }
            
            $conn->close();
        } */
        function getallpost() {
            global $conn;
            
            // Query per selezionare tutti i post ordinati per data decrescente
            $sql = "SELECT posts.*, users.username, users.profile_picture FROM posts INNER JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
            $stmt = $conn->prepare($sql);
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
                    
                    echo '<span class="username"><a href="profile.php" class="profileLink" data-username="' . $row["username"] . '">' . $row["username"] . '</a></span>';
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
                    }
        
                    echo '</div>'; // Close post-comments div
                    
                    echo '<form action="comment.php" method="post" class="comment-form">';
                    echo '<input type="hidden" name="post_id" value="'. $post_id . '">';
                    echo '<textarea name="contenuto" rows="5" cols="70" placeholder="Commenta come ' . $_SESSION['username'] . '" required></textarea><br>';
                    echo '<button type="submit" class="btn-send" name="invia_messaggio">Invia</button>';
                    echo '</form>';
                    
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
       function setupCommentForm() {
    $('.comment-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var postId = form.find('input[name="post_id"]').val();
        var contenuto = form.find('textarea[name="contenuto"]').val();

        $.ajax({
            type: 'POST',
            url: 'comment.php',
            data: { post_id: postId, contenuto: contenuto },
            success: function(response) {
                var comments = JSON.parse(response);
                var commentSection = form.siblings('.post-comments');
                
                commentSection.html(''); // Pulisce i commenti precedenti

                comments.forEach(function(comment) {
                    var commentHtml = '<div class="comment">';
                    commentHtml += '<span class="username">' + comment.comment_username + ':</span>';
                    commentHtml += '<span class="content">' + comment.content + '</span>';
                    commentHtml += '<p style="font-size:80%"><em>' + comment.created_at + '</em></p>';
                    commentHtml += '</div>';
                    commentSection.append(commentHtml);
                });

                form.find('textarea[name="contenuto"]').val(''); // Pulisce il campo del commento
            }
        });
    });
}

$(document).ready(function() {
    setupCommentForm();
});
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

</body>
</html>
