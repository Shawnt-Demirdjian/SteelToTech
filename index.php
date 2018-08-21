
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<link rel="stylesheet" href="./css/index.css">
	<title>Steel to Tech</title>
	<?php
		session_start();
	?>
</head>
<body>
	<h1 class="text-center mt-5">Steel to Tech</h1>
	<hr class="col-1 mx-auto bg-light">
	<br>

	<div class="d-flex justify-content-center">
		<?php if($_SESSION['userID'] > 0):?>
			<!-- Display logout button only if already logged in -->
			<a href="/logout.php" class="btn btn-primary col-1">Log Out</a>
		<?php else: ?>
			<a href="/login.php" class="btn btn-primary col-1">Log In</a>
		<?php endif; ?>
		<a href="/signup.php" class="btn btn-primary ml-4 col-1">Sign Up</a>
	</div>

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
	<!-- <script type="text/javascript" src="/js/index.js"></script> -->
</body>
</html>
