<?php
	
	// Сохраняет загруженные файлы и возвращает JSON-строку со списком.
	function save_files() {
		$files = [];
		foreach ($_FILES["files"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$name = bin2hex(microtime(true)) . "." . pathinfo($_FILES["files"]["name"][$key], PATHINFO_EXTENSION);
				$files[] = $name;
				move_uploaded_file($_FILES["files"]["tmp_name"][$key], $_SERVER['DOCUMENT_ROOT'] . "/files/" . $name);
			}
		}
		return json_encode($files);
	}
?>