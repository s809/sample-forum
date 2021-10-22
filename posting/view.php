<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/posting.php");
	
	if (!isset($_GET["pid"])) {
		header("Location: /");
		exit();
	}
	
	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "create_post":
				$id = create_post($user["id"], $_GET["pid"]);
				header("Location: /posting/view.php?pid=" . $_GET["pid"] . "&page=" . (intdiv($id - 1, 10) + 1) . "#" . ($id - 1));
				exit();
			case "vote":
				vote($user["id"], $_GET["pid"]);
				header("Location: /posting/view.php?pid=" . $_GET["pid"]);
				exit();
			default:
				header("Location: /");
				exit();
		}
	}
	
	$head = thread_head($_GET["pid"]);
	$page = (isset($_GET["page"]) && $_GET["page"] != 0) ? $_GET["page"] - 1 : 0;
	$thread_len = thread_length($_GET["pid"]);
	
	$posts = open_thread($_GET["pid"], $page * 10, 10);
	$surveys = json_decode($head["surveys"], true);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />
		
		<title><?php echo $head["subject"]; ?></title>
		
		<script type="text/javascript" src="/container.js"></script>
		<script type="text/javascript" src="/theme.js"></script>
		
		<script>
			function leastOneVariant(cb) {
				selected = false;
				[...cb.parentNode.children].forEach(function(el) {
					selected |= el.checked;
				});
				
				[...cb.parentNode.children].forEach(function(el) {
					el.required = !selected;
				});
			}
		</script>
		
		<link rel="stylesheet" type="text/css" href="/style.css" />
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>

		<main>
			<template id="fileUploadTemplate">
				<div data-check="this.children[0]">
					<input class="checkable" type="file" name="files[]" data-event="change" data-check="this.files.length"/>
				</div>
			</template>
			
			<?php foreach ($posts as $key => $post) { ?>
				<div id="<?php echo $page * 10 + $key; ?>">
				
					<!-- Шапка поста. -->
					<div class="sided">
						<p style="width: 100%;"><?php echo find_user_login($post["uid"]); ?></p>
						<p style="white-space: nowrap;"><?php echo date("d.m.Y H:i:s", strtotime($post["date"])); ?></p>
					</div>
					
					<!-- Заголовок, если пост - первый в вопросе. -->
					<?php if ($key == 0 && $page == 0) { ?>
						<hr />
						<h1><?php echo $head["subject"]; ?></h1>
					<?php } ?>
					
					<!-- Содержимое. -->
					<?php if ($post["content"] != "") { ?>
						<hr />
						<p><?php echo nl2br($post["content"], true); ?></p>
					<?php } ?>
					
					<!-- Опросы. -->
					<?php if ($key == 0 && $page == 0 && count($surveys["list"])) { ?>
						<hr />
						<form action="/posting/view.php?action=vote&pid=<?php echo $_GET["pid"]; ?>" method="post">
						
							<?php foreach ($surveys["list"] as $sid => $survey) { ?>
								<div>
									<p class="card" style="border-color: slategrey;"><b><?php echo $survey["name"]; ?></b></p>
									
									<?php foreach ($survey["variants"] as $vid => $variant) { ?>
										<?php if ($user && !is_voted($user["id"], $_GET["pid"])) { ?>
											<input type="<?php echo ($survey["type"] == "single" ? "radio" : "checkbox"); ?>" name="<?php echo $sid; ?>[]" value="<?php echo $vid; ?>" onclick="leastOneVariant(this);" required="true"/>
										<?php } else { ?>
											<progress max="<?php echo $surveys["voted"]; ?>" value="<?php echo $variant["voted"]; ?>"></progress>
										<?php } ?>
										
										<?php echo $variant["name"]; ?><br />
									<?php } ?>
								</div>
							<?php } ?>
							
							<?php if ($user && !is_voted($user["id"], $_GET["pid"])) { ?>
								<br /><input type="submit" value="Проголосовать" /><br />
							<?php } ?>
							
							<p>Проголосовало пользователей: <?php echo $surveys["voted"]; ?>.</p>
						</form>
					<?php } ?>
					
					<!-- Список файлов. -->
					<?php foreach (json_decode($post["attachments"]) as $fkey => $file) { ?>
						<?php if ($fkey == 0) { ?>
							<hr />
							Прикрепленные файлы:
						<?php } ?>
						<a href="/files/<?php echo $file; ?>">Файл <?php echo $fkey + 1; ?></a>&nbsp;
					<?php } ?>
				</div>
			<?php } ?>
			
			<div>
				<?php if ($thread_len - 1 == 0) { ?>
					<p>Ответов нет.</p>
				<?php } else { ?>
					<p>Показаны ответы с <?php echo $page * 10 + ($page == 0); ?> по <?php echo $page * 10 + count($posts) - 1; ?>, всего <?php echo $thread_len - 1; ?>.</p>
				<?php } ?>
				
				<p>Текущая страница: <?php echo $page + 1; ?> из <?php echo intdiv($thread_len, 10) + 1; ?>.</p>
				
				<div>
					<?php if ($page >= 1) { ?>
						<a href="/posting/view.php?pid=<?php echo $_GET["pid"]; ?>&page=<?php echo $page; ?>">Назад</a>&nbsp;
					<?php } ?>
					<?php if ($page + 1 <= intdiv($thread_len - 1, 10)) { ?>
						<a href="/posting/view.php?pid=<?php echo $_GET["pid"]; ?>&page=<?php echo $page + 2; ?>">Вперед</a>&nbsp;
					<?php } ?>
					
					<a href="/posting/view.php?pid=<?php echo $_GET["pid"]; ?>">В начало</a>&nbsp;
					<a href="/posting/view.php?pid=<?php echo $_GET["pid"]; ?>&page=<?php echo intdiv($thread_len - 1, 10) + 1; ?>">В конец</a>
				</div>
				
				<br />
				
				<form action="/posting/view.php" method="get">
					<input type="hidden" name="pid" value="<?php echo $_GET["pid"]; ?>" />
					<input type="number" name="page" style="width: 120px;" placeholder="Номер страницы" min="1" max="<?php echo intdiv($thread_len, 10) + 1; ?>" required="true"/>
					<input type="submit" value="Перейти" />
				</form>
			</div>
			
			<hr />
			
			<?php if ($user) { ?>
				<form autocomplete="off" enctype="multipart/form-data" action="/posting/view.php?action=create_post&pid=<?php echo $_GET["pid"]; ?>" method="post">
					<!-- Максимальный размер для загрузки: 30 MiB -->
					<input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
					
					<h3>Ответить:</h3>
					
					Содержимое:<br />
					<textarea name="content" maxlength="1000" required="true"></textarea><br /><br />

					Файлы:<br />
					<div id="fileList" class="container" data-template="fileUploadTemplate" data-max="3"></div>
					
					<br /><input type="submit" value="Отправить" /><br />
				</form>
			<?php } else { ?>
				<div>
					<p>Войдите, чтобы ответить.</p>
				</div>
			<?php } ?>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
</html>