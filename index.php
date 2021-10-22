<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/include/users.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/posting.php");
	
	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "last_posts":
				echo json_encode(last_posts($_GET{"start"}));
				exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=1.0, user-scalable=no" />
		
		<title>F</title>
		
		<script type="text/javascript" src="/theme.js"></script>
		
		<script type="text/javascript">
			var interval;
			var req;
			var last_id = -1;
			
			function lastPosts() {
				let el = document.getElementById("last_posts").lastElementChild;
				if (el) {
					if (!isScrolledIntoView(el)) return false;
				}
				
				if (req) return false;
				
				req = new XMLHttpRequest();
				req.open("GET", "/index.php?action=last_posts&start=" + encodeURIComponent(last_id), true);
				
				req.onload = function() {
					let arr = JSON.parse(this.response);
					
					if (arr.length < 5) {
						clearInterval(interval);
						if (document.getElementById("loading")) {
							document.getElementById("loading").remove();
						}
					}
					
					arr.map(function(e) {
						if (last_id == -1 || parseInt(e.id) < last_id) {
							last_id = parseInt(e.id);
						}
						let q = document.getElementById("postTemplate").content.firstElementChild.cloneNode(true);
						q.firstElementChild.textContent = e.subject;
						q.lastElementChild.textContent = e.date;
						q.href = "/posting/view.php?pid=" + e.id;
						document.getElementById("last_posts").appendChild(q);
						return e;
					});
					
					req = null;
				}
				
				req.send();
				return true;
			}
			
			// https://stackoverflow.com/a/22480938/10804804
			function isScrolledIntoView(el) {
				var rect = el.getBoundingClientRect();
				var elemTop = rect.top;
				var elemBottom = rect.bottom;
				
				var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);

				return isVisible;
			}
		</script>
		
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body onload="interval = setInterval(lastPosts, 100);">
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
		
		<main>
			<template id="postTemplate">
				<a class="sided">
					<span style="width: 100%;"></span>
					<span style="white-space: nowrap;"></span>
				</a>
			</template>
			
			<div>
				<?php if ($user) { ?>
					<br />
					<a href="/posting/create.php">Создать тему</a>
				<?php } ?>
				
				<h2>Последние:</h2>
			</div>
			
			<div id="last_posts">
			</div>
			<p id="loading">Загрузка...</p>
		</main>
		
		<?php require($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
	</body>
</html>