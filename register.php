<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'socialitis';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	exit('Problema nel connettersi al database: ' . mysqli_connect_error());
}
if (!isset($_POST['username'], $_POST['email'],$_POST['password'])) {

	exit('Username e/o password mancanti');
}

if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
	exit('Username, email e/o password mancanti');
}
if ($stmt = $con->prepare('SELECT id,username FROM users WHERE username = ? or email = ?'))
{
	$nome = str_replace(' ', '', $_POST['nome']);
	$cognome = str_replace(' ', '', $_POST['cognome']);
	$username = str_replace(' ', '', $_POST['username']);
	$email = str_replace(' ', '', $_POST['email']);
	$password = str_replace(' ', '', $_POST['password']);
	

// Ora puoi utilizzare le variabili $nome, $cognome, $username, $email e $password senza spazi iniziali e finali

	$stmt->bind_param('ss', $username, $_POST['email']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
		echo "<script>alert('Username o email già in uso'); window.location='register.html';</script>";
		
        exit;
	} else {
		if ($stmt = $con->prepare('INSERT INTO users (nome, cognome, username,email, password, ruolo) VALUES (?, ?, ?, ?, ?,?)')) {
			$password = password_hash($password, PASSWORD_BCRYPT);
			$ruolo = "user";
			$stmt->bind_param('ssssss', $nome, $cognome, $username,$email, $password, $ruolo);
			$stmt->execute();
				echo 'Account registrato; verrai trasferito presto alla schermata di login!';
				header('Location: login.html');
			} else {
				echo 'Errore durante la registrazione dell\'account.';
			}
		} 
	}		

$con->close();
?>