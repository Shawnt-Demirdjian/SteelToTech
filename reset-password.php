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

	$valid = false;
	// Set URL Parameters
	if(isset($_GET["code"]) && isset($_GET["id"])){
		$code = $_GET["code"];
		$userID = $_GET["id"];
		
		// check the code and expiration date
		$codeRes = $link->query("SELECT passResetCode, passResetExpire FROM accounts WHERE id = {$userID}");
		$codeRow = $codeRes->fetch_assoc();

		if(($codeRes->num_rows > 0) && ($code == $codeRow["passResetCode"]) && (strtotime("now") < strtotime($codeRow["passResetExpire"]) )){
			// user exists, code is valid, and has not expired
			$valid = true;
		}
	}

	// Form validation
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Change Password
		$password = $passwordConfirm = "";
		$passwordErr = $passwordConfirmErr = "";
		$success = true;

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
			// Update Password
			$res = $link->query("UPDATE accounts SET password = '{$password}', passResetCode = NULL, passResetExpire = NULL WHERE id = {$userID} ");
			if($res){
				// Successfully updated password
				header('Location: /login');
				$link->close();
				die();
			}else{
				// Failed to insert new user
				$failMessage = "Password update failed. Please tell Shawnt.";
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
		<title>Reset Password</title>
		
	</head>
	<body class="container">
		<div class="singlePageContainer">
			<h1 class="header-font text-center mt-5">Demirdjian Family Archives</h1>
			<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
			<!-- Password Reset -->
			<?php if($valid):?>
				<form class="mt-5 col-8 col-sm-5 col-md-4 col-lg-3 mx-auto my-4" action="" method="post">
					<h2 class="text-center mb-2">Password Reset</h2>
					<h4 class="invalid-feedback d-block text-center"><?php echo $failMessage;?></h4>
					<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					<div class="form-group">
						<label for="password">New Password</label>
						<h4 class="invalid-feedback d-block"><?php echo $passwordErr;?></h4>
						<input class="form-control" type="password" name="password" value="" required>
					</div>
					<div class="form-group">
						<label for="passwordConfirm">New Password Confirm</label>
						<h4 class="invalid-feedback d-block"><?php echo $passwordConfirmErr;?></h4>
						<input class="form-control" type="password" name="passwordConfirm" value="" required>
					</div>
					<button type="reset" class="btn btn-danger">Reset</button>
					<button type="submit" value="change" name="submit" class="btn btn-info float-right">Change</button>
				</form>
			<?php else:?>
				<div class="mt-5 col-8 col-sm-5 col-md-4 col-lg-3 mx-auto my-4">
					<h2 class="text-center mb-2">Password Reset</h2>
					<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
					<h4 class="invalid-feedback d-block text-center"> This link is either invalid or has expired.</h4>
				</div>
			<?php endif;?>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js" integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
