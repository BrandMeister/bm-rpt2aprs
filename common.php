<?php

  if (!array_key_exists("Services", $GLOBALS))
  {
    $services = array();

    foreach (scandir("/opt/BrandMeister/Instances/") as $configuration)
    {
      $contents = file_get_contents("/opt/BrandMeister/Instances/$configuration");
      if (preg_match('/network\s*=\s*(\d+)/', $contents, $matches))
      {
        $instance = $matches[1];

        $services[$instance] = "me.burnaway.BrandMeister.N$instance";

        if ($configuration == "default.conf")
        {
          // It is for backward compatibility
          $GLOBALS["NetworkID"] = $instance;
        }
      }      
    }

    $GLOBALS["Services"] = $services;
  }

  if (array_key_exists("NetworkID", $GLOBALS))
  {
    $network = $GLOBALS["NetworkID"];
    define("SERVICE_NAME", "me.burnaway.BrandMeister.N$network");
  }

  define("OBJECT_PATH", "/me/burnaway/BrandMeister");
  define("INTERFACE_NAME", "me.burnaway.BrandMeister");

  function escape($value)
  {
    if (is_float($value) && 
        (is_nan($value) ||
         is_infinite($value)))
    {
      // Replace NaN with NULL
      return NULL;
    }
    return $value;
  }

?>