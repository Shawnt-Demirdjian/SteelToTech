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

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "update"){
		// Album Update
		$titleErr = $descriptionErr = $eventDateErr = $locationErr = $participantsErr = "";
		$title = $description = $eventDate = $location = $participants = "";
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

		if($success){
			// Form is Valid
			// Update Album Entity
			$resUpdate = $link->query("UPDATE albums SET title = '{$title}', location = '{$location}', participants = '{$participants}', description = '{$description}', eventDate = '{$eventDate}' WHERE id LIKE '{$_GET['albumID']}'");
			if($resUpdate){
				// Update Successful
				$successMessage = "Album update successful!";
				header('Location: /edit-album-info/'.$_GET['albumID']);
			}else{
				// Update Failed
				$failMessage = "Album update failed. Please tell Shawnt.";
			}
		}
	}else if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "delete"){
		$albumID = filter_var($_POST["albumID"], FILTER_VALIDATE_INT);
		// delete all media files
		$res = $link->query("SELECT name FROM media WHERE parent=".$albumID."");
		if($res){
			for($i=0; $i<$res->num_rows; $i++){
				$row = $res->fetch_assoc();
				unlink("media/source/".$row["name"]);
				if(preg_match('/\.jpeg|\.jpg/i', $row["name"]) == 1){
					// unlink scaled copies
					unlink("media/small/".$row["name"]);
					unlink("media/medium/".$row["name"]);
					unlink("media/large/".$row["name"]);
				}
			}
			// delete album and cascades to all media. 
			$res = $link->query("DELETE FROM albums WHERE id=".$albumID."");
			if($res){
				header('Location: /albums');
				$link->close();
				die();
			}
		}
		// one of the preview operations failed.
		$deleteErr = "Album deletion failed. Contact Shawnt.";
	}

	// Check if this album exists
	$albumID = $_GET['albumID'];
	$exists = true;
	$res = $link->query("SELECT * FROM albums WHERE id LIKE '{$albumID}'");
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
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
			integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
			crossorigin="anonymous" />
		<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
		<link rel="stylesheet" href="/css/layout.css">
		<?php if(isset($row['title'])):?>
		<title><?php echo $row['title'];?></title>
		<?php else: ?>
		<title>Album Not Found</title>
		<?php endif; ?>
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
						<h5><?php echo $row['location'];?> | <?php echo date("F jS, Y", strtotime($row['eventDate']));?>
						</h5>
						<!-- Edit Album Buttons -->
						<div class="d-flex justify-content-center btn-group">
							<a href="/view-album/<?php echo $albumID;?>" class="btn btn-sm btn-outline-info">View
								Album</a>
							<a href="#" class="btn btn-sm btn-info">Edit Album Info</a>
							<a href="/edit-album-media/<?php echo $albumID;?>" class="btn btn-sm btn-outline-info">Edit
								Album Media</a>
						</div>
						<?php if(isset($failMessage)):?>
						<h4 class="text-center valid-feedback d-block "><?php echo $failMessage;?></h4>
						<?php endif; ?>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					</div>
				</div>
				<h5 class="text-center"><?php echo $creator['first'] .' '. $creator['last'];?> |
					<?php echo date("F jS, Y", strtotime($row['uploadDate']));?></h5>
				<form class="col-10 mx-auto row" action="" method="post">
					<div class="form-group col-12">
						<label for="title">Title</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($titleErr)) echo $titleErr;?></h4>
						<input class="form-control" type="text" name="title" value="<?php echo $row['title'];?>"
							required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="location">Location</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($locationErr)) echo $locationErr;?></h4>
						<input class="form-control" type="text" name="location" value="<?php echo $row['location'];?>"
							required>
					</div>
					<div class="form-group col-12 col-sm-6">
						<label for="eventDate">Event Date</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($eventDateErr)) echo $eventDateErr;?></h4>
						<input class="form-control" type="date" name="eventDate" value="<?php echo $row['eventDate'];?>"
							required>
					</div>
					<div class="form-group col-12">
						<label for="participants">Participants</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($participantsErr)) echo $participantsErr;?>
						</h4>
						<input class="form-control" type="text" name="participants"
							value="<?php echo $row['participants'];?>" required>
					</div>
					<div class="form-group col-12">
						<label for="description">Description</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($descriptionErr)) echo $descriptionErr;?>
						</h4>
						<textarea rows="5" class="form-control" name="description"
							required><?php echo $row['description'];?></textarea>
					</div>
					<div class="form-group col-12">
						<button type="reset" class="btn btn-danger">Reset</button>
						<button type="submit" value="update" name="submit"
							class="btn btn-info float-right">Update</button>
					</div>
				</form>
				<div class="my-5 mx-auto row justify-content-center">
					<div class="text-center">
						<h3 class="col">Danger Below!</h3>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
						<h4 class="invalid-feedback d-block"><?php if(isset($deleteErr)) echo $deleteErr;?></h4>
						<button class="btn btn-danger col-12" data-toggle="modal"
							data-target="#delete-album-modal">Delete Album</button>
					</div>
				</div>
			</div>
			<?php else: ?>
			<!-- The Album does not exist -->
			<h1 class="text-center mt-5">That album doesn't seem to exist.</h1>
			<?php endif; ?>
		</div>
		<?php require 'includes/footer.php';?>

		<!-- Delete Album Modal -->
		<div class="modal fade" tabindex="-1" role="dialog" id="delete-album-modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Are You Sure?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form class="mx-auto row justify-content-center" action="" method="post">
							<div class="form-group text-center">
								<h3 class="col-12">This is NOT reversable!</h3>
								<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
								<input class="d-none" type="number" name="albumID" value="<?php echo $row['id']; ?>">
								<button class="form-control btn btn-danger col-6" type="submit" name="submit"
									value="delete">Delete Album</button>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js"
			integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js"
			integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
		</script>
	</body>

</html>