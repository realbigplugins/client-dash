=== Client Dash ===

Contributors: BrashRebel, joelworsham
Tags: client, portal, dashboard, admin, users, webmaster
Requires at least: 3.8.0
Tested up to: 3.9.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

= Webmasters rejoice! =

At long last, a vastly improved interface for clients has arrived. Client Dash seeks to provide a simplified and intuitive admin user experience for customers who login to WordPress sites which were developed and are managed by an agency/webmaster.

= What does Client Dash do? =

Upon activation, Client Dash modifies the wp-admin in the following ways:

* Removes all default dashboard widgets
* Provides options for selectively adding back dashboard widgets
* Creates several new admin pages and adds their links under Dashboard in the admin menu
* Creates new dashboard widgets with large buttons that direct users to these new pages
* Adds tabs with helpful information on each of these new pages
* Removes the WordPress logo and menu from the toolbar
* (Multisite) Removes the My Sites menu from the toolbar and the admin menu
* (Multisite) Moves the My Sites information to a tab on the Account page

There is a lot more to come from this plugin in the future. Client Dash has been designed from day 1 to be as flexible as possible so if you are a developer you will absolutely love it. In the near future we will be publishing detailed documentation on how this plugin can be modified and extended. We will also be releasing numerous extensions of our own which will integrate with a variety of useful external tools to help you provide even more value to your customers.

We are also extremely receptive to suggestions, feature requests and colaborations so if you have anything to add or that you would like to see us add, please visit the support forum here and engage us. Also stay tuned as we are working on lots of new updates to this plugin and are also actively developing some exciting add-ons.

== Installation ==

Using this plugin is very simple. All you have to do is:

1. Upload the `client-dash` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Configure settings by going to Settings - >Client Dash

== Screenshots ==

1. The wp-admin dashboard as seen by users with the Administrator role. The widgets and pages this plugin creates will be visible by anyone with the Author role and above (Version 1.0).

== Changelog ==

= 1.2.1 =
* Fixed php warning.
* Safeguarded require_once occurrences for potential issues.
* Reformatted code.
* Added missing files causing fatal error.
* Fixed dashboard widget broken links.
* Re-ordered dashboard widgets.
* Re-ordered menu items.
* Added conditional to only show sites tab under account if is a multisite.
* Removed Webmaster functionality (will be in future release)
* Corrected link for Reports dashboard widget

= 1.2 =
* Enqueued `client-dash.js` with `updown` function.
* Added `cd-click` class to `client-dash.css` for `cursor: pointer`.
* Rearranged information on Site tab on Reports page to be more clear.
* Added a few pieces of data to Site tab on Reports page.
* Removed placeholder content from FAQ tab.
* Allow extensions to add tabs to specific pages.
* Increase extensibility of settings page.
* Added "Webmaster" tab to the settings page.
* Allow user to disable/enable webmaster page.
* Allow user to rename webmaster page/menu-item.
* Allow user to add custom html content to a custom tab on webmaster page.

= 1.1.2 =
* Patch to fix potential fatal error when running on older versions of PHP.

= 1.1.1 =
* Fixed fatal error problem.

= 1.1 =
* Added options page under "Settings->Client Dash".
* Added ability to selectively display dashboard widgets that are automatically removed on options page.
* Removal of dashboard widgets now dynamic, so only Client Dash widgets will exist.
* Removed "Screen Options" and "Help" from dashboard.
* Removed dashboard widgets from bbPress and Woocommerce

= 1.0 =
* Initial release.
* Includes Help page with an Info tab.
* Includes Account page with About and Sites tabs.
* Includes Reports page with Site Overview tab.
* Removes default WordPress dashboard widgets.
* Removes WP logo and menu from toolbar.
* Adds dashboard widgets for each new submenu page.

== Upgrade Notice ==

= 1.2.1 =
* PLEASE UPDATE IMMEDIATELY: Fixed fatal error issue.