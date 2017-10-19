<?php
	function aprs_connect() {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (!$socket) {
			echo "error: can't create aprs socket\n";
			return false;
		}

		if (!socket_connect($socket, APRS_SERVER, APRS_SERVER_PORT)) {
			echo "error: can't connect to aprs server\n";
			return false;
		}

		$tosend = 'user ' . APRS_CALLSIGN . ' pass ' . APRS_PASSCODE . "\n";
		socket_write($socket, $tosend, strlen($tosend));
		$authstartat = time();
		$authenticated = false;
		while ($msgin = socket_read($socket, 1000, PHP_NORMAL_READ)) {
			if (strpos($msgin, APRS_CALLSIGN . ' verified') !== FALSE) {
				$authenticated = true;
				break;
			}
			// Timeout handling
			if (time()-$authstartat > 5)
				break;
		}
		if (!$authenticated) {
			echo "error: aprs auth timeout\n";
			return false;
		}
		return $socket;
	}

	function aprs_decimal_degrees_to_dms($decimal_degrees) {
		$result = array();

		$result['degrees'] = floor($decimal_degrees);
		$result['minutes'] = fmod(floor($decimal_degrees * 60), 60);
		$result['seconds'] = fmod(($decimal_degrees * 3600), 60);

		return $result;
	}

	function make_callsign_valid($string) {
		// Callsigns for AX.25 must:
		// Consist of only upper case letters and numbers
		// Have a base callsign before the SSID at least three characters long
		// Have a numeric SSID which falls in 0 - 15
		// Have a base callsign before the SSID no longer than six characters
		// In this context alphanumeric is defined as any ASCII values in the range:
		// decimal [65 - 90] - 'A'-'Z'
		// decimal [48 - 57] - '0'-'9'

		// Example:
		// [SK0NN Stockholm] ==> [SK0NN]
		// [SM6TKT/R] ==> [SM6TKT]
		// [AX1DCX-12223323123] ==> [AX1DCX]
		$keywords = preg_split("/[\s-\\/]+/", $string);
		$keywords[0] = preg_replace('/[^A-Z0-9]+/', '', $keywords[0]);
		if (!empty($keywords[1])) {
			if (strlen($keywords[0]) >= 7) {
				if (!($keywords[1] > 0 && $keywords[1] <= 9)) {
					$keywords[1] = '';
				}
			} else {
				if (!($keywords[1] > 0 && $keywords[1] <= 15)) {
					$keywords[1] = '';
				}
			}
		}
		return (empty($keywords[1]) ? $keywords[0] : $keywords[0] . "-" . $keywords[1]);
	}

	function aprs_send_location($callsign, $simplex_station, $latitude, $longitude, $pep, $agl,
		$gain, $aprs_text)
	{
		global $aprs_socket;

		echo "  sending location to aprs for $callsign\n";
		echo "    aprs text: $aprs_text\n";

		$timestamp = date('dHi');

		$degrees = aprs_decimal_degrees_to_dms(abs($latitude));
		$hundredths = round(($degrees['seconds']/60)*100);
		if ($hundredths == '100') {
			// In case "hundredths" is equal to 100, it is necessary to increase the minutes and clear the value further.
			// Example: 42.00N/01754.100E should become 42.00N/01755.00E
			$hundredths = '00';
			$degrees['minutes'] = $degrees['minutes'] + 1;
		}
		$latitude = str_pad($degrees['degrees'], 2, '0', STR_PAD_LEFT) .
			str_pad($degrees['minutes'], 2, '0', STR_PAD_LEFT) . '.' .
			str_pad($hundredths, 2, '0', STR_PAD_LEFT) .
			($latitude > 0 ? 'N' : 'S');

		$degrees = aprs_decimal_degrees_to_dms(abs($longitude));
		$hundredths = round(($degrees['seconds']/60)*100);
		if ($hundredths == '100') {
			// In case "hundredths" is equal to 100, it is necessary to increase the minutes and clear the value further.
			// Example: 42.00N/01754.100E should become 42.00N/01755.00E
			$hundredths = '00';
			$degrees['minutes'] = $degrees['minutes'] + 1;
		}
		$longitude = str_pad($degrees['degrees'], 3, '0', STR_PAD_LEFT) .
			str_pad($degrees['minutes'], 2, '0', STR_PAD_LEFT) . '.' .
			str_pad($hundredths, 2, '0', STR_PAD_LEFT) .
			($longitude > 0 ? 'E' : 'W');

		$phg = 'PHG';

		if ($pep >= 81)
			$phg .= '9';
		else if ($pep >= 64)
			$phg .= '8';
		else if ($pep >= 49)
			$phg .= '7';
		else if ($pep >= 36)
			$phg .= '6';
		else if ($pep >= 25)
			$phg .= '5';
		else if ($pep >= 16)
			$phg .= '4';
		else if ($pep >= 9)
			$phg .= '3';
		else if ($pep >= 4)
			$phg .= '2';
		else if ($pep >= 1)
			$phg .= '1';
		else
			$phg .= '0';

		$alt_feet = round($agl*3.28084);
		if ($alt_feet >= 5120)
			$phg .= '9';
		else if ($alt_feet >= 2560)
			$phg .= '8';
		else if ($alt_feet >= 1280)
			$phg .= '7';
		else if ($alt_feet >= 640)
			$phg .= '6';
		else if ($alt_feet >= 320)
			$phg .= '5';
		else if ($alt_feet >= 160)
			$phg .= '4';
		else if ($alt_feet >= 80)
			$phg .= '3';
		else if ($alt_feet >= 40)
			$phg .= '2';
		else if ($alt_feet >= 20)
			$phg .= '1';
		else
			$phg .= '0';

		$gain = round($gain);
		if ($gain > 9)
			$gain = 9;
		if ($gain < 0)
			$gain = 0;
		$phg .= $gain . '0'; // Directivity fixed to omni.

		if ($phg == 'PHG0000')
			$phg = '';

		if ($simplex_station) {
			$aprs_symbol1 = APRS_SYMBOL_SIMPLEX_STATION[0];
			$aprs_symbol2 = APRS_SYMBOL_SIMPLEX_STATION[1];
		} else {
			$aprs_symbol1 = APRS_SYMBOL_REPEATER[0];
			$aprs_symbol2 = APRS_SYMBOL_REPEATER[1];
		}

		$callsign = make_callsign_valid($callsign);

		$tosend = "$callsign>APBM1S,TCPIP*:@${timestamp}z" .
			"$latitude$aprs_symbol1$longitude$aprs_symbol2$phg$aprs_text\n";

		echo "    aprs data: $tosend";
		if (socket_write($aprs_socket, $tosend, strlen($tosend)) === false)
			echo "    send failed\n";
	}
?>
