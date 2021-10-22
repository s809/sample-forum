<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	require($_SERVER['DOCUMENT_ROOT'] . "/include/files.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/surveys.php");
	
	// Подключение к базе данных.
	global $pdb;
	$pdb = new mysqli("127.0.0.1", "user", "", "posts");
	$pdb->set_charset('utf8');
	if ($pdb->connect_errno) {
		die("Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . "): " . $mysqli->connect_error);
	}
	
	// Получить 5 последних вопросов.
	function last_posts(int $id) {
		global $db;
		$res = $db->query('SELECT * FROM posts ' . ($id != -1 ? 'WHERE id < ' . $id : "") . ' ORDER BY id DESC LIMIT 5;');
		
		$arr = [];
		while ($row = $res->fetch_assoc()) {
			$arr[] = ["subject" => $row["subject"], "date" => date("d.m.Y H:i:s", strtotime(open_thread($row["id"], 0, 1)[0]["date"])), "id" => $row["id"]];
		}
		return $arr;
	}
	
	// Создать запись в вопросе.
	function create_post(int $uid, int $pid) {
		global $pdb;
		
		$attachments = save_files($uid);
		
		$url = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
		$content = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', htmlspecialchars($_POST["content"]));
		
		$query = $pdb->prepare("INSERT INTO p" . $pid . "(uid, content, attachments) VALUES(?, ?, ?);");
		$query->bind_param("iss", $uid, $content, $attachments);
		$query->execute();
		
		$res = $pdb->query("SELECT id FROM p" . $pid . " ORDER BY id DESC limit 1;");
		$res->data_seek(0);
		return $res->fetch_array()[0];
	}
	
	// Создать новыую тему.
	function create_thread(int $uid) {
		global $db;
		global $pdb;
		
		$query = $db->prepare("INSERT INTO posts(subject, surveys) VALUES(?, ?);");
		$content = htmlspecialchars($_POST["subject"]);
		$surveys = encode_surveys();
		$query->bind_param("ss", $content, $surveys);
		$query->execute();
		
		$res = $db->query("SELECT id FROM posts ORDER BY id DESC limit 1;");
		$res->data_seek(0);
		$pid = $res->fetch_array()[0];
		
		$pdb->query("CREATE TABLE p" . $pid . " LIKE db.post_template;");
		
		create_post($uid, $pid);
		
		return $pid;
	}
	
	// Шапка темы.
	function thread_head(int $pid) {
		global $db;
		$res = $db->query("SELECT * FROM posts WHERE id = " . $pid . ";");
		$res->data_seek(0);
		return $res->fetch_assoc();
	}
	
	// Получить записи в вопросе.
	function open_thread(int $pid, int $start, int $count) {
		global $pdb;
		$res = $pdb->query("SELECT * FROM p" . $pid . " WHERE id > " . ($start ? $start : $start - 1) . " ORDER BY id LIMIT 10;");
		$res->data_seek(0);
		$chunk = [];
		for ($i = 0; ($post = $res->fetch_array()) && $i < $count; $i++) {
			$chunk[] = $post;
		}
		return $chunk;
	}
	
	// Общее количество записей в вопросе.
	function thread_length(int $pid) {
		global $pdb;
		$res = $pdb->query("SELECT COUNT(1) FROM p" . $pid . ";");
		$res->data_seek(0);
		return $res->fetch_array()[0];
	}
?>