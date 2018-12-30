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
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link rel="stylesheet" href="/css/layout.css">
	<link rel="stylesheet" href="/css/view-album.css">
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
				<div class="row no-gutters justify-content-center">
					<form action="" method="post" id="media">
						<h3>Remove Pictures</h3>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
						<div class="form-group d-flex col-12 justify-content-center media-container">
							<?php
								$videos = array();
								$nameIndex = 0;
								for ($nameIndex; $nameIndex < $resMedia->num_rows; $nameIndex++) {
									$currentMedia = $resMedia->fetch_assoc()["name"];								
									if (preg_match("/video/", mime_content_type("./media/" . $currentMedia)) == 1) {
										// Video Type
										array_push($videos, $currentMedia);
										// echo '<video type="video/mp4" controls class="img-fluid" src="/media/' . $currentMedia . '"></video>';
									} else {
										// Image Type
										echo '<div class="media-item text-center">';
										echo '<div class="btn-group rotate-buttons" role="group">';
										echo '<button type="button" class="btn btn-sm rotate-left-btn"><i class="fas fa-undo"></i></button>';
										echo '<button type="button" class="btn btn-sm rotate-right-btn"><i class="fas fa-redo"></i></button>';
										echo '<button type="button" class="btn btn-sm save-rotation"><i class="far fa-save"></i></button></div>';
										echo '<img data-angle="0" class="album-image mb-4" src="/media/' . $currentMedia . '">';
										echo '<input class="media-checkbox" name="' . ($nameIndex+1) . '" value="' . $currentMedia . '" type="checkbox">';
										echo '</div>';
									}								
								}
							?>
						</div>
						<h3>Remove Videos</h3>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
						<div class="form-group d-flex col-12 justify-content-center media-container">
							<?php
								foreach ($videos as &$name){
									echo '<div class="media-item">';
									echo '<video type="video/mp4" controls class="album-video mb-5 align-middle " src="/media/' . $name . '"></video>';
									echo '<input class="media-checkbox" name="' . (++$nameIndex) . '" value="' . $name . '" type="checkbox">';
									echo '</div>';								
								}
							?>
						</div>
						<div class="form-group col-12">
							<button class="btn btn-danger float-right" type="button" data-toggle="modal" data-target="#delete-media-modal">Delete Selected</button>
							<input class="d-none" type="number" name="albumID" value= "<?php echo $row['id']; ?>">
						</div>
						<!-- Delete Media Modal -->
						<div class="modal fade" tabindex="-1" role="dialog" id="delete-media-modal">
							<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Are You Sure?</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<div class="mx-auto row justify-content-center">
											<div class="form-group text-center">
												<h3 class="col-12">This is NOT reversable!</h3>
												<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
												<button form="media" type="submit" name="submit" value="del" class="btn btn-danger col-6">Delete Selected</button>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
									</div>
								</div>
							</div>
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
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js" integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="/js/edit-album-media.js"></script>
</body>
</html>