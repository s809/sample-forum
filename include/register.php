<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	// Регистрация нового пользователя.
	function register(string $login, string $pass) {
		global $db;
		$query = $db->prepare('INSERT INTO users(login, pass) VALUES(?, ?);');
		$query->bind_param("ss", $login, $pass);
		$query->execute();
		if ($query->errno) {
			die($query->error);
		}
	}
?>