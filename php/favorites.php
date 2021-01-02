<?php
/*Start session */
session_start();
/*If user is already logged in redirect to welcome page*/
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: index.php");
	exit;
}
/*Logout functionality*/
if (isset($_POST['logout'])) {
	$_SESSION["loggedin"] = false;
	header("location: index.php");
}
/*if remove button is pressed start this functionality*/
if (isset($_POST['remove_from_favorites'])) {
	parse_str($_POST['form'], $form);
	$movie_id = $form['movie_id'];
	$user_id =  $_SESSION['id'];

	/*querry to delete movie from favorites*/
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'http://pepProxy:1027/favorites/deleteFavorite',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'DELETE',
	CURLOPT_POSTFIELDS =>'  {  
        
        "User_id":"'.$user_id.'",
        "Movie_id":"'.$movie_id.'"

}',
  CURLOPT_HTTPHEADER => array(
	'Content-Type: application/json',
	'X-Auth-Token: 1234'

  ),
));

$response = curl_exec($curl);

curl_close($curl);

}
if (isset($_POST['show'])) {
	$response = "";

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://pepProxy:1027/favorites',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>'  {  
			"User_id":"'.$_SESSION['id'].'"
		
	}',
	  CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'X-Auth-Token: 1234'
	  ),
	));
	
	$res = curl_exec($curl);
	
	curl_close($curl);
	$result=json_decode($res, true);

	/*If user has items in favorites print favorites as a table else show message*/
	if (count($result) >= 1) {
		$response .= '<br>

			
				<tr>
					<th>
						ID
					</th>
					<th>
						Name
					</th>
					<th>
						Start date
					</th>
					<th>
						End date
					</th>
					<th>
						Cinema
					</th>
					<th>
						Category
					</th>
					<th>
						Favorite
					</th>

	 			</tr>';
		/*for each movie user has in favorites print its attributes*/
        foreach ($result as $row) { // Important line !!! 

			if($row==null){
				continue;
			}

	 		$response .= '<form action="favorites.php" class="favform" id="formId' .  $row['_id'] . '" method="post">';

			$response .= '<tr><td><input type="text" form="formId' .  $row['_id'] .'" name="movie_id" value=' . $row['_id'] . ' id="formId' .  $row['_id'] . '" readonly ></td>';
			
			foreach ($row as $field => $value) {
				if ($field == '_id' || $field == '__v') {
					continue;
				}

				if ($field == 'Start_date' || $field == 'End_date') {
                    $date = explode("T", $value)[0];
                    $response .= '<td form="formId' . $row['_id'] . '">'
                    . $date .
                    "</td>";
                    continue;
                }
				else{
				$response .= "<td>"
					. $value .
					"</td>";
				}
			}
			$response .= '<td>';
			$response .= '
					<button type="submit" class="favform" name="remove_from_favorites" id="formId' .  $row['_id'] . '" form="formId' .  $row['_id'] . '"  >Remove from Favorites</button>
					</td>
					</form>';
			$response .= "</tr>";
		}
	} else {
		$response .= '<p id="errorMsg"> You have no movies in your favorites </p>';
	}
	$response .= '</form>';
	exit($response);
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Favorites</title>
	<link rel="stylesheet" href="mystyle.css">
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			$(document).on("submit", '.favform', function(e) {
				var frm = $(this).closest('form');
				var movie_id = $(frm).closest('input').val();

				e.preventDefault();

				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						remove_from_favorites: 1,
						show: 0
					},
					success: function(data) {
						console.log('Submission was successful.');
						$('#table').html(data);
					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			});
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				url: 'favorites.php',
				method: 'POST',
				data: {
					show: 1
				},
				success: function(data) {
					$('#table').html(data);

				},
				dataType: 'text'
			});
		});
	</script>

</head>

<body>
	<div class="page">
		<h1>Favorites</h1>

		<div id="dashboard">
			<!-- Form working as dashboard for menu and logout -->
			<form form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div id="user-info">
					<?php echo $_SESSION['username']; ?>
					<?php echo "(" . $_SESSION['role'] . ")"; ?>


					<button type="submit" name="logout" id="logout"> <i class="material-icons">power_settings_new</i>
						Logout</button>
				</div>
		</div>
		<!-- Drop down menu -->
		<div class="dropdown">
			<div id="dropmenu">
				Menu &#x25BE;
			</div>

			<div class="dropdown-content">
				<a href="movies.php">Movies</a>
				<a href="owner.php">Owners</a>
				<a href="administrator.php">Administrators</a>
			</div>
		</div>
		</form>
		<table id="table"></table>
	</div>
	</div>
</body>

</html>