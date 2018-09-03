<?php
	// start or resume session
	session_start();

	// redirect away if not logged in
	if($_SESSION['userID'] <= 0){
		header('Location: index.php');
		die();
	}

	// connect to database
	$link = new mysqli("localhost", "root", "xliv11", "steelt10_demi");
	if($link->connect_errno){
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	$link->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Forum" rel="stylesheet">
	<link rel="stylesheet" href="./css/layout.css">
	<title>Account</title>
</head>
<body>
	<?php require 'includes/header.php';?>
	<div class="singlePageContainer">
		<h1 class="text-center mt-4"><?php echo $_SESSION['first'] ?>'s Account Settings</h1>
		<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
		<div class="row no-gutters">
			<div class="col-12 col-md-6 mt-5">
				<h2 class="text-center">Account Information</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<form class="col-10 mx-auto" action="account.php" method="post">
					<div class="form-group">
						<label for="firstName">First Name</label>
						<h4 class="invalid-feedback d-block"><?php echo $firstNameErr;?></h4>
						<input class="form-control" type="text" name="firstName" value="<?php echo $_SESSION['first'];?>" required>
					</div>
					<div class="form-group">
						<label for="lastName">Last Name</label>
						<h4 class="invalid-feedback d-block"><?php echo $lastNameErr;?></h4>
						<input class="form-control" type="text" name="lastName" value="<?php echo $_SESSION['last'];?>" required>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<h4 class="invalid-feedback d-block"><?php echo $emailErr;?></h4>
						<input class="form-control" type="email" name="email" value="<?php echo $_SESSION['email'];?>" required>
					</div>
					<button type="submit" value="update" class="btn btn-info">Update</button>
				</form>
			</div>
			<div class="col-12 col-md-6 my-5">
			<h2 class="text-center">Change Password</h2>
				<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
				<form class="col-10 mx-auto" action="account.php" method="post">
					<div class="form-group">
						<label for="oldPassword">Old Password</label>
						<h4 class="invalid-feedback d-block"><?php echo $oldPasswordErr;?></h4>
						<input class="form-control" type="password" name="oldPassword" value="" required>
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
					<button type="submit" value="change" class="btn btn-info">Change</button>
				</form>
			</div>
		</div>
	</div>
	<?php require 'includes/footer.php';?>
</body>
</html>