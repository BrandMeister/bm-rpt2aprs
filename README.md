# bm-rpt2aprs

Uploads repeaters of a Brandmeister network to APRS.

The script queries location and other data from the Brandmeister API,
and uploads them to the APRS-IS as objects. The repeater and hotspot IDs
are configured manually.

## Usage

- You'll need PHP CLI (ex. php5-cli).
- Rename (and edit) *config-example.inc.php* to *config.inc.php*.

You can periodically run this script from crontab. Crontab entry example:

```
0,30 *  *   *   *     /home/nonoo/bm-rpt2aprs/bm-rpt2aprs.php &>/dev/null
```

## NOTE

Add NOGATE tag to hotspot's 'Priority Message' at your Brandmeister dashboard to prevent location reporting to APRS.

