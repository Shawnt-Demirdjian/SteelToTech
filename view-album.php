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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css"
			integrity="sha256-Vzbj7sDDS/woiFS3uNKo8eIuni59rjyNGtXfstRzStA=" crossorigin="anonymous" />
		<link rel="stylesheet" href="/css/layout.css">
		<link rel="stylesheet" href="/css/view-album.css">
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
					<div class="mt-4 col-md-4 order-md-1 col-6 order-2">
						<h5>Description</h5>
						<p><?php echo $row['description'];?></p>
					</div>
					<div class="col-md-4 order-md-2 col-12 order-1 text-center">
						<h1><?php echo $row['title'];?></h1>
						<h5><?php echo $row['location'];?> | <?php echo date("F jS, Y", strtotime($row['eventDate']));?>
						</h5>
						<!-- Edit Album Buttons -->
						<div class="d-flex justify-content-center btn-group">
							<a href="#" class="btn btn-sm btn-info">View Album</a>
							<a href="/edit-album-info/<?php echo urlencode($albumID);?>"
								class="btn btn-sm btn-outline-info">Edit Album Info</a>
							<a href="/edit-album-media/<?php echo urlencode($albumID);?>"
								class="btn btn-sm btn-outline-info">Edit Album Media</a>
						</div>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					</div>
					<div class="mt-4 col-md-4 col-6 order-3 text-right">
						<h5>Participants</h5>
						<p><?php echo $row['participants'];?></p>
					</div>
				</div>
				<h3>Pictures</h3>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
				<div class="row no-gutters justify-content-center">
					<div class="col-12 mb-5 text-center">
						<?php
							$videos = array();
							for($i=0; $i < $resMedia->num_rows; $i++){
								$currentMedia = $resMedia->fetch_assoc()["name"];
								
								if (preg_match("/video/",mime_content_type("./media/source/" . $currentMedia)) == 1){
									// Video Type
									array_push($videos, $currentMedia);
								}else{
									// Image Type
									echo
									"<a href='/media/large/{$currentMedia}' data-fancybox='gallery' class='album-picture-link'>
										<picture class='album-picture'>
											<source data-srcset='/media/medium/{$currentMedia}' media='(max-width: 576px)'>
											<img class='col-lg-2 col-md-4 col-sm-6 col-12 mb-4 album-image' data-src='/media/small/{$currentMedia}'>
										</picture>
									</a>";
								}								
							}
						?>
					</div>
				</div>
				<h3>Videos</h3>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 bg-light ml-0">
				<div class="row no-gutters justify-content-center">
					<div class="col-12 text-center">
						<?php
							foreach ($videos as &$name){
								echo '<video type="video/mp4" controls class="align-middle col-lg-2 col-md-4 col-sm-6 col-12 album-video mb-5" data-src="/media/source/'.$name.'"></video>';
							}
						?>
					</div>
				</div>
			</div>
			<?php else: ?>
			<!-- The Album does not exist -->
			<h1 class="text-center mt-5">That album doesn't seem to exist.</h1>
			<?php endif; ?>
		</div>
		<?php require 'includes/footer.php';?>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"
			integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js"
			integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
		</script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"
			integrity="sha256-yt2kYMy0w8AbtF89WXb2P1rfjcP/HTHLT7097U8Y5b8=" crossorigin="anonymous"></script>
		<script src="/js/view-album.js"></script>
	</body>

</html>