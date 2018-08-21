<?php
	// Resume Session
	session_start();

	// Log Out by Destroy Session
	session_destroy();

	// Redirect to home
	header('Location: index.php');
	die();
?>
