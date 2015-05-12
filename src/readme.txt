=== Client Dash ===

Contributors: BrashRebel, joelworsham
Tags: client, portal, dashboard, admin, users, webmaster
Requires at least: 3.8.0
Tested up to: 4.2.2
Stable tag: 1.6.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

= Take control of the WordPress dashboard =

With Client Dash, WordPress webmasters have full control of the WP-Admin area. With an end goal of helping improve the User Experience within WordPress specifically for clients, we've created and implemented numerous powerful features for simplifying and customizing the dashboard.

Here are some of the major features:

= Customize the Admin Menu =

As of version 1.6, you are now able to completely customize the WordPress admin menu using the built in, drag and drop menu system you are already familiar with. This will enable you to:

* Reorder all menu items
* Rearrange menu items
* Remove menu items
* Customize menu items (label, link, icon, etc.)
* Add new menu items

Customizing the admin menu previously was very complicated and no elegant, intuitive solutions existed. With Client Dash, it is now so easy that anyone familiar with the existing WordPress menu system can do it.

Admin menus are also controlled on a per role basis so you are able to completely control the admin menu for each individual role.

= Dashboard Widget Manager =

Have you ever wished you could customize the WordPress dashboard a little more? Well if you are capable of using the built in WordPress widget manager than with Client Dash you've found your solution.

We implemented a powerful dashboard widget control feature which works exactly the same as the WordPress widget manager you're already familiar with. So now, adding, removing, rearranging and customizing WP dashboard widgets is easier than ever before.

= Extremely Extensible =

From the start, Client Dash has been created with developers in mind. We've worked very hard to make extending Client Dash as easy as possible. To that end, we've created an Extension Boilerplate which does almost all of the work for you...and that is only the beginning!

If you are a developer and want to learn more about extending Client Dash, please subscribe to our [mailing list](http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Readme%20link&utm_campaign=Client%20Dash%20Plugin). We'll be sharing our documentation, tips, features and beta releases that way.

= What else does Client Dash do? =

Upon activation, Client Dash modifies the `wp-admin` in the following ways:

* Removes all default dashboard widgets
* Creates several new admin pages and adds their links under Dashboard in the admin menu
* Creates new dashboard widgets with large buttons that direct users to these new pages
* Adds tabs with helpful information on each of these new pages
* Removes the WordPress logo and menu from the toolbar
* (Multisite) Removes the My Sites menu from the toolbar and the admin menu
* (Multisite) Moves the My Sites information to a tab on the Account page

Also included under Client Dash settings are:

* Controls for restricting access to content on Client Dash pages for specific roles
* An intuitive icon selector for changing the dashicons used for Client Dash pages and dashboard widgets
* An option to reset all Client Dash settings to their defaults
* A directory of existing add-ons for Client Dash

We are also extremely receptive to suggestions, feature requests and collaborations so if you have anything to add or that you would like to see included in Client Dash, please visit the support forum here and engage us. Also, stay tuned as we are working on lots of new updates to this plugin and are also actively developing some exciting add-ons. Lots more to come!

= Like this plugin? =

We love making plugins for WordPress! This is just one of the many we have released and are working on. If you'd like to stay informed about what we release and receive exclusive discounts on our premium plugins, [subscribe here](http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Readme%20link&utm_campaign=Client%20Dash%20Plugin). We've got some really cool stuff in the works so you'll be the first to know about all of it.

== Installation ==

Using this plugin is very simple. All you have to do is:

1. Upload the `client-dash` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Configure settings by going to Settings - >Client Dash

== Screenshots ==

1. The new wp-admin dashboard. This is your new landing page. You can customize visibility of each item based on the role.

2. The display customization page for the core Client Dash pages and their content. You can customize who sees what with ease (including custom roles)!

3. Client Dash offers the ability to completely take control of the default WordPress Administration Menu (on the left of the admin screen). It's the menu system that we all have come to know and love.

4. You can customize the default icons used in Client Dash with this beautiful icon selector (supports all WordPress standard Dashicons).

5. Webmasters have the ability to add a custom section that can be customized to fit any companies needs.

6. Take control over the dashboard with the new dashboard widget customizer! It uses the WordPress Widget API, so you'll feel right at home.

7. Client Dash has a few addons available, and many more to come!

== Changelog ==

= 1.6.8 =
* Fixed potential fatal errors when using dashboard widgets.
* Fix CD core widgets not working

= 1.6.7 =
* Very minor bug fixes.
* Testing in the WordPress 4.1 environment.

= 1.6.6 =
* Allowed the Links menu item to be available in Client Dash only when the link manager functionality is enabled.
* Fixed edge-case fatal error due to some inconsistency in the code.

