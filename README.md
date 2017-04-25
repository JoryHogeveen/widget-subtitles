# Widget Subtitles #
Add a customizable subtitle to your widgets

[![WordPress Plugin version](https://img.shields.io/wordpress/plugin/v/widget-subtitles.svg?style=flat)](https://wordpress.org/plugins/widget-subtitles/)
[![WordPress Plugin WP tested version](https://img.shields.io/wordpress/v/widget-subtitles.svg?style=flat)](https://wordpress.org/plugins/widget-subtitles/)
[![WordPress Plugin downloads](https://img.shields.io/wordpress/plugin/dt/widget-subtitles.svg?style=flat)](https://wordpress.org/plugins/widget-subtitles/)
[![WordPress Plugin rating](https://img.shields.io/wordpress/plugin/r/widget-subtitles.svg?style=flat)](https://wordpress.org/plugins/widget-subtitles/)
[![Travis](https://secure.travis-ci.org/JoryHogeveen/widget-subtitles.png?branch=master)](http://travis-ci.org/JoryHogeveen/widget-subtitles)
[![Code Climate](https://codeclimate.com/github/JoryHogeveen/widget-subtitles/badges/gpa.svg)](https://codeclimate.com/github/JoryHogeveen/widget-subtitles)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://github.com/JoryHogeveen/widget-subtitles/blob/master/license.txt)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YGPLMLU7XQ9E8&lc=NL&item_name=Widget%20Subtitles&item_number=JWPP%2dWS&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest)

## Description

This plugin adds a subtitle input field to all your widgets. You can also change the location of the subtitle and even use filters to change the subtitle output.

### Filters

#### `widget_subtitles_element`
Allows you to change the HTML element for the subtitle.  
Since  1.0  

*	**Default:** `span`
*	**Parameters:**
	*	`string` Default element.
	*	`string` Widget ID, widget name + instance number.
	*	`string` Sidebar ID where this widget is located. (since 1.1)
	*	`array`  All widget data. (since 1.1)
*	**Return:** `string` A valid HTML element.

#### `widget_subtitles_classes`
Allow filter for subtitle classes to overwrite, remove or add classes.  
Since  1.0  

*	**Default:** `array( 'widget-subtitle', 'widgetsubtitle', 'subtitle-{LOCATION}' );` Where {LOCATION} stands for your selected location
*	**Parameters:**
	*	`array`  Default classes.
	*	`string` Widget ID, widget name + instance number.
	*	`string` Sidebar ID where this widget is located. (since 1.1)
	*	`array`  All widget data. (since 1.1)
*	**Return:** `array` An array of CSS classes.

#### `widget_subtitles_default_location`
Sets the default location for subtitles.  
Since  1.1.2  

*	**Default:** `after-inside`
*	**Parameters:** `string` The default subtitle location.
*	**Return:** `string` Options: `after-inside`, `after-outside`, `before-inside`, `before-outside`.

You can use these filters inside your theme functions.php file or in a plugin.

## Installation

1. Upload `/widget-subtitles` to the `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress
3. Go to *Appearance* > *Widgets* menu and fill out your subtitles
