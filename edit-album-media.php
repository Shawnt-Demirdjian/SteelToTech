<?php
// start or resume session
session_start();

// redirect away if not logged in
if (!isset($_SESSION['userID'])) {
	header('Location: /');
	die();
}

// Get Config File
include_once './config.php';

// connect to database
$link = new mysqli("localhost", constant("user"), constant("password"), "steelt10_demi");
if ($link->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "del") {
	// Delete Photos Form
	$albumID = filter_var($_POST["albumID"], FILTER_VALIDATE_INT);
	if($albumID){
		foreach ($_POST as $key => $value){
			// sanitize value
			$value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			if ($key != "submit" && $key != "albumID"){
				// $value is the name of the file to be deleted.
				$res = $link->query("DELETE FROM media WHERE name = '{$value}' AND parent= '{$albumID}' ");
				unlink("media/".$value);
			}
		}
	}


} else if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "add") {
	$success = true;
	// Add Photos Form
	$albumID = filter_var($_POST["albumID"], FILTER_VALIDATE_INT);
	if($albumID){
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
			$uploadDate = date("\n Y-m-d");
			// Insert all Media
			for($i= 0; $i < count($_FILES['media']['type']); $i++){
				// Generate unique file name
				$uniqueFileName = preg_replace('/[^a-z0-9-_+A-Z.]+/', '-', uniqid(bin2hex(random_bytes(5))) . basename($_FILES['media']['name'][$i]));
				// Insert into Database
				$link->query("INSERT INTO media (uploader, uploadDate, parent, name) VALUES ('{$_SESSION['userID']}','{$uploadDate}','{$albumID}','{$uniqueFileName}')");
				// Move to /media
				move_uploaded_file($_FILES['media']['tmp_name'][$i], "media/" . $uniqueFileName);
			}
		}
	}
}

// Check if this album exists
$title = $_GET['title'];
$exists = true;
$res = $link->query("SELECT * FROM albums WHERE title LIKE '{$title}'");
$row = $res->fetch_assoc();
if ($res->num_rows <= 0) {
	// Album does not exists
	$exists = false;
} else {
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
	<link rel="stylesheet" href="/css/layout.css">
	<link rel="stylesheet" href="/css/edit-album-media.css">
	<title><?php echo $row['title']; ?></title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<?php if ($exists): ?>
			<!-- The Album does exist -->
			<div class="container-fluid">
				<div class="row justify-content-around mt-4">
					<div class="text-center">
						<h1><?php echo $row['title']; ?></h1>
						<h5><?php echo $row['location']; ?> | <?php echo date("F jS, Y", strtotime($row['eventDate'])); ?></h5>
						<!-- Edit Album Buttons -->
						<div class="d-flex justify-content-center btn-group">
							<a href="/view-album/<?php echo urlencode($title); ?>" class="btn btn-sm btn-outline-info">View Album</a>
							<a href="/edit-album-info/<?php echo urlencode($title); ?>" class="btn btn-sm btn-outline-info">Edit Album Info</a>
							<a href="#" class="btn btn-sm btn-info">Edit Album Media</a>
						</div>
						<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $success): ?>
							<h4 class="text-center valid-feedback d-block "><?php echo $successMessage; ?></h4>
						<?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
							<h4 class="text-center invalid-feedback d-block "><?php echo $failMessage; ?></h4>
						<?php endif;?>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					</div>
				</div>
				<h3>Remove Pictures</h3>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
				<div class="row no-gutters justify-content-center">
					<form class="col-12 d-flex" id="media" action="" method="post">
						<?php
							for ($i = 0; $i < $resMedia->num_rows; $i++) {
								$currentMedia = $resMedia->fetch_assoc()["name"];
								echo '<div class="media-item">';
								if (preg_match("/video/", mime_content_type("./media/" . $currentMedia)) == 1) {
									// Video Type
									echo '<video type="video/mp4" controls class="img-fluid" src="/media/' . $currentMedia . '"></video>';
								} else {
									// Image Type
									echo '<img class="img-fluid" src="/media/' . $currentMedia . '">';
								}
								echo '<input class="media-checkbox" name="' . ($i+1) . '" value="' . $currentMedia . '" type="checkbox">';
								echo '</div>';
							}
						?>
						<div class="form-group col-12">
							<button type="submit" name="submit" value="del" class="btn btn-danger float-right">Delete Selected</button>
							<input class="d-none" type="number" name="albumID" value= "<?php echo $row['id']; ?>">
						</div>
					</form>
				</div>
				<div>
					<h3>Add Pictures</h3>
					<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="form-group my-5 d-flex justify-content-center align-items-center">
							<div class="text-center border border-white py-1 mr-1">
								<label for="media[]">Upload Media</label>
								<h4 class="invalid-feedback d-block"><?php echo $mediaErr; ?></h4>
								<input class="offset-2" type="file" name="media[]" accept="video/*,image/*" multiple>
								<input class="d-none" type="number" name="albumID" value= "<?php echo $row['id']; ?>">
							</div>
							<button type="submit" name="submit" value="add" class="btn btn-success ml-1">Add Pictures</button>
						</div>
					</form>
				</div>
			</div>
		<?php else: ?>
			<!-- The Album does not exist -->
			<h1 class="text-center mt-5">There is no album with the title</h1>
			<h1 class="text-center mt-1">"<?php echo $title; ?>"</h1>
		<?php endif;?>
	</div>
	<?php require 'includes/footer.php';?>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
</body>
</html>