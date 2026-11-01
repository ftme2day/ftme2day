# FTME2Day Analytics Tags (WordPress)

Drop `ftme2day-analytics.php` into `wp-content/mu-plugins/` (or zip and install as a plugin), then add to `wp-config.php`:

```php
define( 'FTME_GTM_ID', 'GTM-XXXXXXX' );   // preferred
// define( 'FTME_GA4_ID', 'G-XXXXXXXXXX' ); // only used when no GTM ID is set
```

Logged-in editors/admins are not tracked.

## Events pushed

| event | when | params |
|---|---|---|
| `phone_click` | click on `tel:` link | `link_url` |
| `email_click` | click on `mailto:` link | `link_url` |
| `file_download` | link to pdf/doc/xls/ppt/zip/rar | `link_url`, `file_extension` |
| `outbound_click` | link to another domain | `link_url`, `link_domain` |
| `form_submit` | any form submit | `form_id`, `form_action` |
| `scroll_depth` | 25/50/75/90% | `percent_scrolled` |

## In GTM
For each event create a Custom Event trigger (same name) and a GA4 Event tag using the parameters above as Data Layer Variables. Mark `phone_click`, `email_click` and `form_submit` as key events in GA4.
