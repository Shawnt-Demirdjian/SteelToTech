<?php
	// start or resume session
	session_start();

	// redirect away if not logged in
	if($_SESSION['userID'] <= 0){
		header('Location: /');
		die();
	}

	// Get Config File
	include_once('./config.php');

	// connect to database
	$link = new mysqli("localhost", constant("user"), constant("password"), "steelt10_demi");
	if($link->connect_errno){
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	// Get 10 most recent albums
	$res = $link->query("SELECT id, title, location, uploadDate FROM albums ORDER BY uploadDate DESC LIMIT 10");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link rel="stylesheet" href="./css/layout.css">
	<link rel="stylesheet" href="./css/search.css">
	<style>#search{color:white !important; text-decoration: underline;}</style>
	<title>Search</title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<h1 class="text-center mt-4">Search</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto mb-5 bg-light">
		<div class="container-fluid d-flex" id="results">
			<?php
			// Display 10 most recent albums
			$curr;
			for($i=$res->num_rows; $i>0; $i--){
				$curr = $res->fetch_assoc();
				$thumbName = $link->query("SELECT name from media WHERE parent={$curr['id']} ORDER BY uploadDate DESC LIMIT 1");
				$thumbName = $thumbName->fetch_assoc();
				echo
				'<div class="">
					<div class="card m-4">
						<img class="card-img-top" src="/media/'.$thumbName["name"].'">
						<div class="card-body">
							<h3 class="card-title"><a href="/album/'.urlencode($curr["title"]).'">'.$curr["title"].'</a></h3>
							<h5 class="card-subtitle">'.$curr["location"].'</h5>
							<h5 class="card-subtitle">'.date("F jS, Y", strtotime($curr["uploadDate"])).'</h5>
						</div>
					</div>
				</div>';
			}

			$link->close();
			?>
		</div>
	</div>
	<?php require 'includes/footer.php';?>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
</body>
</html>