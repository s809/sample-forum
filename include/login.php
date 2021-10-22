<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	// Поиск пользователя по логину.
	function user_exists(string $login) {
		global $db;
		$res = $db->query('SELECT * FROM users WHERE login = "' . $login . '";');
		$res->data_seek(0);
		$user = $res->fetch_array();
		return $user[0];
	}
	
	// Поиск пользователя по его id.
	function find_user_login(int $id) {
		global $db;
		$res = $db->query('SELECT login FROM users WHERE id = ' . $id . ';');
		$res->data_seek(0);
		$user = $res->fetch_array();
		return $user[0];
	}
	
	// Проверка логина и пароля для входа.
	function login(string $login, string $pass) {
		global $db;
		$res = $db->query('SELECT * FROM users WHERE login = "' . $login . '" AND pass = "' . $pass . '";');
		$res->data_seek(0);
		$user = $res->fetch_assoc();
		
		if (!$user) {
			logout();
			return false;
		}
		
		unset($user["pass"]);
		return $user;
	}
	
	// Смена пароля.
	function change_pass(string $uid, string $pass_new) {
		global $db;
		$db->query('UPDATE users SET pass = "' . $pass_new . '" WHERE id = "' . $uid . '";');
	}
	
	// Выход.
	function logout() {
		setcookie("login", "", 1);
		setcookie("pass", "", 1);
	}
?>