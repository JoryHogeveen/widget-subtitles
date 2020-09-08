=== Widget Subtitles ===
Contributors: keraweb
Donate link: https://www.keraweb.nl/donate.php?for=widget-subtitles
Tags: widget, widget subtitle, subtitle, subtitles, sub title, sidebar
Requires at least: 3.0
Tested up to: 5.5
Requires PHP: 5.2.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a customizable subtitle to your widgets

== Description ==

This plugin adds a subtitle input field to all your widgets. You can also change the location of the subtitle and even use filters to change the subtitle output.

= Filters =
*   [`widget_subtitles_element`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_element)
*   [`widget_subtitles_classes`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_classes)
*   [`widget_subtitles_default_location`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_default_location)
*   [`widget_subtitles_edit_location_capability`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_edit_location_capability)
*   [`widget_subtitles_available_locations`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_available_locations)
*   [`widget_subtitles_add_subtitle`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitles_add_subtitle)
*   [`widget_subtitle`](https://github.com/JoryHogeveen/widget-subtitles/wiki#filter-widget_subtitle)

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

= 1.2.1 =

*	**Enhancement:** Improve updating subtitles to reflect WordPress default title.
*	**Compatibility:** Tested with WordPress 5.3.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/12)

= 1.2 =

*	**Feature:** New filter: `widget_subtitle` to change the subtitle for a widget. Similar to WP's `widget_title`.
*	**Feature:** New filter: `widget_subtitles_add_subtitle` to allow custom subtitle location handlers.
*	**Enhancement:** Extended filter: `widget_subtitles_available_locations` now allows custom locations.
*	**Enhancement:** Make use of `wp_get_sidebars_widgets()` instead of a global to get the related sidebar ID from a widget instance.
*	**Documentation:** Created a wiki on GitHub.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/11)

= 1.1.4.1 =

*	**Fix:** PHP notice.

= 1.1.4 =

*	**Enhancement:** Form JS handling.
*	**Enhancement:** Add support links on plugins overview page.
*	**Maintenance:** Updated to CodeClimate v2.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/10)

= 1.1.3 =

*	**Feature:** new filter `widget_subtitles_available_locations`. Overwrites the available locations for a widget.
*	**Enhancement:** Add fourth `WP_Widget` object parameter to the `widget_subtitles_element` and `widget_subtitles_classes` filters.
*	**Enhancement:** JavaScript improvements.
*	**Compatibility:** Tested with WordPress 4.9.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/widget-subtitles/pull/9)

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
