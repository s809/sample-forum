<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	function encode_surveys() {
		$offset = 0;
		$surveys = ["voted" => 0];
		
		if (!array_key_exists("variant_count", $_POST)) {
			$surveys["list"] = [];
			return json_encode($surveys);
		}
		
		foreach ($_POST["variant_count"] as $key => $value) {
			$survey["name"] = $_POST["survey_names"][$key];
			$survey["type"] = $_POST["type"][$key];
			
			for ($index = $offset; $index < $offset + $value; $index++) {
				if ($_POST["variants"][$index] == "") continue;
				
				$variant = ["voted" => 0];
				$variant["name"] = $_POST["variants"][$index];
				
				$survey["variants"]["v" . str_pad($index - $offset, strlen($value), "0", STR_PAD_LEFT)] = $variant;
			}
			
			$offset += $value;
			
			$surveys["list"]["s" . str_pad($key, strlen(array_sum($_POST["variant_count"])), "0", STR_PAD_LEFT)] = $survey;
			unset($survey);
		}
		
		return json_encode($surveys);
	}
	
	function is_voted(int $uid, int $pid) {
		global $db;
		
		$res = $db->query('SELECT JSON_CONTAINS(voted_in, "' . $pid . '") FROM users WHERE id = ' . $uid . ';');
		$res->data_seek(0);
		$ret = $res->fetch_array();
		
		return $ret[0];
	}
	
	function vote(int $uid, int $pid) {
		global $db;
		
		foreach ($_POST as $sid => $vals) {
			foreach ($vals as $val) {
				$query = $db->prepare("UPDATE posts SET surveys = JSON_SET(surveys, CONCAT('$.list.\"', ?, '\".variants.\"', ?, '\".voted'), JSON_EXTRACT(surveys, CONCAT('$.list.\"', ?, '\".variants.\"', ?, '\".voted')) + 1);");
				$query->bind_param("ssss", $sid, $val, $sid, $val);
				$query->execute();
			}
		}
		
		$db->query("UPDATE posts SET surveys = JSON_REPLACE(surveys, '$.voted', JSON_EXTRACT(surveys, '$.voted') + 1);");
		
		$db->query('UPDATE users SET voted_in = JSON_ARRAY_APPEND(COALESCE(voted_in, "[]"), "$", ' . $pid . ') where id = ' . $uid . ';');
	}
?>