<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	
	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "register":
				if (!$user) register($_POST["login"], $_POST["pass"]);
				setcookie("login", $_POST["login"], time()+3600*26*30);
				setcookie("pass", $_POST["pass"], time()+3600*26*30);
				header("Location: /");
				exit();
				break;
			case "login_uniq":
				header("Content-Type: text/plain");
				echo intval(boolval(user_exists($_GET["login"])));
				exit();
				break;
		}
	}
	
	if ($user) {
		header("Location: /login.php");
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />
		
		<title>Регистрация</title>
		
		<link rel="stylesheet" type="text/css" href="style.css" />
		
		<script type="text/javascript" src="/theme.js"></script>
		
		<script type="text/javascript">
			// Проверка совпадения паролей.
			function check() {
				document.getElementsByClassName("input-error")[1].classList.toggle("hidden", true);
				
				let pass = document.getElementsByName("pass");
				if (pass[0].value == pass[1].value) {
					let form = document.getElementById("register");
					if (form.reportValidity()) {
						form.submit();
					}
				} else {
					document.getElementsByClassName("input-error")[1].classList.toggle("hidden", false);
				}
			}
			
			// Проверка логина на уникальность.
			function login_uniq(login) {
				let req = new XMLHttpRequest();
				req.open("GET", "/register.php?action=login_uniq&login=" + login.value, true);
				
				req.onload = function() {
					let res = parseInt(req.response);
					document.getElementsByClassName("input-error")[0].classList.toggle("hidden", !res);
					document.getElementById("reg-btn").disabled = res;
				}
				
				req.send();
			}
		</script>
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
		
		<main>
			<form id="register" action="register.php?action=register" method="post">
				<h3>Регистрация</h3>
				
				Логин:<br />
				<input type="text" name="login" minlength="5" maxlength="20" autocomplete="username" onfocusout="login_uniq(this);" required="required" /><br />
				
				<span class="input-error hidden">Текущий логин занят.<br /></span>
				
				Пароль:<br />
				<input type="password" name="pass" minlength="6" maxlength="20" autocomplete="new-password" required="required" /><br />
				
				Повторите пароль:<br />
				<input type="password" name="pass" minlength="6" maxlength="20" autocomplete="new-password" required="required" /><br />
				
				<span class="input-error hidden">Пароли не совпадают!<br /></span>
				
				<br /><button type="button" id="reg-btn" onclick="check();">Регистрация</button><br />
			</form>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
</html>