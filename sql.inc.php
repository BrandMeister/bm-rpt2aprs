<?php
	function sql_connect() {
		$sql = mysqli_connect(DMR_DB_HOST, DMR_DB_USER, DMR_DB_PASSWORD, DMR_DB_NAME);
		if (!$sql) {
			echo "error: can't connect to mysql database!\n";
			return false;
		}

		// Making sure we are using UTF8 for everything.
		$sql->query("set names 'utf8'");
		$sql->query("set charset 'utf8'");

		return $sql;
	}

	function sql_get_repeater_ids_for_network($sql) {
		$query_response = $sql->query('select `ID` from `' . DMR_DB_REPEATERS_TABLE . '` ' .
			'where `Network`="' . NETWORK_ID . '"');
		if (!$query_response) {
			echo "mysql query error: $sql->error\n";
			return false;
		}
		$result = array();
		if ($query_response) {
			while ($row = $query_response->fetch_row())
				$result[] = $row[0];
		}
		return $result;
	}
?>
