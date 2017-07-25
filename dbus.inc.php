<?php
function dbus_get_repeater_ids_for_network() {

      $connection = new DBus(DBus::BUS_SYSTEM, true);
      $service = "me.burnaway.BrandMeister.N".NETWORK_ID;
      $proxy = $connection->createProxy($service, "/me/burnaway/BrandMeister", "me.burnaway.BrandMeister");

      $result = $proxy->getContextList();

      if ((is_object($result)) &&
          (get_class($result) == "DbusArray"))
      {
        $list = $result->getData();
        foreach ($list as $banner)
        {
          $result = $proxy->getRepeaterData($banner);
          if ((is_object($result)) &&
              (get_class($result) == "DbusSet"))
          {
            $set = $result->getData();

            $result_ids[] = $set[1];
          }
        }
      }
  return $result_ids;
}
?>
