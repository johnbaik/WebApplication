<?php

$client_id = 'da571d55-1983-4804-a90a-f0a31298de66';
$client_secret = 'c50abccd-376f-4cf3-ad59-c99f454f05e2';
$redirect_uri = 'http://localhost:8000/welcome.php';

/*Start session */
session_start();

/*If user is already logged in redirect to welcome page*/
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
	header("location: welcome.php");
	exit;
}

/*Connect to database*/
// require_once "serverconn.php";

$error_msg = "";

/* if($_SERVER["REQUEST_METHOD"] == "POST"){
	// Now we check if the data from the login form was submitted, isset() will check if the data exists.
	
	// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
	if ($stmt = $con->prepare('SELECT ID, PASSWORD, ROLE, NAME,SURNAME,CONFIRMED FROM Users WHERE USERNAME = ?')) {
		// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		// Store the result so we can check if the account exists in the database.
		$stmt->store_result();
		
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id, $password,$role,$name,$surname,$confirmed);
			$stmt->fetch();
			// Account exists, now we verify the password.
			// Note: remember to use password_hash in your registration file to store the hashed passwords.
			if ($_POST['password'] === $password) {
				if ($confirmed) {
					
					// Verification success! User has loggedin!
					// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
					session_regenerate_id();
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['username'] = $_POST['username'];
					$_SESSION['id'] = $id;
					$_SESSION['role']=$role;
					$_SESSION['name'] = $name;
					$_SESSION['surname']=$surname;
					//Redirect to welcome page
					header('Location: welcome.php');
				}
				else{
					//Show error message
					$error_msg = "You are not confirmed yet!";
					
				}
				
			} else {
				// Incorrect password
				//Show error message
				$error_msg = "Incorrect username and/or password";
				
			}
		} else {
			// Incorrect username
			//Show error message
			$error_msg = "Incorrect username and/or password";
			
		}
		
		$stmt->close();
	}
	
} */

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Login</title>
	<link rel="stylesheet" href="mystyle.css">
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!-- jquery CDN -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="page" id="login-page">
		<h1 id="loginTitle">Login</h1>
		<i class="material-icons" style="font-size: 2.5rem;align-self:center;">&#xe7fd;</i>
		<button id="login" type="button" onclick="location.href='http://localhost:3000/oauth2/authorize?response_type=token&client_id=da571d55-1983-4804-a90a-f0a31298de66&state=xyz&redirect_uri=http://localhost:8000/welcome.php';">Log
			in with Keyrock Account</button>
	</div>
</body>

</html>