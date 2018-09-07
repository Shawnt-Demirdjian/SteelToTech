<?php
	// start or resume session
	session_start();

	// redirect away if not logged in
	if($_SESSION['userID'] <= 0){
		header('Location: index.php');
		die();
	}

	// connect to database
	$link = new mysqli("localhost", "root", "xliv11", "steelt10_demi");
	if($link->connect_errno){
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	// Form Validation
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$titleErr = $descriptionErr = $eventDateErr = $mediaErr = $locationErr = $participantsErr = "";
		$title = $description = $eventDate = $location = $participants = "";
		$uploadDate = date("\n Y-m-d");
		$success = true;

		// Title Validation
		if (empty($_POST["title"])) {
			$titleErr = "Title is required";
			$success = false;
		}else {
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

		// Media Validation
		foreach($_FILES['media']['type'] as &$currType){
			// Iterate through all uploaded media and check for bad extensions
			if(preg_match('/image\/|video\//', $currType) != 1){
				// Incorrect Type
				$success = false;
				$mediaErr = "One or more of your files are not of the accepted file types.";
			}
		}

		if($success){
			// Form is Valid
			// Create Album Entity
			$res = $link->query("INSERT INTO albums (uploadDate, eventDate, creator, description, location, participants) VALUES ('{$uploadDate}','{$eventDate}','{$_SESSION['userID']}','{$description}','{$location}','{$participants}')");
			if($res){
				// successfully inserted Album
				$newAlbumID = $link->insert_id;
				// Insert all Media
				for($i= 0; $i < count($_FILES['media']['type']); $i++){
					// Generate unique file name
					$uniqueFileName = uniqid(bin2hex(random_bytes(5))) . basename($_FILES['media']['name'][$i]);
					// Insert into Database
					$link->query("INSERT INTO media (uploader, uploadDate, parent, name) VALUES ('{$_SESSION['userID']}','{$uploadDate}','{$newAlbumID}','{$uniqueFileName}')");
					// Move to /media
					move_uploaded_file($_FILES['media']['tmp_name'][$i], "media/" . $uniqueFileName);
				}
			}else{
				// failed to insert Album
			}
			// Create Media Entities
		}
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
	<link rel="stylesheet" href="./css/layout.css">
	<style>#createAlbum{color:white !important; text-decoration: underline;}</style>
	<title>Create Album</title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<h1 class="text-center mt-4">Create Album</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
		<form action="createAlbum.php" method="post" class="container-fluid my-5" enctype="multipart/form-data">
			<div class="row justify-content-center">
				<div class="col-11 col-md-6 border border-white d-flex justify-content-center align-items-center">
					<div class="form-group">
						<div class="text-center">
							<label for="media[]">Upload Media</label>
							<h4 class="invalid-feedback d-block"><?php echo $mediaErr;?></h4>
							<input class="offset-2" type="file" name="media[]" accept="video/*,image/*" multiple>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 mt-4 mt-md-0 row justify-content-center">
					<div class="form-group col-12 col-sm-6">
						<label for="title">Title</label>
						<h4 class="invalid-feedback d-block"><?php echo $titleErr;?></h4>
						<input class="form-control" type="text" name="title" required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="eventDate">Event Date</label>
						<h4 class="invalid-feedback d-block"><?php echo $eventDateErr;?></h4>	
						<input class="form-control" type="date" name="eventDate" required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="location">Location</label>
						<h4 class="invalid-feedback d-block"><?php echo $locationErr;?></h4>
						<input class="form-control" type="text" name="location" required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="participants">Participants</label>
						<h4 class="invalid-feedback d-block"><?php echo $participantsErr;?></h4>
						<input class="form-control" type="text" name="participants" required>
					</div>
					<div class="form-group col-12">
						<label for="description">Description</label>
						<h4 class="invalid-feedback d-block"><?php echo $descriptionErr;?></h4>
						<textarea class="form-control" name="description" required></textarea>
					</div>
					<div class="form-group col-12">
						<button type="submit" name="submit" class="btn btn-info float-right">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php require 'includes/footer.php';?>
</body>
</html>