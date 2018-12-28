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
		$emailErr = "";
		$email = "";
		$success = true;
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

		if($success){
			// Form is valid
			// Find this user
			$res = $link->query("SELECT * FROM accounts WHERE email LIKE '{$email}'");
			$row = $res->fetch_assoc();
			if($res->num_rows > 0){
				// This user exists
				// generate resetLink
				$expire = date("\n Y-m-d-h-i", strtotime('1 hour'));
				$code = bin2hex(random_bytes(10));
				$resetLink = "https://steeltotech.com/reset-password/".$row["id"]."/".$code;
				
				// Store code and expire with user
				$codeRes = $link->query("UPDATE accounts SET passResetCode = '{$code}', passResetExpire = '{$expire}' WHERE email LIKE '{$email}' ");
				if($codeRes){
					// Send the email
					$to = $row["name"]." <".$email.">";
					$subject = "Password Recovery: Demirdjian Family Archives";
					$message = "<h4>Please click link below to reset your password. This link will expire in an hour.</h4>";
					$message .= "<br>";
					$message .= "<a href='".$resetLink."'>".$resetLink."</a>";
					$headers = [];
					mail($to, $subject, $message, $headers);

					$SucMessage = "If a user with that email exists, they have been sent an email with a link to reset their password.";
				}else{
					$emailErr = "Something went wrong. Contact Shawnt";
				}
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
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
		<link rel="stylesheet" href="./css/layout.css">
		<link rel="stylesheet" href="./css/index.css">
		<title>Recover Password</title>
		
	</head>
	<body class="container">
		<div class="singlePageContainer">
			<h1 class="header-font text-center mt-5">Demirdjian Family Archives</h1>
			<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
			<!-- Password Recover -->
			<form id="logIn" class="mt-5 col-8 col-sm-5 col-md-4 col-lg-3 mx-auto my-4" action="" method="post">
				<h2 class="text-center mb-2">Password Recovery</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<h3 class="valid-feedback d-block"><?php echo $SucMessage;?></h3>
				<div class="form-group">
					<label for="email">Email</label>
					<h4 class="invalid-feedback d-block"><?php echo $emailErr;?></h4>
					<input class="form-control" type="email" name="email" value="" required>
				</div>
				<button type="submit" value="logIn" class="btn btn-info">Recover</button>
			</form>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
	</body>
</html>
