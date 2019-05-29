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

	// Form validation
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$success = true;
		$failMessage = "";
		$successMessage = "";
		if($_POST['submit'] == 'update'){
			// Update Account Info
			$firstName = $lastName = $email = "";
			$firstNameErr = $lastNameErr = $emailErr = "";

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
				// Update user info
				$res = $link->query("UPDATE accounts SET first = '{$firstName}', last = '{$lastName}', email = '{$email}' WHERE id = {$_SESSION['userID']} ");
				if($res){
					// Successfully added new user
					$successMessage = "User successfully updated!";
					$_SESSION['email'] = $email;
					$_SESSION['first'] = $firstName;
					$_SESSION['last'] = $lastName;
				}else{
					// Failed to insert new user
					$failMessage = "User update failed. Please tell Shawnt.";
				}
			}

		}else{
			// Change Password
			$oldPassword = $password = $passwordConfirm = "";
			$oldPasswordErr = $passwordErr = $passwordConfirmErr = "";

			// Old Password Validation
			if (empty($_POST["oldPassword"])) {
				$OldPasswordErr = "Old Password is required";
				$success = false;
			}else {
				$res = $link->query("SELECT password FROM accounts WHERE id = {$_SESSION['userID']}");
				$row = $res->fetch_assoc();
				if(password_verify($_POST["oldPassword"], $row['password'])){
					// correct password
					$oldPassword = $_POST["oldPassword"];
				}else{
					// wrong password
					$oldPasswordErr = "Old Password is incorrect";
					$success = false;
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
				// Update Password
				$res = $link->query("UPDATE accounts SET password = '{$password}' WHERE id = {$_SESSION['userID']} ");
				if($res){
					// Successfully added new user
					$successMessage = "Password successfully updated!";
				}else{
					// Failed to insert new user
					$failMessage = "Password update failed. Please tell Shawnt.";
				}
			}
		}
	}
	
	$link->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Forum" rel="stylesheet">
	<link rel="stylesheet" href="./css/layout.css">
	<style>#account{color:white !important; text-decoration: underline;}</style>
	<title>Account</title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<h1 class="text-center mt-4"><?php echo $_SESSION['first'] ?>'s Account Settings</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
		<?php if($_SERVER["REQUEST_METHOD"] == "POST" && $success):?>
			<h4 class="text-center valid-feedback d-block "><?php echo $successMessage;?></h4>
		<?php elseif($_SERVER["REQUEST_METHOD"] == "POST"): ?>
			<h4 class="text-center invalid-feedback d-block "><?php echo $failMessage;?></h4>
		<?php endif; ?>
		<div class="row no-gutters">
			<div class="col-12 col-md-6 mt-4 mb-5">
				<h2 class="text-center">Account Information</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<form class="col-10 mx-auto" action="/account" method="post">
					<div class="form-group">
						<label for="firstName">First Name</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($firstNameErr)) echo $firstNameErr;?></h4>
						<input class="form-control" type="text" name="firstName" value="<?php echo $_SESSION['first'];?>" required>
					</div>
					<div class="form-group">
						<label for="lastName">Last Name</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($lastNameErr)) echo $lastNameErr;?></h4>
						<input class="form-control" type="text" name="lastName" value="<?php echo $_SESSION['last'];?>" required>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($emailErr)) echo $emailErr;?></h4>
						<input class="form-control" type="email" name="email" value="<?php echo $_SESSION['email'];?>" required>
					</div>
					<button type="reset" class="btn btn-danger">Reset</button>
					<button type="submit" value="update" name="submit" class="btn btn-info float-right">Update</button>
				</form>
			</div>
			<div class="col-12 col-md-6 mt-4 mb-5">
			<h2 class="text-center">Change Password</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<form class="col-10 mx-auto" action="/account" method="post">
					<div class="form-group">
						<label for="oldPassword">Old Password</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($oldPasswordErr)) echo $oldPasswordErr;?></h4>
						<input class="form-control" type="password" name="oldPassword" value="" required>
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($passwordErr)) echo $passwordErr;?></h4>
						<input class="form-control" type="password" name="password" value="" required>
					</div>
					<div class="form-group">
						<label for="passwordConfirm">Password Confirm</label>
						<h4 class="invalid-feedback d-block"><?php if(isset($passwordConfirmErr)) echo $passwordConfirmErr;?></h4>
						<input class="form-control" type="password" name="passwordConfirm" value="" required>
					</div>
					<button type="reset" class="btn btn-danger">Reset</button>
					<button type="submit" value="change" name="submit" class="btn btn-info float-right">Change</button>
				</form>
			</div>
		</div>
	</div>
	<?php require 'includes/footer.php';?>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.js" integrity="sha256-awnyktR66d3+Hym/H0vYBQ1GkO06rFGkzKQcBj7npVE=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>