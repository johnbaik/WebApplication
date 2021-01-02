<?php
/*Start session */
session_start();
/*If user is already logged in redirect to welcome page*/
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: index.php");
	exit;
}
/*Connect to database*/

/*Logout functionality*/
if (isset($_POST['logout'])) {
	$_SESSION["loggedin"] = false;
	session_destroy();
	header("location: index.php");
	/*Check if user has the right privileges*/
}
if ($_SESSION["role"] != "Admin" && $_SESSION["role"] != "Cinema_Owner") {
	header("location: restricted.php");
}

$error_msg = "";
/*If user presses to delete a movie*/
if (isset($_POST['deleteMovie']) && ($_POST['deleteMovie'] == 1)) {
	parse_str($_POST['form'], $form);
	deleteMov($form['movie_id']);
}

if (isset($_POST['showCinemas'])) {
	show_cinemas($_SESSION['id']);
}

/*if user presses to update a movie*/
if (isset($_POST['UpdateMovie']) && ($_POST['UpdateMovie'] == 1)) {
	parse_str($_POST['form'], $form);
	update($form['movie_id'], $form['title'], $form['startdate'], $form['enddate'], $form['cinemaname'], $form['category']);
} else {
	$error_msg = "";
}



/*If user presses to add a cinema*/
if (isset($_POST['addcinemasubmit'])) {
	parse_str($_POST['form'], $form);
	//exit($form['addcinemaname']);
	add_cinema($form['addcinemaname'], $_SESSION['id']);
} else {
	$add_error_msg = "";
}

/*if user presses to add a movie*/
if (isset($_POST['addMoviesubmit'])) {
	parse_str($_POST['form'], $form);
	add_movie($form['title'], $form['startdate'], $form['enddate'], $form['cinemaname'], $form['category']);
} else {
	$add_movie_error_msg = "";
}
/*if user presses to delete a cinema*/
if (isset($_POST['deletecinemasubmit'])) {
	parse_str($_POST['form'], $form);
	//exit($form['cinemaname']);
	deleteCinema($form['cinemaname'], $_SESSION['id']);
} else {
	$delete_cinema_error_msg = "";
}
if (isset($_POST['OwnerMovies'])) {
	LoadPage($_SESSION['username']);
}
function LoadPage($ownername)
{
	$response = "";
	$response .=
		'<thead>
	<tr id="fist-row">
		<th>
			Title
		</th>
		<th>
			Start Date
		</th>
		<th>
			End Date
		</th>
		<th>
			Cinema Name
		</th>
		<th>
			Category
		</th>
	</tr>
</thead>

<tbody>';

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/movies',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{
		"Cinema_owner":"' . $ownername . '"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'

		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	$res = json_decode($response, true);

	$response .= '<tr id="firstRow">
		<th>
			ID
		</th>
		<th>
			Title
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

	</tr>
	<br>';

	foreach ($res as $result) { // Important line !!! 

		foreach ($result as $row) {

			$start = explode("T", $row['Start_date'])[0];
			$end = explode("T", $row['End_date'])[0];
			$response .= '<form action="" class="ownerform" method="post" id="form' . $row['_id'] . '">';

			$response .= "<tr>";
			$response .= '
		<td>
		<input type="text" name="movie_id" value=' . $row['_id'] . ' id="movie_id" form="form' . $row['_id'] . '"  required>
		</td>
		<td>
		<input type="text" name="title" value="' . $row['Title'] . '" id="title" form="form' . $row['_id'] . '" required>
		</td>
		<td>
		<input type="date" name="startdate" value="' . $start . '" id="startdate" form="form' . $row['_id'] . '" required>
		</td>
		<td>
		<input type="date" name="enddate" value="' . $end . '" id="enddate" form="form' . $row['_id'] . '" required> 
		</td>';
			$response .= '<td>';
			$response .= '<select name="cinemaname" form="form' . $row['_id'] . '" >';
			$response .= '<option value="';
			$response .= $row['Cinema'];
			$response .= '"selected hidden>';
			$response .= $row['Cinema'];
			$response .= '</option>';

			/*Get all the cinemas that this owner has*/
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://pepProxy:1027/owner/showCinemas',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => '{
	
	 "Cinema_owner":"' . $ownername . '"
	}',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'X-Auth-Token: 1234'
				),
			));

			$cinema = curl_exec($curl);

			curl_close($curl);
			$cin = json_decode($cinema, true);

			foreach ($cin as $cine) {

				$response .= '<option value="';
				$response .= $cine['Cinema_name'];
				$response .= '">';
				$response .= $cine['Cinema_name'];
				$response .= '</option>';
			}

			$response .= '
		</select>
		</td>
		<td>
		<input type="text" name="category" value="' . $row['Category'] . '" id="category" form="form' . $row['_id'] . '" required>
		</td>';

			$response .= '<td>
			<button type="submit" class="deletebtn" id="deleteform' . $row['_id'] . '" name="deleteMovie" form="form' . $row['_id'] . '">Delete</button>
		</td>

		<td>
			<button type="submit" class="updatebtn" id="updateform' . $row['_id'] . '" name="UpdateMovie" form="form' . $row['_id'] . '">Update</button>
		</td>
		</tr>

		</form>';
		}
	}
	// $response.='</tbody>';
	exit($response);
	//exit;
}
function deleteCinema($cinemaname, $owner)
{
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/deleteCinema',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'DELETE',
		CURLOPT_POSTFIELDS => '{
	 "Cinema_name":"' . $cinemaname . '",
	 "Cinema_owner":"' . $owner . '"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	$res = json_decode($response, true);
	exit($res['message']);
}

