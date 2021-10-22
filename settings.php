<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	
	if (!$user) {
		header("Location: /login.php");
		exit();
	}
	
	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "change_pass":
				if (login($user["login"], $_POST["pass"])) {
					change_pass($user["id"], $_POST["pass-new"]);
					setcookie("login", $user["login"], time()+3600*26*30);
					setcookie("pass", $_POST["pass-new"], time()+3600*26*30);
					header("Location: /");
					exit();
				} else {
					$failed = true;
				}
				break;
			default:
				header("Location: /settings.php");
				exit();
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />		
		
		<title>Настройки</title>
		
		<link rel="stylesheet" type="text/css" href="style.css" />
		
		<script type="text/javascript" src="/theme.js"></script>
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
		
		<main>
			<div>
				<h2>Настройки:</h2>
			</div>
			
			<div>
				<h3>Изменить фон:</h3>
				
				<table>
					<tr>
						<td>
							Верхний цвет:
						</td>
						<td>
							<input id="bg_top" type="color" value="#ffffff" onchange="updateTheme('bg_top', this.value)" />
						</td>
					</tr>
					<tr>
						<td>
							Нижний цвет:
						</td>
						<td>
							<input id="bg_bottom" type="color" value="#ffffff" onchange="updateTheme('bg_bottom', this.value)" />
						</td>
					</tr>
				</table>
			</div>

			<form action="settings.php?action=change_pass" method="post">
				<input style="display: none;" type="text" name="login" maxlength="40" autocomplete="username" />
				
				<h3>Изменить пароль:</h3>
				
				Старый пароль:<br />
				<input type="password" name="pass" maxlength="40" autocomplete="current-password" required="required" /><br />
				<?php if (isset($failed)) { ?>
					<span class="input-error">Неверный пароль.</span><br />
				<?php } ?>
				
				Пароль:<br />
				<input type="password" name="pass-new" minlength="6" maxlength="20" autocomplete="new-password" required="required" /><br />
				
				Повторите пароль:<br />
				<input type="password" name="pass-new" minlength="6" maxlength="20" autocomplete="new-password" required="required" /><br />
				
				<br /><input type="submit" value="Изменить" /><br />
			</form>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
<html>