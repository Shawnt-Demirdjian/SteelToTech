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

	// Check if this album exists
	$title = $_GET['title'];
	$exists = true;
	$res = $link->query("SELECT * FROM albums WHERE title LIKE '{$title}'");
	$row = $res->fetch_assoc();
	if($res->num_rows <= 0 ){
		// Album does not exists
		$exists = false;
	}else{
		// Album exists
		// Get all media in this album
		$resMedia = $link->query("SELECT name FROM media WHERE parent LIKE '{$row['id']}'");
	}

	$link->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox.min.css">
	<link rel="stylesheet" href="/css/layout.css">
	<title><?php echo $row['title'];?></title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<?php if($exists):?>
			<!-- The Album does exist -->
			<div class="container-fluid">
				<div class="row justify-content-around mt-4">
					<div class="mt-4 col-md-4 order-md-1 col-6 order-2">
						<h5>Description</h5>
						<p><?php echo $row['description'];?></p>
					</div>
					<div class="col-md-4 order-md-2 col-12 order-1 text-center">
						<h1><?php echo $row['title'];?></h1>
						<h5><?php echo $row['location'];?> | <?php echo date("F jS, Y", strtotime($row['eventDate']));?></h5>
						<!-- Edit Album Buttons -->
						<div class="d-flex justify-content-center btn-group">
							<a href="#" class="btn btn-sm btn-info">View Album</a>
							<a href="/edit-album-info/<?php echo urlencode($title);?>" class="btn btn-sm btn-outline-info">Edit Album Info</a>
							<a href="/edit-album-media/<?php echo urlencode($title);?>" class="btn btn-sm btn-outline-info">Edit Album Media</a>
						</div>	
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					</div>
					<div class="mt-4 col-md-4 col-6 order-3 text-right">
						<h5>Participants</h5>
						<p><?php echo $row['participants'];?></p>
					</div>
				</div>
				<div class="row no-gutters justify-content-center">
					<div class="col-12 mb-5">
						<?php
							for($i=0; $i < $resMedia->num_rows; $i++){
								$currentMedia = $resMedia->fetch_assoc()["name"];
								echo '<a href="/media/'.$currentMedia.'" data-fancybox="gallery">';
								if (preg_match("/video/",mime_content_type("./media/" . $currentMedia)) == 1){
									// Video Type
									echo '<video type="video/mp4" controls class="col-lg-2 col-md-4 col-sm-6 img-fluid" src="/media/'.$currentMedia.'">';
								}else{
									// Image Type
									echo '<img class="col-lg-2 col-md-4 col-sm-6 img-fluid" src="/media/'.$currentMedia.'">';
								}
								echo '</a>';
							}
						?>
					</div>
				</div>
			</div>
		<?php else: ?>
			<!-- The Album does not exist -->
			<h1 class="text-center mt-5">There is no album with the title</h1>
			<h1 class="text-center mt-1">"<?php echo $title;?>"</h1>
		<?php endif; ?>
	</div>
	<?php require 'includes/footer.php';?>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox.min.js"></script>
</body>
</html>