function deleteMov($movie_id)
{
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/deleteMovie',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'DELETE',
		CURLOPT_POSTFIELDS => '{
	"_id":"' . $movie_id .'"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://orion:1026/v2/entities/'. $movie_id.'/',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'DELETE',
	));

	$response = curl_exec($curl);

	curl_close($curl);

	LoadPage($_SESSION['username']);
}
function update($movie_id, $title, $startdate, $enddate, $cinemaname, $category)
{

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/updateMovie',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PATCH',
		CURLOPT_POSTFIELDS => '{
		"_id":"'.$movie_id.'",
	    "Title":"' . $title . '",
	    "Start_date":"' . $startdate . '",
	    "End_date":"' . $enddate . '",
	    "Cinema": "' . $cinemaname . '",
		"Category":"' . $category . '"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	// echo $response;
	$res = json_decode($response, true);

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://orion:1026/v2/entities/'.$movie_id.'/attrs',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PATCH',
		CURLOPT_POSTFIELDS => '{
  "Title": {
    "value": "' . $title . '",
    "type": "String"
  },
  "StartDate": {
    "value": "' . $startdate . '",
    "type": "String"
  },
  "EndDate": {
    "value": "' . $enddate . '",
    "type": "String"
  },
  "Cinema": {
    "value": "' . $cinemaname . '",
    "type": "String"
  },
  "Category": {
    "value": "' . $category . '",
    "type": "String"
  }
}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	// echo $response;

	exit($res['message']);
}

function show_cinemas($ownername)
{
	$response = "";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/showCinemas',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{
	
	 "Cinema_owner":"' . $ownername . '"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));

	$res = curl_exec($curl);

	curl_close($curl);
	$result = json_decode($res, true);

	foreach ($result as $row) {

		$response .= '<option value="';
		$response .= $row['Cinema_name'];
		$response .= '">';
		$response .= $row['Cinema_name'];
		$response .= '</option>';
	}

	exit($response);
}

function add_cinema($cinemaname, $ownername)
{

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/addCinema',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{
		
		"Cinema_name":"' . $cinemaname . '",
		"Cinema_owner":"' . $ownername . '"
	}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	$res = json_decode($response, true);
	exit($res['message']);
}

