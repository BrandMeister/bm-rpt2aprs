#!/usr/bin/php
<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	chdir(dirname(__FILE__));

	include('config.inc.php');
	include('dbus.inc.php');
	include('aprs.inc.php');

	echo "connecting to aprs...\n";
	$aprs_socket = aprs_connect();
	if ($aprs_socket === false)
		return 1;

	$repeater_ids = dbus_get_repeater_ids_for_network();

	if ($repeater_ids) {
		foreach ($repeater_ids as $repeater_id) {
			echo "getting info for repeater id $repeater_id...\n";
			$ctx = stream_context_create(array(
				'http' => array(
					'timeout' => 5
				)
			));
			$result = file_get_contents("https://api.brandmeister.network/v1.0/repeater/?action=GET&q=$repeater_id", 0, $ctx);
			$result = json_decode($result);
			if (!isset($result->callsign)) {
				echo "  no callsign, ignoring\n";
				continue;
			}
			if ($result->lat == 0 || $result->lng == 0) {
				echo "  invalid coordinates, ignoring\n";
				continue;
			}
			if (time()-strtotime($result->last_updated) > 600) {
				echo "  last update was too long ago, ignoring\n";
				continue;
			}

			if ($result->priorityDescription != '')
				$description = $result->priorityDescription;
			else {
				$description = explode('-', $result->hardware);
				$description = $description[0];
				$description = explode(' ', $description);
				$description = $description[0];
				$description = str_replace('_', ' ', $description);
				if ($description == '')
					$description = APRS_DEFAULT_TEXT;
			}

			aprs_send_location($result->callsign, ($result->tx == $result->rx), $result->lat,
				$result->lng, $result->pep, $result->agl, $result->gain, $description . ' ' .
				$result->tx . '/' . $result->rx . ' CC' . $result->colorcode);
		}
	}


	socket_close($aprs_socket);
?>
