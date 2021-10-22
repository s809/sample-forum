<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	
	if ($user && (!isset($_GET["action"]) || $_GET["action"] != "logout")) {
		header("Location: /");
		exit();
	}

	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "login":
				if (login($_POST["login"], $_POST["pass"])) {
					setcookie("login", $_POST["login"], time()+3600*26*30);
					setcookie("pass", $_POST["pass"], time()+3600*26*30);
					header("Location: /");
					exit();
				} else {
					$failed = true;
				}
				break;
			case "logout":
				logout();
				header("Location: /");
				exit();
				break;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />
		
		<title>Вход</title>
		
		<link rel="stylesheet" type="text/css" href="style.css" />
		
		<script type="text/javascript" src="/theme.js"></script>
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
		
		<main>
			<form action="login.php?action=login" method="post">
				<h3>Вход</h3>
				
				Логин:<br />
				<input type="text" name="login" maxlength="40" autocomplete="username" required="required" /><br />
				
				Пароль:<br />
				<input type="password" name="pass" maxlength="40" autocomplete="current-password" required="required" /><br />
				<?php if (isset($failed)) { ?>
					<span class="input-error">Неверный логин или пароль.</span><br />
				<?php } ?>
				
				<br /><input type="submit" value="Войти" /><br />
			</form>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
</html>