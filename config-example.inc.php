<?php
	// Network ID from BrandMeister.conf
	define('NETWORK_ID',					2162);

	// User, passcode, server used for connecting to the APRS network.
	define('APRS_CALLSIGN',					'HA2NON-14');
	define('APRS_PASSCODE',					0);
	define('APRS_SERVER',					'hun.aprs2.net');
	define('APRS_SERVER_PORT',				14580);
	define('APRS_DEFAULT_TEXT',				'Brandmeister DMR');

	// MySQL user, password, host, Registry database name and table names.
	define('DMR_DB_USER',					'ham-dmr.hu');
	define('DMR_DB_PASSWORD',				'');
	define('DMR_DB_HOST',					'localhost');
	define('DMR_DB_NAME',					'Registry');
	define('DMR_DB_USERS_TABLE',			'Users');
	define('DMR_DB_REPEATERS_TABLE',		'Repeaters');
?>
