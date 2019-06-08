<?php
	// start or resume session
	session_start();

	// redirect away if not logged in
	if(!isset($_SESSION['userID'])){
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
	$res = $link->query("SELECT id, title, location, eventDate FROM albums ORDER BY eventDate DESC LIMIT 10");

?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
			integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
			crossorigin="anonymous" />
		<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
		<link rel="stylesheet" href="./css/layout.css">
		<link rel="stylesheet" href="./css/search.css">
		<style>
		#search {
			color: white !important;
			text-decoration: underline;
		}
		</style>
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
					<a class="card-link" href="/view-album/'.$curr["id"].'">
					<div class="card m-4">
						<div class="card-image-container">
							<img class="card-img" data-src="/media/medium/'.$thumbName["name"].'">
						</div>
						<div class="card-body">
							<h3 class="card-title">'.$curr["title"].'</h3>
							<h5 class="card-subtitle">'.$curr["location"].'</h5>
							<h5 class="card-subtitle">'.date("F jS, Y", strtotime($curr["eventDate"])).'</h5>
						</div>
					</div></a>
				</div>';
			}

			$link->close();
			?>
			</div>
		</div>
		<?php require 'includes/footer.php';?>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"
			integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js"
			integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
		</script>
		<script src="/js/search.js"></script>
	</body>

</html>