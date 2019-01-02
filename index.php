<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
	<link rel="stylesheet" href="./css/layout.css">
	<link rel="stylesheet" href="./css/index.css">
	<title>Demirdjian Family Home</title>
</head>
<body class="container">
	<div class="singlePageContainer">
		<h1 class="header-font text-center mt-5"><a href="/" class=" home-link">Demirdjian Family Home</a></h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">

		<div class="d-flex justify-content-center my-4">
			<?php if($_SESSION['userID'] > 0):?>
				<a href="/search" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1">Albums</a>
			<?php else: ?>
				<a href="/login" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1">Albums</a>
			<?php endif; ?>
				<a href="https://steeltotech.com:2096" target="_blank" rel="noopener noreferrer" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1 ml-4">Webmail</a>
		</div>

		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light mt-4">
		<h4 class="text-center">Others Tolerated</h4>
	</div>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js" integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
