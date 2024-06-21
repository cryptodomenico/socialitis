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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['contenuto'])) {
    $post_id = $_POST['post_id']; 
    $contenuto = $_POST['contenuto'];
    $allowed_tags = '<a><h1><h2><abbr><acronym><address><b><blockquote><code><del><em><i><ins><li><ol><p><pre><span><strong><ul>';
  
    // Rimuovi i tag HTML e PHP per evitare script dannosi
    $contenuto_safe = strip_tags($contenuto, $allowed_tags);
    // Rimuovi i tag specifici non desiderati
    $contenuto_safe = str_replace(['<br>', '<script>'], '', $contenuto_safe);
    
    add_comment($post_id, $contenuto_safe);

    // Recupera i nuovi commenti
    $comments = get_comments($post_id);
    echo json_encode($comments);
    exit();
}

function add_comment($post_id, $contenuto)
{
    $mio_id = $_SESSION['mioid'];
    global $conn;

    // Inserisci il messaggio nel database
    $sql_inserimento_commento = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stms_inserimento_commento = $conn->prepare($sql_inserimento_commento);
    $stms_inserimento_commento->bind_param("iis", $post_id, $mio_id, $contenuto);
    $stms_inserimento_commento->execute();
    $stms_inserimento_commento->close(); 
}

function get_comments($post_id)
{
    global $conn;
    $comment_sql = "SELECT comments.*, users.username AS comment_username FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY comments.created_at DESC";
    $comment_stmt = $conn->prepare($comment_sql);
    $comment_stmt->bind_param("i", $post_id);
    $comment_stmt->execute();
    $comment_result = $comment_stmt->get_result();
    
    $comments = [];
    while ($comment_row = $comment_result->fetch_assoc()) {
        $comments[] = $comment_row;
    }
    
    return $comments;
}
?>