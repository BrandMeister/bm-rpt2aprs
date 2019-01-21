<?php
	function dbus_get_repeater_ids_for_network($service) {
		$connection = new DBus(DBus::BUS_SYSTEM, false);
		$proxy = $connection->createProxy($service, OBJECT_PATH, INTERFACE_NAME);
		$type = new DbusUInt32(2);
		$result = $proxy->getContextList($type);

		if (is_object($result) && get_class($result) == 'DbusArray') {
			$list = $result->getData();

			foreach ($list as $banner) {
				$result = $proxy->getRepeaterData($banner);

				if (is_object($result) && get_class($result) == 'DbusSet') {
					$set = $result->getData();
					$result_ids[] = $set[1];
				}
			}
		}
		return $result_ids;
	}
?>
