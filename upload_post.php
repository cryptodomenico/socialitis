<?php
// Connessione al database
session_start();
// Controlla se l'utente è loggato
if (!isset($_SESSION['mioid'])) {
    // Reindirizza l'utente alla pagina di accesso se non è loggato
    header("Location: login.html");
    exit();
}
else {
    // Reindirizza l'utente alla pagina di accesso se non è loggato
    global $mio_id,$username;
    $mio_id = $_SESSION['mioid'];
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

// Funzione per aggiungere un nuovo post nel database
function generateUniqueFileName($filename) {
    $file_contents = @file_get_contents(basename($filename));
    $hash = hash('sha256', $file_contents);
    $timestamp = time(); // Aggiungi il timestamp corrente

    // Estrai l'estensione del file
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // Costruisci il nome del file basato sull'hash e sul timestamp
    $unique_filename = $hash . '_' . $timestamp . '.' . $extension;

    return $unique_filename;
}

function addPostToDatabase($user_id, $content, $uploaded_filenames) {
    global $conn;
    
    // Prepara la query SQL per l'inserimento del post nel database
    $sql = "INSERT INTO posts (user_id, content, uploaded, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $content, $uploaded_filename);

    foreach ($uploaded_filenames as $uploaded_filename) {
        if ($stmt->execute()) {
           // echo '<script>alert("Post caricato con successo!");</script>';
          //  header("Location: main.php");
        } else {
            echo '<script>alert("Si è verificato un errore durante il caricamento.");</script>';
        }
    }

    // Esegue la query SQL per inserire il nuovo post nel database
    /*if ($stmt->execute()) {
        echo '<script>alert("Post caricato con successo!");</script>';
        header("Location: main.php");
    } else {
        echo '<script>alert("Si è verificato un errore durante il caricamento.");</script>';
    }*/

    // Chiude lo statement
    $stmt->close();
}

// Verifica se il modulo è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se è stato caricato un file
    if (isset($_FILES["file"])) {
        $uploaded_filenames = []; // Array per memorizzare i nomi dei file caricati

        $files = $_FILES["file"];

        // Percorso di destinazione per il caricamento dei file
        $target_dir = "post_images/";

        // Itera attraverso i file caricati
        for ($i = 0; $i < count($files['name']); $i++) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_size = $files['size'][$i];
            $file_error = $files['error'][$i];

            // Genera un nome univoco per il file
            $uploaded_filename = generateUniqueFileName($file_name);

            // Controlla se il file è un'immagine o un video
            $allowed_extensions = array("jpg", "jpeg", "png", "gif", "mp4", "mov", "avi");
            $file_extension = strtolower(pathinfo($uploaded_filename, PATHINFO_EXTENSION));
            if (in_array($file_extension, $allowed_extensions)) {
                // Sposta il file nella directory di destinazione
                if (move_uploaded_file($file_tmp, $target_dir . $uploaded_filename)) {
                    // Il file è stato caricato con successo
                    array_push($uploaded_filenames, $uploaded_filename);
                } else {
                    // Si è verificato un errore durante il caricamento del file
                    echo '<script>alert("Si è verificato un errore durante il caricamento del file.");</script>';
                }
            } else {
                // Estensione del file non consentita
                echo '<script>alert("Puoi caricare solo immagini o video.");</script>';
            }
        }
    }
    $user_id = $_SESSION['mioid']; // Supponiamo che l'utente corrente abbia ID 1
    $contenuto = $_POST['description'];
    $allowed_tags = '<a><h1><h2><abbr><acronym><address><b><blockquote><code><del><em><i><ins><li><ol><p><pre><span><strong><ul>';

    // Rimuovi i tag HTML e PHP per evitare script dannosi
    $contenuto_safe = strip_tags($contenuto, $allowed_tags);

    // Rimuovi i tag specifici non desiderati
    $contenuto_safe = str_replace(['<br>', '<script>'], '', $contenuto_safe);
    // Aggiungi il post al database
    addPostToDatabase($mio_id, $contenuto_safe, $uploaded_filenames);
} else {
    // Nessun file è stato caricato
    echo '<script>alert("Nessun file è stato caricato.");</script>';
}


// Chiude la connessione al database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carica Foto o Video</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #fafafa;
        }
        .container {
            width: 400px;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }
        h1 {
            margin-bottom: 20px;
        }
        input[type="file"] {
            display: none;
        }
        label.upload-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #3897f0;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        label.upload-button:hover {
            background-color: #1e6aba;
        }
        .preview {
            margin-top: 20px;
        }
        img.preview-image {
            max-width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        textarea {
            width: 95%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            margin-bottom: 20px;
        }
        button.upload-button {
            padding: 10px 20px;
            background-color: #3897f0;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button.upload-button:hover {
            background-color: #1e6aba;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carica Foto o Video</h1>
        <form action="upload_post.php" method="post" enctype="multipart/form-data">
            <label for="file-upload" class="upload-button">
                Seleziona File
            </label>
            <input type="file" id="file-upload" name="file[]" accept="image/*, video/*" multiple required>
            <div class="preview">
                <img id="preview-image" class="preview-image" src="#" alt="Preview" style="display: none;">
            </div>
            <textarea name="description" placeholder="Inserisci una descrizione..."></textarea>
            <button type="submit" class="upload-button"><img src="icons/upload.png" width="15px" height="15px" style="margin-right: 10px">Carica</button>
        </form>
        <script>
            // Aggiunge un listener all'input file per il cambio
            document.getElementById('file-upload').addEventListener('change', function(event) {
                var previewImage = document.querySelector('.preview');

                // Svuota la visualizzazione dell'anteprima prima di aggiungere nuove anteprime
                previewImage.innerHTML = '';

                var files = event.target.files;

                // Itera attraverso i file selezionati
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var fileType = file.type.split('/')[0]; // Ottiene il tipo di file (immagine o video)

                    var reader = new FileReader();

                    reader.onload = function(e) {
                        var previewElement;
                        if (fileType === 'image') {
                            // Crea un elemento immagine per l'anteprima
                            previewElement = document.createElement('img');
                            previewElement.classList.add('preview-image');
                            previewElement.src = e.target.result;
                        } else if (fileType === 'video') {
                            // Crea un elemento video per l'anteprima
                            previewElement = document.createElement('video');
                            previewElement.classList.add('preview-video');
                            previewElement.src = e.target.result;
                            previewElement.controls = true; // Mostra i controlli per il video
                        }

                        // Aggiungi l'anteprima all'elemento .preview
                        previewImage.appendChild(previewElement);
                    }

                    // Leggi il contenuto del file come URL dati
                    reader.readAsDataURL(file);
                }
            });
        </script>
    </div>

 
</body>
</html>
