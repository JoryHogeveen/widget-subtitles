=== Widget Subtitles ===
Contributors: keraweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YGPLMLU7XQ9E8&lc=NL&item_name=Widget%20Subtitles&item_number=JWPP%2dWS&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: widget, widget subtitle, subtitle, subtitles, sub title, sidebar
Requires at least: 3.0
Tested up to: 4.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a customizable subtitle to your widgets

== Description ==

This plugin adds a subtitle input field to all your widgets. You can also change the location of the subtitle and even use filters to change the subtitle output.

= Filter: `widget_subtitles_element` =
Allows you to change the HTML element for the subtitle

*	**Default:** span
*	**Parameters:**
	*	string (default)
	*	string (widget id)
	*	string (sidebar id)
	*	array  (widget data)

= Filter: `widget_subtitles_classes` =
Allows you to change the HTML element for the subtitle

*	**Default:** `array( 'widget-subtitle', 'widgetsubtitle', 'subtitle-LOCATION' );` Where LOCATION stands for your selected location
*	**Parameters:**
	*	array  (default classes)
	*	string (widget id)
	*	string (sidebar id)
	*	array  (widget data)

== Installation ==

1. Upload `/widget-subtitles` to the `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress
3. Go to *Appearance* > *Widgets* menu and fill out your subtitles

== Changelog ==

= 1.1 =
*	Add extra parameters to the filter hooks

= 1.0.1 =
*	Update textdomain hook

= 1.0 =
*	First version
