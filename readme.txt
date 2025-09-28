=== ARC Lens ===
Contributors: ARC Software
Tags: filters, search, collection, api
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Facet filters and search for registered collections via the ARC Gateway plugin.

== Description ==
ARC Lens provides an admin interface and frontend filtering system for collections registered via the ARC Gateway plugin. It allows developers to quickly display filters and fetch data from registered collection routes.

Key features:
* Display filter sets for any registered collection.
* Fetch filtered data from the collection's REST API endpoints.
* Fully JS-driven, minimal PHP rendering.
* Easy integration with ARC Gateway collections.

== Installation ==
1. Upload the `arc-lens` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Ensure the ARC Gateway plugin is also installed and active.
4. Add filters via the admin page or directly in templates using `Render::output($collectionKey)`.

== Frequently Asked Questions ==

= Does ARC Lens work without ARC Gateway? =
No. ARC Lens depends on ARC Gateway for collection registration and route management.

= Can I display filters for custom collections? =
Yes, simply provide the collection alias or key when calling `Render::output()`.

== Screenshots ==
1. Admin page showing registered collections and filters.
2. Frontend filter set rendering example.

== Changelog ==
= 1.0.0 =
* Initial release with basic filter rendering and collection integration.
* Fetching data from collection REST routes.
* Admin page for testing collections and filters.

== Upgrade Notice ==
= 1.0.0 =
Initial release.

== Arbitrary section ==
This plugin is developed and maintained by ARC Software.