= 1.6.5 =
* Made jQuery live for the icon-selector dropdown in CD Settings -> Menus so that it would also work for newly added menu items.
* Made sure the filter that removes CD admin menu nav menus from WP Core lists applies everywhere (they were showing in the customizer in the custom menu widget).
* Menus generated for administrators weren't getting all items, due to custom capabilities. Now if you have "manage_options" (typically only admins) you get all menu items.
* Changed how the new admin menu was added in order to gain more control over order, which fixed the sub-menu being out of order bug.
* Changed the code to tell which page is the current page to be a little more specific.
* Menu items in CD Settings -> Menus now display not just live icons, but images and svg as well. (svg is a bit buggy still though).

= 1.6.4 =
* Default dashboard widgets for CD Core had "Client Dash" in them. Removed that.
* Made link visibility in Reports -> Site dependent on user capabilities.
* Fixed "Plugins 0" on menus page to be "Plugins".

= 1.6.3 =
* Dashboard sidebar widgets erased after messing with core widget area
* Add nag on icons page if under wp 3.9 because not all icons will show
* Syntax error on PHP 5.3 and previous
* Network activated or activated on main site of multi-site causes CD styles to exist on network admin dashboard
* Dashboard widget CD Core items visibility not dynamic on initial install
* Webmaster widget custom title modifications no longer allowed from CD Settings -> Widgets
* Altering CD widgets erases WP widgets and vice versa

= 1.6.2 =
* Small bug with widgets on initial install.

= 1.6.1 =
* Incorrect upload.

= 1.6.0 =
* Added adminmenu customizing functionality under Settings -> Menus.
* Revised widgets area to properly use the WP Widget API.
* Made core much more extensible.
* Created Widgets, Menus, and Settings API for Client Dash.
* Other bug fixes and improvements.

= 1.5.5 =
* Fixed PHP notice error.

= 1.5.4 =
* Minor improvement in backend Roles functionality (allows compatibility with new WooCommerce extension).

= 1.5.3 =
* Multiple content sections in the same tab were not working.

= 1.5.2 =
* Postboxes being half width was targeting all pages, when it should have only been targeting the dashboard.

= 1.5.1 =
* PHP backwards compatibility issue.
* Check if class does not exist for Dash Widgets.

= 1.5 =
* Added dashboard widget customization.
* Added Settings -> Widgets for dashboard widget customization.
* Added defaults to Roles settings.
* Reversed checkbox logic for Roles settings.
* Added ability to strip out any dashboard widget settings that may be set.
* Added cd-tips, which are handy tooltips.
* Added toggle switches for disabling entire pages within the Roles settings.
* Renamed "Roles" tab in "Settings" to "Display".
* Changed content "block" system to a content "section" system and separated them out with menus under the tabs.
* Improved some setting visuals.

= 1.4 =
* Added Roles customization settings.
* Overall cleanup of plugin visuals.
* Added some new nags for specific users.
* Added new "content section" system for added customization.
* Added references to three exciting new extensions on the addons tab.
* Created new Domain tab on Help page.
* Added support for custom post types in lists on Reports - >Site.
* Added link to view current role capabilities on Account - >About You.
* Created some handy CSS classes.

= 1.3.2 =
* Fixed php error on settings page for widgets.
* Added `.cd-col-two` CSS class for two column layouts.

= 1.3.1 =
* Fixed save button issue on Icons page.
* Added install/activate/deactivate button on addons page.

= 1.3 =
* Added Dashicons customization in Settings.
* Added Addons page for browsing available addons.
* Added alert for empty Webmaster tab.
* Added ability to change number of feed entries pulled.
* Added webmaster dashboard widget.
* Re-worded media-library size under reports.
* Added error catching for feeds.
* Added current user URL to Account- >About.
* Now displays help dropdown for user role to output capabilities on Account- >About.
* Added filter to hide submit button when desired.
* Added conditionals for displaying account information on About tab of Account.

= 1.2.2 =
* Changed `get_current_theme()` to `wp_get_theme()` since the former is deprecated (thanks to @sethalling).
* Modified method for getting role name (thanks @sethalling).
* Added `cd_(WIDGET NAME)_widget` filter on contents of current dashboard widgets.

= 1.2.1 =
* Fixed php warning.
* Safeguarded include_once occurrences for potential issues.
* Reformatted code.
* Added missing files causing fatal error.
* Fixed dashboard widget broken links.
* Re-ordered dashboard widgets.
* Re-ordered menu items.
* Added conditional to only show sites tab under account if is a multisite.
* Removed Webmaster functionality (will be in future release).
* Corrected link for Reports dashboard widget

= 1.2 =
* Enqueued `cd.main.js` with `updown` function.
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

= 1.6.2 =
* Please update to fix a small widget issue on installation.
