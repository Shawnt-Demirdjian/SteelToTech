<?php
// Receive POST request
// Rotate give file by given angle in degrees
// Return JSON with message property
header('Content-Type: application/json');

// Return 405 if not POST
if($_SERVER["REQUEST_METHOD"] != "POST"){
	http_response_code(405);
	echo json_encode(["message" => "Post method only."]);
	die();
}

// Return 400 if missing parameters
if(!isset($_POST["angle"]) || !isset($_POST["filename"])){
	http_response_code(400);
	echo json_encode(["message" => "Please include an Integer 'angle' and a String 'filename'."]);
	die();
}

// Set variables
$angle = filter_var($_POST["angle"], FILTER_VALIDATE_INT);
$filename = filter_var( $_POST["filename"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Return 400 if invalid parameters
if((!$angle and gettype($angle) == "boolean") || !$filename){
	// http_response_code(400);
	echo json_encode(["message" => "Parameter 'angle' must be an Integer and 'filename' must be a String."]);
	die();
}

// Remove front slash on file name
$filename = ltrim($filename);

// Flip angle to counterclockwise
$angle *= -1;

// All is well, parameters are valid

// Load the image
$OriginalSource = imagecreatefromjpeg("./media/source/".$filename);
$smallSource = imagecreatefromjpeg("./media/small/".$filename);
$mediumSource = imagecreatefromjpeg("./media/medium/".$filename);
$largeSource = imagecreatefromjpeg("./media/large/".$filename);

// Rotate the image
$OriginalRotate = imagerotate($OriginalSource, $angle, 0);
$smallRotate = imagerotate($smallSource, $angle, 0);
$mediumRotate = imagerotate($mediumSource, $angle, 0);
$largeRotate = imagerotate($largeSource, $angle, 0);

// Overwrite old image
$result = imagejpeg($OriginalRotate, "media/source/".$filename , 100);
$smallResult = imagejpeg($smallRotate, "media/small/".$filename , 100);
$mediumResult = imagejpeg($mediumRotate, "media/medium/".$filename , 100);
$largeResult = imagejpeg($largeRotate, "media/large/".$filename , 100);

if($result && $smallResult && $mediumResult && $largeResult){
	http_response_code(200);
	echo json_encode(["message" => "Image successfully rotated."]);
}else{
	http_response_code(503);
	echo json_encode(["message" => "Image failed to be rotated."]);
}

?>