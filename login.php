<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
		<link rel="stylesheet" href="./css/index.css">
		<title>Log In</title>
		<?php
			$link = mysqli_connect('localhost', 'root', 'xliv11', 'demi');
			if($link === false){
				die("ERROR: Could not connect. " . mysqli_connect_error());
			}
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				echo "<h1>SHIT</h1>";
				echo $_POST['submit'];
				switch($_POST['submit']) {
					case 'logIn':
						echo "<h1>logIn</h1>";
					break;
					case 'signUp':
						echo "<h1>signUp</h1>";
					break;
					default:
						echo $_POST['submit'];
				}
			}
		?>
	</head>
	<body>
		<h1 class="text-center mt-5">Steel to Tech</h1>
		<hr class="col-1 mx-auto bg-light">
		<br>
		<!-- Log In -->
		<form id="logIn" class="mt-5 col-3 mx-auto" action="login.php" method="post">
			<h2 class="text-center mb-2">Log In</h2>
			<div class="form-group">
				<label for="firstName">First Name</label>
				<input class="form-control" type="text" name="firsName" value="" required>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input class="form-control" type="password" name="password" value="" required>
			</div>
			<button type="submit" value="logIn" class="btn btn-primary">Submit</button>
		</form>
		<h6 class="d-flex justify-content-center">or<a href="/signup.php" class="ml-1">Sign Up</a></h6>

		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
		<!-- <script type="text/javascript" src="/js/index.js"></script> -->
	</body>
</html>