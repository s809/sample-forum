<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/posting.php");
	
	if (!$user) {
		header("Location: /");
		exit();
	}

	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "create_thread":
				$pid = create_thread($user["id"]);
				header("Location: /posting/view.php?pid=" . $pid);
				exit();
			default:
				header("Location: /");
				exit();
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />
		
		<title>Создание темы</title>
		
		<script type="text/javascript" src="/container.js"></script>
		<script type="text/javascript" src="/theme.js"></script>
		
		<script type="text/javascript">
			function addSurvey() {
				let added = checkAndInsertTemplate(document.getElementById("surveyList"));
				if (added) {
					listenCheckables(added);
				}
			}
			
			function deleteSurvey(el) {
				containerEntity(el).remove();
			}
			
			function countVariants(el, deleted) {
				let innerContainer = containerEntity(el).parentNode;
				let count = containerEntity(innerContainer).children[0];
				count.value = innerContainer.childElementCount - (deleted == true);
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
			
			<template id="surveyTemplate">
				<li>
					<input type="hidden" name="variant_count[]" value="1" />
					
					Тема:
					<input type="text" name="survey_names[]" maxlength="40" required="true">&nbsp;
					
					Тип:
					<select name="type[]">
						<option value="single" selected="true">Один вариант</option>
						<option value="multiple">Несколько вариантов</option>
					</select>
					<br />
					
					Варианты:
					<ul class="container" data-template="variantTemplate" data-max="7"></ul>
					<button type="button" onclick="deleteSurvey(this);">Удалить</button><br />
					<br />
				</li>
			</template>
			
			<template id="variantTemplate">
				<li data-check="this.children[0]" data-add="countVariants(this);" data-remove="countVariants(this, true);">
					<input name="variants[]" class="checkable" type="text" data-event="input" data-check="this.value.length"/>
				</li>
			</template>
			
			<form autocomplete="off" enctype="multipart/form-data" action="/posting/create.php?action=create_thread" method="post">
				<!-- Максимальный размер для загрузки: 30 MiB -->
				<input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
				
				<h3>Создание темы:</h3>
				
				Тема:<br />
				<input name="subject" type="text" id="subject" minlength="1" maxlength="200" required="true" /><br /><br />
				
				Содержимое:<br />
				<textarea name="content" maxlength="1000"></textarea><br /><br />
				
				Опросы:
				<ol id="surveyList" class="container" data-template="surveyTemplate" data-max="3"></ol>
				<button type="button" onclick="addSurvey();">Добавить опрос</button><br /><br />
				
				Файлы:<br />
				<div id="fileList" class="container" data-template="fileUploadTemplate" data-max="3"></div>
				
				<br /><input type="submit" value="Отправить" /><br />
			</form>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
</html>