function add_movie($title, $startdate, $enddate, $cinemaname, $category)
{
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://pepProxy:1027/owner/addMovie',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '
        {
            "Title": "' . $title . '",
            "Start_date": "' . $startdate . '",
            "End_date": "' . $enddate . '",
            "Cinema": "' . $cinemaname . '",
            "Category": "' . $category . '"
        }
',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: 1234'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	// echo $response;
	$res = json_decode($response, true);

	// Orion Entiry Creation
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://orion:1026/v2/entities',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{
    	"id":"'.$res['movieId'].'",
  		"type": "Movie",
  		"Title": {
    	"value": "' . $title . '",
    	"type": "String"
  						},
  		"ID": {
    	"value": "' . $res['movieId'] . '",
    	"type": "String"
  						},
  		"StartDate": {
    	"value": "' . $startdate . '",
    	"type": "String"
  						},
  		"EndDate": {
    	"value": "' . $enddate . '",
    	"type": "String"
  						},
  		"Cinema": {
    	"value": "' . $cinemaname . '",
    	"type": "String"
  		},
  		"Category": {
    	"value": "' . $category . '",
    	"type": "String"
  						}
					}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);

	// echo $response;
	$curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://orion:1026/v2/subscriptions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
  "description": "A subscription to get changed Movie ID",
  "subject": {
    "entities": [
      {
        "id": "'.$res['movieId'].'",
        "type": "Movie"
      }
    ],
    "condition": {
      "attrs": [
        "Title","StartDate","EndDate","Cinema","Category"
      ]
    }
  },
  "notification": {
    "http": {
      "url": "http://restapi:5000/notifications"
    },
    "attrs": [
      "ID"
    ]
  },
  "throttling": 5
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

	exit($res['message']);
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Owners</title>
	<link rel="stylesheet" href="mystyle.css">
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
	</script>
	<!-- Add Cinema Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).ready(function() {
			var frm = $('#addCinema');

			frm.submit(function(e) {

				e.preventDefault();

				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						addcinemasubmit: 1
					},
					success: function(data) {
						console.log('Submission was successful.');
						console.log(frm);
						$('#addCinBtn').html(data);

						$.ajax({
							url: 'owner.php',
							method: 'POST',
							data: {
								showCinemas: 1
							},
							success: function(data) {
								console.log(data);
								$('select').html(data);
								$.ajax({
									url: 'owner.php',
									method: 'POST',
									data: {
										OwnerMovies: 1
									},
									success: function(data) {
										console.log(data);
										$('#table').html(data);
									},
									dataType: 'text'
								});
							},
							dataType: 'text'
						});

					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			});
		});
	</script>
	<!-- Delete Cinema Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).ready(function() {
			var frm = $('#deleteCinema');

			frm.submit(function(e) {

				e.preventDefault();

				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						deletecinemasubmit: 1
					},
					success: function(data) {
						//console.log('Submission was successful.');
						console.log(data);
						$('#delCinBtn').html(data);
						$.ajax({
							url: 'owner.php',
							method: 'POST',
							data: {
								showCinemas: 1
							},
							success: function(data) {
								console.log(data);
								$('select').html(data);
								$.ajax({
									url: 'owner.php',
									method: 'POST',
									data: {
										OwnerMovies: 1
									},
									success: function(data) {
										console.log(data);
										$('#table').html(data);
									},
									dataType: 'text'
								});
							},
							dataType: 'text'
						});
					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			});
		});
	</script>
	<!-- Add Movie Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).ready(function() {
			var frm = $('#add-movie');

			frm.submit(function(e) {

				e.preventDefault();

				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						addMoviesubmit: 1
					},
					success: function(data) {
						console.log('Submission was successful.');
						//console.log(data);
						$('#addMovieBtn').html(data);
						$.ajax({
							url: 'owner.php',
							method: 'POST',
							data: {
								OwnerMovies: 1
							},
							success: function(data) {
								console.log(data);
								$('#table').html(data);
							},
							dataType: 'text'
						});
					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			});
		});
	</script>
	<!-- Delete and Update Movie Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).on("submit", '.ownerform', function(e) {
			var btn = ($(document.activeElement));
			var frm = $(this).closest('form');
			// alert(frm.prop('id'));
			// alert(btn.attr('name'));
			if (btn.attr('name') == "deleteMovie") {
				e.preventDefault();
				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						UpdateMovie: 0,
						deleteMovie: 1
					},
					success: function(data) {
						console.log('Submission was successful.');
						console.log(data);
						$('#table').html(data);
					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			} else if (btn.attr('name') == "UpdateMovie") {
				e.preventDefault();
				$.ajax({
					type: frm.attr('method'),
					url: frm.attr('action'),
					data: {

						form: frm.serialize(),
						UpdateMovie: 1,
						deleteMovie: 0
					},
					success: function(data) {
						console.log('Submission was successful.');
						//console.log(data);
						$("#update" + frm.prop('id')).html(data);
					},
					error: function(data) {
						console.log('An error occurred.');
						console.log(data);
					},
				});
			}
		});
	</script>
	<!-- Show Movies Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				url: 'owner.php',
				method: 'POST',
				data: {
					OwnerMovies: 1
				},
				success: function(data) {
					console.log(data);
					$('#table').html(data);
				},
				dataType: 'text'
			});
		});
	</script>
	<!-- Show Cinemas Jquerry Event for ajax -->
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				url: 'owner.php',
				method: 'POST',
				data: {
					showCinemas: 1
				},
				success: function(data) {
					console.log(data);
					$('select').html(data);
				},
				dataType: 'text'
			});
		});
	</script>

