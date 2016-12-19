# bm-rpt2aprs

Uploads repeaters of a Brandmeister network to APRS.

The script queries repeater and hotspot IDs of a Brandmeister network ID
from the Registry MySQL database, queries location and other data from the
API, and uploads them to the APRS-IS as objects.

## Usage

- You'll need PHP CLI (ex. php5-cli).
- Rename (and edit) *config-example.inc.php* to *config.inc.php*.
- The Registry MySQL database is the database found on each Brandmeister server.
  Make sure your database user has select access rights on Registry database tables.

You can periodically run this script from crontab. Crontab entry example:

```
0,30 *  *   *   *     /home/nonoo/bm-rpt2aprs/bm-rpt2aprs.php &>/dev/null
```
