<?php
session_start();
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
	// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	// $fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	// if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		
		move_uploaded_file($tempFile,$targetFile);
		echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		include("../Database.class.php");
		include_once("../images.class.php");
		$images = new images();
	    $myFile = "../../testFile.txt";
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, 1000);
		fclose($fh);
		$images->setIDOPTION($theData);
		$images->setIMAGEURL($_FILES['Filedata']['name']);
		$images->insert();
		
		
	// } else {
	// 	echo 'Invalid file type.';
	// }
}
?>