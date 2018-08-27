<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
		<link rel="stylesheet" href="./css/index.css">
		<title>Log In</title>
		<?php
			// start or resume session
			session_start();

			// redirect away if already logged in
			if($_SESSION['userID'] > 0){
				header('Location: index.php');
				die();
			}

			// connect to database
			$link = new mysqli("localhost", "root", "xliv11", "demi");
			if($link->connect_errno){
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}

			$emailErr = $passwordErr = "";
            $email = $password = $failure = "";
			$success = true;
			// Form validation
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				// Email Validation
				if (empty($_POST["email"])) {
	            	$emailErr = "Email is required";
					$success = false;
	            }else {
					$email = $_POST["email"];
					// Sanitize Email
					$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					$email = filter_var($email, FILTER_SANITIZE_EMAIL);
					if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
						// Invalid Email
						$emailErr = "Email is invalid";
						$success = false;
					}
				}
				
				// Password Name Validation
				if (empty($_POST["password"])) {
	            	$passwordErr = "Password is required";
					$success = false;
	            }else {
	            	$password = $_POST["password"];
	            }

				if($success){
					// Form is valid
					// Find this user
					$res = $link->query("SELECT PASSWORD , ID FROM accounts WHERE email LIKE '{$email}'");
					$row = $res->fetch_assoc();
					if($res->num_rows > 0){
						// This user exists
						if(password_verify($password, $row['PASSWORD'])){
							// correct password
							$_SESSION['userID'] = $row['ID'];
							header('Location: index.php');
							$link->close();
							die();
						}else{
							// wrong password
							$passwordErr = "Password is incorrect";
						}
					}else{
						$emailErr = "This user doesn't exist";
					}
				}
			}
			$link->close();
		?>
	</head>
	<body class="container">
		<h1 class="text-center mt-5">Demirdjian Family Archives</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
		<!-- Log In -->
		<form id="logIn" class="mt-5 col-8 col-sm-5 col-md-4 col-lg-3 mx-auto my-4" action="login.php" method="post">
			<h2 class="text-center mb-2">Log In</h2>
			<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
			<h3 class="invalid-feedback d-block"><?php echo $failure;?></h3>
			<div class="form-group">
				<label for="email">Email</label>
				<h4 class="invalid-feedback d-block"><?php echo $emailErr;?></h4>
				<input class="form-control" type="email" name="email" value="" required>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<h4 class="invalid-feedback d-block"><?php echo $passwordErr;?></h4>
				<input class="form-control" type="password" name="password" value="" required>
			</div>
			<button type="submit" value="logIn" class="btn btn-info">Submit</button>
		</form>

		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
		<!-- <script type="text/javascript" src="/js/index.js"></script> -->
	</body>
</html>
