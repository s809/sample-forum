<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	require($_SERVER['DOCUMENT_ROOT'] . "/include/login.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/register.php");
	
	// Подключение к базе данных.
	global $db;
	$db = new mysqli("127.0.0.1", "user", "", "db");
	$db->set_charset('utf8');
	if ($db->connect_errno) {
		die("Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . "): " . $mysqli->connect_error);
	}
	
	// Проверка существующего входа.
	if (isset($_COOKIE["login"]) && isset($_COOKIE["pass"])) {
		$user = login($_COOKIE["login"], $_COOKIE["pass"]);
	} else {
		$user = false;
	}
?>