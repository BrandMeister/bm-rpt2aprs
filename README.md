# bm-rpt2aprs

Uploads repeaters of a Brandmeister network to APRS.

The script queries repeater and hotspot IDs of a Brandmeister network ID
from the DBUS-API, queries location and other data from the
API, and uploads them to the APRS-IS as objects.

## Usage

- You'll need PHP CLI (ex. php5-cli).
- Rename (and edit) *config-example.inc.php* to *config.inc.php*.

You can periodically run this script from crontab. Crontab entry example:

```
0,30 *  *   *   *     /home/nonoo/bm-rpt2aprs/bm-rpt2aprs.php &>/dev/null
```

## Skipping APRS reporting

A sysop of a repeater or a hotspot can prevent APRS reporting by adding a tag  
NOGATE or NOAPRS to the 'Priority Message' field at the Brandmeister dashboard.

## Multiple SSIDs

The SSID of an APRS object is parsed from the repeater ID if the ID lenght is
exactly nine numbers.  
For example a hotspot using ID 244301810 is reported to the APRS-IS as OH3NFC-10.

