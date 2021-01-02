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
    session_destroy();
    header("location: index.php");
}
$cinemaname = "";
$categoory = "";
$start_date = "";
$title = "";
$favButton = "Add to Favorites";
$movie_id = "";
$favMessage = "";
$firstTime = "0";
//show all movies

/*If user tryies to search for movies go inside this functionality*/
if (isset($_POST['search'])) {
    $firstTime = "1";
    parse_str($_POST['form'], $form);

    //echo $form['cinemaname'];
    $cinemaname = $form['cinemaname'];
    $categoory = $form['categoory'];
    $start_date = $form['start_date'];
    $title = $form['title'];
    $firstTime == '1';
    //echo $cinemaname;

}

/*Querry for Movie table selection. 
Search field work as filters. When they are all blank shows all movies*/
if (isset($_POST['allMovies'])) {
    $response="";


    $requestBody="{";
        if($cinemaname!=""){ if($requestBody!="{"){$requestBody.=',';}  $requestBody.='"Cinema":"'.$cinemaname.'"';}
        if($categoory!=""){if($requestBody!="{"){$requestBody.=',';} $requestBody.='"Category":"'.$categoory.'"';}
        if($start_date!=""){if($requestBody!="{"){$requestBody.=',';} $requestBody.='"Start_date":"'.$start_date.'"';}
        if($title!=""){if($requestBody!="{"){$requestBody.=',';}$requestBody.='"Title":"'.$title.'"';}

    $requestBody.="}";

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://pepProxy:1027/movies',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $requestBody,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234'
      ),
    ));  
    $res = curl_exec($curl);    
    curl_close($curl);

    $result = json_decode($res, true);


    // /*When there are movies to display*/

     if (count($result) >= 1) {
        /*fetch each movie and check if is added in favorites*/
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
                <th>
                    Favorite
                </th>

            </tr>
            <br>';
        foreach ($result as $row) { // Important line !!! 
            /*display the right message*/
            if ($row['_id'] != $movie_id) {
                $favButton = "Add to Favorites";
            } elseif ($favMessage != "") {
                $favButton = $favMessage;
                $favMessage = "";
            }

            $response .= ' <form class="favform" id="formId' . $row['_id'] . '" action="movies.php" method="post"> <tr>';
            /*Create the rows in order to show movie attributes*/
            $response .= '<td><input type="text" form="formId' . $row['_id'] . '" name="movie_id" value=' . $row['_id'] . ' id="movie_id' . $row['_id'] . '" readonly></td>';
            /*Print all the fields of the movie*/
            foreach ($row as $field => $value) {
                if ($field == '_id' || $field == '__v') {
                    continue;
                }
                if ($field == 'Start_date' || $field == 'End_date') {
                    $date = explode("T", $value)[0];
                    $response .= '<td form="formId' . $row['_id'] . '">'
                    . $date .
                    "</td>";
                    $response .= $date;
                    continue;
                }
                else{
                $response .= '<td form="formId' . $row['_id'] . '">'
                    . $value .
                    "</td>";
                $response .= $value;
                }
            }
            $response .= '<td>';
            $response .= '

									<button type="submit" form="formId' . $row['_id'] . '" class="favbut" id="formId' . $row['_id'] . '" name="Add_to_favorites" >' . $favButton . ' </button>

									</td>
									';

            $response .= "</form></tr>";
        }
    } else {
        /*if no movie to display. Print error message*/
        $response .= '<p style="display:flex; align-self:center;">No results';
    }
    $firstTime=='1';
    exit($response);
}

/*When favoties button is pressed start this functionality*/
if (isset($_POST['Add_to_favorites'])) {
    parse_str($_POST['form'], $form);
    $movie_id = $form['movie_id'];
    $user_id =  $_SESSION['id'];
    /*Check if is already in favorites*/

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://pepProxy:1027/movies/addToFavorites',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
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
    $res=json_decode($response, true);

    
    // echo $response;
    exit ($res['message']);
 
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Movies</title>
    <link rel="stylesheet" href="mystyle.css">
    <!-- Google library for icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            var frm = $('#search');

            frm.submit(function(e) {

                e.preventDefault();

                $.ajax({
                    type: frm.attr('method'),
                    url: frm.attr('action'),
                    data: {

                        form: frm.serialize(),
                        search: 1,
                        allMovies: 0
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
                url: 'movies.php',
                method: 'POST',
                data: {
                    allMovies: 1
                },
                success: function(data) {
                    console.log(data);
                    $('#table').html(data);

                },
                dataType: 'text'
            });

        });
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
                        Add_to_favorites: 1
                    },
                    success: function(data) {
                        console.log('Submission was successful.');
                        //console.log(data);
                        $("button.favbut" +"#"+ frm.prop('id')).html(data);
                    },
                    error: function(data) {
                        console.log('An error occurred.');
                        console.log(data);
                    },
                });
            });
        });
    </script>

</head>

<body>

    <div class="page">

        <h1>Movies</h1>

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
                <i class="material-icons"> &#xe8fe;</i> Menu
            </div>

            <div class="dropdown-content">
                <a href="movies.php">Movies</a>
                <a href="owner.php">Owners</a>
                <a href="administrator.php">Administrators</a>
            </div>
        </div>
        </form>

        <div class="search">
            <!-- Search bar  -->
            <form id="search" action="movies.php" method="post">
                <i class="material-icons">&#xe8b6;</i>
                <label>Search the site:</label>
                <input type="search" name="cinemaname" aria-label="Search By Cinema" placeholder="Cinema">
                <input type="search" name="categoory" aria-label="Search By Category" placeholder="Category">
                <input type="search" onfocus="(this.type='date')" onblur="(this.type='search')" name="start_date" aria-label="Search By Start Date" placeholder="Start Date">
                <input type="search" name="title" aria-label="Search By Title" placeholder="Title">


                <button type="submit" name="search" id="searchbtn">Search</button>
            </form>
        </div>

        <!-- favorites button -->
        <a id="favoritesbtn" href="favorites.php">
            <button>See all your Favorites</button>
        </a>

        <!-- Movies Table -->
        <table id="table">
            <br>
        </table>
    </div>
    </div>
</body>

</html>