</head>

<body>
	<div class="page">
		<h1>Cinema Owner Panel</h1>

		<div id="dashboard">
			<!-- Form working as dashboard for menu and logout -->
			<form form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div id="user-info">
					<?php echo $_SESSION['username']; ?>
					<?php echo "(" . $_SESSION['role'] . ")";
					?>


					<button type="submit" name="logout" id="logout"> <i class="material-icons">power_settings_new</i>
						Logout</button>
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
		<!-- Add bar for cinema -->
		<form class="add" id="addCinema" action="owner.php" method="post">
			<label> <i class="material-icons">&#xe147;</i>Add your Cinema</label>

			<input type="text" name="addcinemaname" id="addcinemaname" placeholder="Your Cinema Name" required>

			<button type="submit" id="addCinBtn" name="addcinemasubmit">Add</button>
		</form>
		<!-- Delete bar for cinema -->
		<form class="add" id="deleteCinema" action="" method="post">
			<label><i class="material-icons">&#xe15c;</i>Delete you Cinema</label>

			<select type="select" id="select" name="cinemaname" placeholder="Cinema Name" id="cinemaname" required>
				<option value="" selected disabled hidden>Select Cinema</option>

			</select>
			<button type="submit" id="delCinBtn" name="deletecinemasubmit"> Delete </button></button>
			<div id="delete_cinema_error_msg">
				<?php echo "<p style='color:red;' >" . $delete_cinema_error_msg . "</p>"; ?>
			</div>
		</form>
		<div>
			<!-- add bar for movie -->
			<form class="add" id="add-movie" action="" method="post">
				<label><i class="material-icons">&#xe145;</i>Add a Movie</label>

				<input type="text" name="title" placeholder="Title" id="title" required>
				<input type="text" name="startdate" onfocus="(this.type='date')" onblur="(this.type='search')" placeholder="Start Date" id="startdate" required>
				<input type="text" name="enddate" onfocus="(this.type='date')" onblur="(this.type='search')" placeholder="End Date" id="enddate" required>
				<select type="select" id="select" name="cinemaname" placeholder="Cinema Name" id="cinemaname" required>
					<option value="" selected disabled hidden>Select Cinema</option>
				</select>
				<input type="text" name="category" placeholder="Category" id="category" required>
				<button type="submit" id="addMovieBtn" name="addmoviesubmit">Add</button>
				<div id="add_movie_error_msg">
					<?php echo "<p style='color:red;' >" . $add_movie_error_msg . "</p>"; ?>
				</div>
			</form>
		</div>
		<div>
			<!-- Movies that this owner has added -->
			<table id="table">
				<br>
			</table>

			<div id="error_msg">
				<?php echo "<p style='color:red;' >" . $error_msg . "</p>";
				?>
			</div>
		</div>
	</div>

</body>

</html>