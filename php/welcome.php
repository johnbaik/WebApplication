<?php
/*Start session */
session_start();

if (isset($_GET['token'])) {

	$_SESSION['token'] = $_GET['token'];
	$_SESSION['loggedin'] = TRUE;
}
/*If user is already logged in redirect to welcome page*/
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: index.php");
	exit;
}
/*Logout functionality*/
if (isset($_POST['logout'])) {
	$_SESSION["loggedin"] = false;
	session_destroy();
	header("location: index.php");
	/*Check if user has the right privileges*/
}

echo "<br>";

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => 'http://keyrock:3000/user?access_token=' . $_SESSION['token'],
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
$json = json_decode($response, true);

curl_close($curl);

$_SESSION['username'] = $json['username'];
$_SESSION['id'] = $json['id'];
$_SESSION['role'] = $json['roles']['0']['name'];

if (isset($_POST['notify'])) {
	$resp="";

	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://pepProxy:1027/notifications/notify',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "User_id":"'.$_SESSION['username'].'"
}',
  CURLOPT_HTTPHEADER => array(
	'Content-Type: application/json',
	'X-Auth-Token: 1234'

  ),
));

$res = curl_exec($curl);

curl_close($curl);
$result = json_decode($res, true);

	$tmp="this.parentElement.style.display='none'";
	$resp.='<div class="alert" id="alert">
					<span id="closebtn">&times</span>';

	$resp.='<br>';
	if($result!=null){
		foreach ($result as $row) {
			$resp.=$row['Text'];
			$resp.='<br>';
		}
	}
	else{
		$resp.=" No notifications";
	}
					
	$resp.='</div>';

	exit($resp);
}
if(isset($_POST['deleteNotif'])){

	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://pepProxy:1027/notifications/deleteNotif',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
	"User_id":"'.$_SESSION['username'].'"
	}',
  CURLOPT_HTTPHEADER => array(
	'Content-Type: application/json',
	'X-Auth-Token: 1234'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

exit();
}

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Welcome</title>
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="mystyle.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
	</script>
	<script type="text/javascript">

		$(document).ready(function() {
			$(document).on("click", "#notifications", function(e) {
				$.ajax({
					url: 'welcome.php',
					method: 'POST',
					data: {
						notify: 1
					},
					success: function(data) {
						console.log(data);
						$('#resp').html(data);
					},
					dataType: 'text'
				});
			})
		});
		$(document).ready(function() {
			$(document).on("click", "#closebtn", function(e) {
				$("#alert").hide();
				$.ajax({
					url: 'welcome.php',
					method: 'POST',
					data: {
						deleteNotif: 1
					},
					success: function(data) {
						console.log(data);
						// $('#resp').html(data);
					},
					dataType: 'text'
				});
			})
		});

	</script>
</head>

<body>
	<!-- All the page works inside this div -->
	<div class="page">
		<h1>Welcome</h1>
		<div id="dashboard">
			<!-- Form working as dashboard for menu and logout -->
			<form form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<!-- Show user info  -->
				<div id="user-info">
					<?php echo $_SESSION['username']; ?>
					<?php echo "(" . $_SESSION['role'] . ")"; ?>

					<button type="submit" name="logout" id="logout"> <i class="material-icons">power_settings_new</i> Logout</button>
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

		<button id="notifications">Show Notifications</button>
				
				<div id="resp">

				</div>
		<div id="welcome">
			<p>Welcome to our site. Please navigate to the category you like by the dropmenu on your left.</p>
		</div>
	</div>
</body>

</html>