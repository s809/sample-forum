<header>
	<a href="/" id="home" title="На главную">F</a>
	
	<div id="links">
	</div>
	
	<?php if ($user) { ?>
		<div class="user">
			<p><?php echo $user["login"]; ?></p>
		</div>
	<?php } ?>
	
	<div class="buttons">
		<?php if ($user) { ?>
			<a href="/settings.php">Настройки</a>
			<a href="/login.php?action=logout">Выйти</a>
		<?php } else { ?>
			<a href="/login.php">Войти</a>
			<a href="/register.php">Регистрация</a>
		<?php } ?>
	</div>
</header>