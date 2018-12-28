<?php
	// start or resume session
	session_start();

	// redirect away if already logged in
	if(isset($_SESSION['userID'])){
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

	// Form validation
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$firstNameErr = $lastNameErr = $passwordErr = $emailErr = $passwordConfirmErr = "";
		$firstName = $lastName = $password = $email = $passwordConfirm = $failure = "";
		$success = true;
		// First Name Validation
		if (empty($_POST["firstName"])) {
			$firstNameErr = "First Name is required";
			$success = false;
		}else {
			$firstName = $_POST["firstName"];
			$firstName = filter_var($firstName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		// Last Name Validation
		if (empty($_POST["lastName"])) {
			$lastNameErr = "Last Name is required";
			$success = false;
		}else {
			$lastName = $_POST["lastName"];
			$lastName = filter_var($lastName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		// Email Validation
		if (empty($_POST["email"])) {
			$emailErr = "Email is required";
			$success = false;
		}else {
			$res = $link->query("SELECT * FROM accounts WHERE email LIKE '{$_POST["email"]}'");
			if($res->num_rows > 0 ){
				// Email Taken
				$success = false;
				$emailErr = "Email has been taken";
			} else{
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
		}

		// Password Validation
		if (empty($_POST["password"])) {
			$passwordErr = "Password is required";
			$success = false;
		}else {
			$password = $_POST["password"];
		}

		// Password Confirm Validation
		if (empty($_POST["passwordConfirm"])) {
			$passwordConfirmErr = "Password Confirm name is required";
			$success = false;
		}elseif(($_POST["passwordConfirm"]) != $password){
			$passwordConfirmErr = "Password Confirm must match Password";
			$success = false;
		}

		if($success){
			// Form is valid
			// Hash Password
			$password = password_hash($password, PASSWORD_DEFAULT);
			// Insert the new user
			$res = $link->query("INSERT INTO accounts (first, last, email, password) VALUES ('{$firstName}','{$lastName}','{$email}','{$password}')");
			if($res){
				// Successfully added new user
				header('Location: /login');
				$link->close();
				die();
			}else{
				// Failed to insert new user
				$failure = "User registration failed. Please tell Shawnt.";
			}
		}
	}
	$link->close();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
		<link rel="stylesheet" href="./css/layout.css">
		<link rel="stylesheet" href="./css/index.css">
		<title>Sign Up</title>
	</head>
	<body class="container">
		<div class="singlePageContainer">
			<h1 class="header-font text-center mt-5">Demirdjian Family Archives</h1>
			<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
			<!-- Sign Up -->
			<form id="signUp" class="mt-5 col-8 col-sm-5 col-md-4 col-lg-3 mx-auto my-4" action="/signup" method="post">
				<h2 class="text-center mb-2">Sign Up</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<h3 class="invalid-feedback d-block"><?php echo $failure;?></h3>
				<div class="form-group">
					<label for="firstName">First Name</label>
					<h4 class="invalid-feedback d-block"><?php echo $firstNameErr;?></h4>
					<input class="form-control" type="text" name="firstName" value="" required>
				</div>
				<div class="form-group">
					<label for="lastName">Last Name</label>
					<h4 class="invalid-feedback d-block"><?php echo $lastNameErr;?></h4>
					<input class="form-control" type="text" name="lastName" value="" required>
				</div>
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
				<div class="form-group">
					<label for="passwordConfirm">Password Confirm</label>
					<h4 class="invalid-feedback d-block"><?php echo $passwordConfirmErr;?></h4>
					<input class="form-control" type="password" name="passwordConfirm" value="" required>
				</div>
				<button type="submit" value="signUp" class="btn btn-info">Sign Up</button>
			</form>
		</div>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js" integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
