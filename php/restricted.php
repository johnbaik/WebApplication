<?php 
/*Start session */
session_start();
/*If user is already logged in redirect to welcome page*/
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
	header("location: index.php");
	exit;
}
/*Logout functionality*/
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$_SESSION["loggedin"] = false;
	header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Restricted</title>
	<link rel="stylesheet" href="mystyle.css">
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
	<div class="page">
		<h1>Error Restricted Access</h1>

		<div id="dashboard">
			<!-- Form working as dashboard for menu and logout -->
			<form form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div id="user-info">  
					<?php echo $_SESSION['username']; ?>
					<?php echo "(".$_SESSION['role'].")"; ?>

					<button type="submit" name="logout"id="logout"> <i class="material-icons">power_settings_new</i> Logout</button>
				</div>
			</div>
			<!-- Drop down menu -->
			<div class="dropdown">
				<div id="dropmenu">
					<i class="material-icons"> &#xe8fe;</i> Menu 
				</div>

				<div class="dropdown-content">
					<a href="movies.php">Movies</a>
					<a href="owner.php">Owners</a>
					<a href="administrator.php">Administrators</a>
				</div>
			</div>
		</form>
		<i class="material-icons" style="font-size: 10rem; color: red; align-self: center;">&#xe14b;</i>
		<div id="restricted">
			<a href="welcome.php">
				<button id="restrictedbtn">Go back to Main Page</button>
			</a>
		</div>
	</body>
	</html>