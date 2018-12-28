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

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Album Update
		$titleErr = $descriptionErr = $eventDateErr = $locationErr = $participantsErr = "";
		$title = $description = $eventDate = $location = $participants = "";
		$success = true;

		// Title Validation
		if (empty($_POST["title"])) {
			$titleErr = "Title is required";
			$success = false;
		}else {
			if(strcasecmp($_POST["title"], $_GET['title']) != 0){
				// Title field was changed
				$res = $link->query("SELECT id FROM albums WHERE title LIKE '{$_POST['title']}'");
				if($res->num_rows > 0 ){
					// Title Taken
					$success = false;
					$titleErr = "Title has been taken";
				}
			}
			$title = $_POST["title"];
			$title = filter_var($title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		// Location Validation
		if (empty($_POST["location"])) {
			$locationErr = "Location is required";
			$success = false;
		}else {
			$location = $_POST["location"];
			$location = filter_var($location, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		// Participants Validation
		if (empty($_POST["participants"])) {
			$participantsErr = "Participants is required";
			$success = false;
		}else {
			$participants = $_POST["participants"];
			$participants = filter_var($participants, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		
		// Description Validation
		if (empty($_POST["description"])) {
			$descriptionErr = "Description is required";
			$success = false;
		}else {
			$description = $_POST["description"];
			$description = filter_var($description, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		// Event Date Validation
		if (empty($_POST["eventDate"])) {
			$eventDateErr = "Event Date is required";
			$success = false;
		}else {
			$eventDate = $_POST["eventDate"];
			$eventDate = filter_var($eventDate, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		if($success){
			// Form is Valid
			// Update Album Entity
			$resUpdate = $link->query("UPDATE albums SET title = '{$title}', location = '{$location}', participants = '{$participants}', description = '{$description}', eventDate = '{$eventDate}' WHERE title LIKE '{$_GET['title']}'");
			if($resUpdate){
				// Update Successful
				$successMessage = "Album update successful!";
				header('Location: /edit-album-info/'.$title);
			}else{
				// Update Failed
				$failMessage = "Album update failed. Please tell Shawnt.";
			}
		}
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

		// Get name of creator
		$creator = $link->query("SELECT first, last FROM accounts WHERE id = {$row['creator']}");
		$creator =$creator->fetch_assoc();
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
					<div class="text-center">
						<h1><?php echo $row['title'];?></h1>
						<h5><?php echo $row['location'];?> | <?php echo date("F jS, Y", strtotime($row['eventDate']));?></h5>
						<!-- Edit Album Buttons -->
						<div class="d-flex justify-content-center btn-group">
							<a href="/view-album/<?php echo urlencode($title);?>" class="btn btn-sm btn-outline-info">View Album</a>
							<a href="#" class="btn btn-sm btn-info">Edit Album Info</a>
							<a href="/edit-album-media/<?php echo urlencode($title);?>" class="btn btn-sm btn-outline-info">Edit Album Media</a>
						</div>
						<?php if($_SERVER["REQUEST_METHOD"] == "POST" && $success):?>
							<h4 class="text-center valid-feedback d-block "><?php echo $successMessage;?></h4>
						<?php elseif($_SERVER["REQUEST_METHOD"] == "POST"): ?>
							<h4 class="text-center invalid-feedback d-block "><?php echo $failMessage;?></h4>
						<?php endif; ?>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					</div>
				</div>
				<h5 class="text-center"><?php echo $creator['first'] .' '. $creator['last'];?> | <?php echo date("F jS, Y", strtotime($row['uploadDate']));?></h5>
				<form class="col-10 mx-auto row" action="" method="post">
					<div class="form-group col-12">
						<label for="title">Title</label>
						<h4 class="invalid-feedback d-block"><?php echo $titleErr;?></h4>
						<input class="form-control" type="text" name="title" value="<?php echo $row['title'];?>" required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="location">Location</label>
						<h4 class="invalid-feedback d-block"><?php echo $locationErr;?></h4>
						<input class="form-control" type="text" name="location" value="<?php echo $row['location'];?>" required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="eventDate">Event Date</label>
						<h4 class="invalid-feedback d-block"><?php echo $eventDateErr;?></h4>	
						<input class="form-control" type="date" name="eventDate" value="<?php echo $row['eventDate'];?>" required>
					</div>
					<div class="form-group col-12">
						<label for="participants">Participants</label>
						<h4 class="invalid-feedback d-block"><?php echo $participantsErr;?></h4>
						<input class="form-control" type="text" name="participants" value="<?php echo $row['participants'];?>" required>
					</div>
					<div class="form-group col-12">
						<label for="description">Description</label>
						<h4 class="invalid-feedback d-block"><?php echo $descriptionErr;?></h4>
						<textarea rows="5" class="form-control" name="description" required><?php echo $row['description'];?></textarea>
					</div>
					<div class="form-group col-12">
						<button type="reset" class="btn btn-danger">Reset</button>
						<button type="submit" value="update" name="submit" class="btn btn-info float-right">Update</button>
					</div>
				</form>
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
</body>
</html>