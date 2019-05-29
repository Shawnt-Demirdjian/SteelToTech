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
			if(preg_match('/image\/jpeg|video\/mp4|video\/quicktime/', $currType) != 1){
				// Incorrect Type
				$success = false;
				$mediaErr = "One or more of your files are not of the accepted file types.";
			}
		}

		if($success){
			// Form is Valid
			// Create Album Entity
			$res = $link->query("INSERT INTO albums (uploadDate, eventDate, creator, title, description, location, participants) VALUES ('{$uploadDate}','{$eventDate}','{$_SESSION['userID']}', '{$title}', '{$description}','{$location}','{$participants}')");
			if($res){
				// successfully inserted Album
				$newAlbumID = $link->insert_id;
				// Insert all Media
				for($i= 0; $i < count($_FILES['media']['type']); $i++){
					// Generate unique file name
					$uniqueFileName = preg_replace('/[^a-z0-9-_+A-Z.]+/', '-', uniqid(bin2hex(random_bytes(5))) . basename($_FILES['media']['name'][$i]));
					// Insert into Database
					$link->query("INSERT INTO media (uploader, uploadDate, parent, name) VALUES ('{$_SESSION['userID']}','{$uploadDate}','{$newAlbumID}','{$uniqueFileName}')");
					// Move to /media
					move_uploaded_file($_FILES['media']['tmp_name'][$i], "media/" . $uniqueFileName);
					
					if(preg_match('/image\/jpeg/', $_FILES['media']['type'][$i]) == 1){
						// Create thumbnail for JPEGS only (ignore videos)
						$thumbnail = imagecreatefromjpeg("media/" . $uniqueFileName);
						$resolution = getimagesize("media/" . $uniqueFileName);
						// Scale thumbnail
						if($resolution[0] > $resolution[1]){
							// Landscape
							$thumbnail = imagescale($thumbnail, 150);
						}else{
							// Portrait
							$thumbnail = imagescale($thumbnail, 75);
						}
						// Save thumbnail
						$resulttemp = imagejpeg($thumbnail, "thumbnails/" . $uniqueFileName, 100);
					}
				}
				header('Location: /view-album/'. $newAlbumID);
				$link->close();
				die();
			}else{
				// failed to insert Album
			}
		}
	}
	
	$link->close();
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
		<link rel="stylesheet" href="/css/loading.css">
		<style>
			#create-album {
				color: white !important;
				text-decoration: underline;
			}
		</style>
		<title>Create Album</title>
	</head>

	<body>
		<?php require 'includes/header.php';?>
		<div class="singlePageContainer">
			<h1 class="text-center mt-4">Create Album</h1>
			<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
			<form action="/create-album" method="post" class="useLoader container-fluid my-5"
				enctype="multipart/form-data">
				<div class="row justify-content-center">
					<div class="col-11 col-md-6 border border-white d-flex justify-content-center align-items-center">
						<div class="form-group">
							<div class="text-center">
								<label for="media[]">Upload Media</label>
								<h4 class="invalid-feedback d-block"><?php echo $mediaErr;?></h4>
								<input class="offset-2" type="file" name="media[]" accept=".jpeg, .jpg, .mov, .mp4"
									multiple>
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
		<div id="loader-background"></div>
		<div id="loader">
			<img id="loader-sword" class="loader-icon animated slow" src="/images/sword.svg"/>
			<img id="loader-code" class="loader-icon animated slow" src="/images/html-coding.svg"/>
			<h2 id="loader-message">Loading...</h2>
		</div>

		<?php require 'includes/footer.php';?>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"
			integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js"
			integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
		</script>
		<script src="/js/loading.js"></script>
	</body>

</html>