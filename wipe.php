<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/posting.php");
	
	mysqli_report(MYSQLI_REPORT_OFF);
	
	$db->query("delete from users;");
	$db->query("alter table users auto_increment = 0;");
	
	$db->query("delete from posts;");
	$db->query("alter table posts auto_increment = 0;");
	
	$mysqli = new mysqli("127.0.0.1", "user", "", "");
	$mysqli->query('drop database posts;');
	$mysqli->query('create database posts;');
	$mysqli->close();
	
	$files = glob($_SERVER['DOCUMENT_ROOT'] . "/files/*");
	foreach ($files as $file) {
		if(is_file($file))
			unlink($file);
	}
	
	foreach ($_COOKIE as $key => $value) {
		setcookie($key, "", 0);
	}
	
	header("Location: /");
?>