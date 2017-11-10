=== Widget Subtitles ===
Contributors: keraweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YGPLMLU7XQ9E8&lc=NL&item_name=Widget%20Subtitles&item_number=JWPP%2dWS&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: widget, widget subtitle, subtitle, subtitles, sub title, sidebar
Requires at least: 3.0
Tested up to: 4.9
Requires PHP: 5.2.4
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a customizable subtitle to your widgets

== Description ==

This plugin adds a subtitle input field to all your widgets. You can also change the location of the subtitle and even use filters to change the subtitle output.

= Filter: `widget_subtitles_element` =
Allows you to change the HTML element for the subtitle.  
Since  1.0  

*	**Default:** `span`
*	**Parameters:**
	*	`string`    Default element.
	*	`string`    Widget ID, widget name + instance number.
	*	`string`    Sidebar ID where this widget is located. (since 1.1)
	*	`array`     All widget data. (since 1.1)
	*	`WP_Widget` The widget type class. (since 1.1.3)
*	**Return:** `string` A valid HTML element.

= Filter: `widget_subtitles_classes` =
Allow filter for subtitle classes to overwrite, remove or add classes.  
Since  1.0  

*	**Default:** `array( 'widget-subtitle', 'widgetsubtitle', 'subtitle-{LOCATION}' );` *Where {LOCATION} stands for your selected location*.
*	**Parameters:**
	*	`string`    Default element.
	*	`string`    Widget ID, widget name + instance number.
	*	`string`    Sidebar ID where this widget is located. (since 1.1)
	*	`array`     All widget data. (since 1.1)
	*	`WP_Widget` The widget type class. (since 1.1.3)
*	**Return:** `array` An array of CSS classes.

= Filter: `widget_subtitles_default_location` =
Sets the default location for subtitles.  
Since  1.1.2  

*	**Default:** `after-inside`
*	**Parameters:** `string` The default subtitle location.
*	**Return:** `string` Options: `after-inside`, `after-outside`, `before-inside`, `before-outside`.

= Filter: `widget_subtitles_edit_location_capability` =
Change the capability required to modify subtitle locations.  
Since  1.1.2  

*	**Default:** `edit_theme_options`
*	**Parameters:** `string` The default capability.
*	**Return:** `string` The new capability.

You can use these filters inside your theme functions.php file or in a plugin.

== Installation ==

Installation of this plugin works like any other plugin out there. Either:

1. Upload and unpack the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Or search for "Widget Subtitles" via your plugins menu.

== Screenshots ==

1. The subtitle options
2. Frontend subtitle example (after title - inside heading)

== Changelog ==

= 1.1.3 =

*	**Enhancement:** Add fourth `WP_Widget` object parameter to the `widget_subtitles_element` and `widget_subtitles_classes` filters.
*	**Enhancement:** JavaScript improvements.
*	**Compatibility:** Tested with WordPress 4.9.

= 1.1.2 =

*	**Feature:** new filter `widget_subtitles_default_location`. Sets the default location for subtitles.
*	**Feature:** new filter `widget_subtitles_edit_location_capability`. Change the capability required to modify subtitle locations. [#8](https://github.com/JoryHogeveen/widget-subtitles/issues/8)
*	**Enhancement:** Fix CodeClimate coding standards issues.
*	Screenshots added

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/7)

= 1.1.1 =

*	**Enhancement:** Fixed code inspections from CodeClimate.
*	**Compatibility:** Tested with WordPress 4.8.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/5)

= 1.1 =
*	**Enhancement:** Add extra parameters to the filter hooks.

= 1.0.1 =
*	**Enhancement:** Update textdomain hook.

= 1.0 =
*	First version.

== Other Notes ==

You can find me here:

*	[Keraweb](http://www.keraweb.nl/ "Keraweb")
*	[GitHub](https://github.com/JoryHogeveen/widget-subtitles/)
*	[LinkedIn](https://nl.linkedin.com/in/joryhogeveen "LinkedIn profile")
