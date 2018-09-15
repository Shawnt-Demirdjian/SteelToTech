<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
	<link rel="stylesheet" href="./css/layout.css">
	<link rel="stylesheet" href="./css/index.css">
	<title>Demirdjian Family Archives</title>
</head>
<body class="container">
	<div class="singlePageContainer">
		<h1 class="header-font text-center mt-5">Demirdjian Family Archives</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">

		<div class="d-flex justify-content-center my-4">
			<?php if($_SESSION['userID'] > 0):?>
				<!-- Display logout button only if already logged in -->
				<a href="/logout" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1">Log Out</a>
				<a href="/search" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1 ml-4">Home</a>
			<?php else: ?>
				<a href="/login" class="btn btn-info col-3 col-sm-2 col-md-2 col-lg-1">Log In</a>
			<?php endif; ?>
				<a href="https://steeltotech.com:2096" class="btn btn-info col-3 col-sm-3 col-md-2 col-lg-1 ml-4">Webmail</a>
		</div>

		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light mt-4">
		<h4 class="text-center">Others Tolerated</h4>
	</div>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
	<!-- <script type="text/javascript" src="/js/index.js"></script> -->
</body>
</html>
