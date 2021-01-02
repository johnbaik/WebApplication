<?php
/*Start session */
session_start();

/*If user is already logged in redirect to welcome page*/
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: index.php");
	exit;
}
/*Connect to database*/
// require_once "serverconn.php";
/*Logout functionality*/
if (isset($_POST['logout'])) {
	$_SESSION["loggedin"] = false;
	header("location: index.php");
	/*Check if user has correct privilages*/
}
if ($_SESSION["role"] != "Admin") {
	//Redirect to resctricted page
	header("location: restricted.php");
}

$error_msg;
/*if delete button is pressed start this functionality*/
if (isset($_POST['deleteID']) && isset($_POST['id'])) {
	delete($_POST['id']);
}
/*if update buttonn is pressed start this functionality*/
if (isset($_POST['UpdateID']) && isset($_POST['id'])) {
	update($_POST['id'], $_POST['name'], $_POST['surname'], $_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'], $_POST['confirmed']);
} else if ($error_msg = "") {
	$error_msg = "";
}

function delete($res)
{
	/*Querry to delete a user*/
	$sql = "DELETE FROM `Users` WHERE `ID` = ?";
	global $con;
	/*if querry is ok execute or display error*/
	if ($stmt = mysqli_prepare($con, $sql)) {
		// Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "i", $res);

		if (mysqli_stmt_execute($stmt)) {
		} else {
			echo "Something went wrong. Please try again later.";
		}
		mysqli_stmt_close($stmt);
	}
}

function update($id, $name, $surname, $username, $password, $email, $role, $confirmed)
{

	global $con;
	global $error_msg;

	/*get the ID from the new username*/
	$sql1 = "SELECT `ID` FROM `Users` WHERE `USERNAME` LIKE '" . $username . "'";
	$id_from_username = mysqli_query($con, $sql1);
	$result_id = mysqli_fetch_assoc($id_from_username);
	/*Get the username from the new id*/
	$sql2 = "SELECT `USERNAME` FROM `Users` WHERE `ID` LIKE '" . $id . "'";
	$username_from_id = mysqli_query($con, $sql2);
	$result_username = mysqli_fetch_assoc($username_from_id);

	/*Check if username already exists or if it is its own and execute else print message*/
	if ((mysqli_num_rows($username_from_id) == 1)) {
		if (mysqli_num_rows($id_from_username) == 1 && ($id == $result_id['ID'])) {
			$sql = "UPDATE `Users` SET `NAME` = '" . $name . "', `SURNAME`='" . $surname . "', `USERNAME`='" . $username . "', `PASSWORD`='" . $password . "', `EMAIL`='" . $email . "', `ROLE`='" . $role . "', `CONFIRMED`='" . $confirmed . "' WHERE	`Users`.`ID`=" . $id;

			mysqli_query($con, $sql);
		} else if (mysqli_num_rows($id_from_username) == 0) {

			$sql = "UPDATE `Users` SET `NAME` = '" . $name . "', `SURNAME`='" . $surname . "', `USERNAME`='" . $username . "', `PASSWORD`='" . $password . "', `EMAIL`='" . $email . "', `ROLE`='" . $role . "', `CONFIRMED`='" . $confirmed . "' WHERE	`Users`.`ID`=" . $id;

			mysqli_query($con, $sql);
		} else {
			$error_msg = " The username already exists";
		}
	} else {


		$error_msg = 'Sorry, you cannot change the id !';
	}
}


?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Administrator</title>
	<link rel="stylesheet" href="mystyle.css">
	<!-- Google library for icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>

<body>
	<div class="page">
		<h1>Administrator</h1>

		<div id="dashboard">
			<!-- Form working as dashboard for menu and logout -->
			<form form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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


		<div id="upadate_error_msg" style='display:flex; align-self: center;'>
			<?php echo "<p style='color:red;' >" . $error_msg . "</p>"; ?>
		</div>

		<div>
			<p id="errorMsg" style="color: crimson;">Please use Keyrock as an admin to make changes to the Users</p>
			<!-- users table -->
			<!-- <table id="admin-table">
				<thead>
					<th>
						ID
					</th>
					<th>
						Name
					</th>
					<th>
						Surname
					</th>
					<th>
						Username
					</th>
					<th>
						Password
					</th>
					<th>
						Email
					</th>
					<th>
						Role
					</th>
					<th>
						Confirmed
					</th>
					<th>

					</th>
					<th>

					</th>
				</thead>
				<br> -->

				<?php
				// /*querry to fetch all users and their attributes*/
				// $sql = "SELECT `ID`, `NAME`, `SURNAME`,`USERNAME`,`PASSWORD`, `EMAIL`,`ROLE`, `CONFIRMED` FROM `Users` ";

				// // $result = mysqli_query($con, $sql);
				// /*print a row for each user*/
				// // while ($row = mysqli_fetch_assoc($result)) {
				// 	echo '<form action="" method="post">';
				// 	echo "<tr>";
				// 	echo '
				// 	<td>
				// 	<input type="text" name="id" value=' . $row['ID'] . ' id="id" readonly required>
				// 	</td>
				// 	<td>
				// 	<input type="text" name="name" value="' . $row['NAME'] . '" id="name"  required>
				// 	</td>
				// 	<td>
				// 	<input type="text" name="surname" value="' . $row['SURNAME'] . '" id="surname" required>
				// 	</td>
				// 	<td>
				// 	<input type="text" name="username" value="' . $row['USERNAME'] . '" id="username" required>
				// 	</td>
				// 	<td>
				// 	<input type="text" name="password" value="' . $row['PASSWORD'] . '" id="password" required>
				// 	</td>
				// 	<td>
				// 	<input type="email" name="email" value="' . $row['EMAIL'] . '" id="email" required>
				// 	</td>
				// 	<td>
				// 	<select id="role" name="role" >
				// 	<option value="' . $row['ROLE'] . '" selected hidden >' . $row['ROLE'] . '</option>
				// 	<option value="ADMIN">Admin</option>
				// 	<option value="CINEMAOWNER">Cinema Owner</option>
				// 	<option value="USER">User</option>
				// 	</select>
				// 	</td>
				// 	<td> 
				// 	<input type="text" pattern="[0,1]" title="ONLY 1(CONFIRMED) OR 0(UNCONFIRMED)" name="confirmed" value="' . $row['CONFIRMED'] . '" id="confirmed" required>
				// 	</td>
				// 	';
				// 	echo "<td>";
				// 	echo ("<button type='submit' name='deleteID'>Delete</button>");
				// 	echo "</td>";
				// 	echo "<td>";
				// 	echo ("<button type='submit' name='UpdateID'>Update</button>");
				// 	echo "</td>";
				// 	echo "</tr>";
				// 	echo "</form>";
				// // }
				?>
			</table>
		</div>
	</div>
</body>

</html>