<?php
/**
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Widget_Subtitles
 * @since   0.1.0
 * @version 1.1.4.1
 * @licence GPL-2.0+
 * @link    https://github.com/JoryHogeveen/widget-subtitles
 *
 * @wordpress-plugin
 * Plugin Name:       Widget Subtitles
 * Plugin URI:        https://wordpress.org/plugins/widget-subtitles/
 * Description:       Add a customizable subtitle to your widgets
 * Version:           1.1.4.1
 * Author:            Jory Hogeveen
 * Author URI:        http://www.keraweb.nl
 * Text Domain:       widget-subtitles
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/JoryHogeveen/widget-subtitles
 *
 * @copyright 2015-2018 Jory Hogeveen
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'WS_Widget_Subtitles' ) ) {

/**
 * Plugin initializer class.
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Widget_Subtitles
 * @since   0.1.0
 * @version 1.1.4.1
 */
final class WS_Widget_Subtitles
{
	/**
	 * The single instance of the class.
	 *
	 * @since  0.1.0
	 * @var    \WS_Widget_Subtitles
	 */
	private static $_instance = null;

	/**
	 * The plugin basename.
	 *
	 * @since  1.1.4
	 * @var    string
	 */
	public static $_basename = '';

	/**
	 * The plugin i18n domain.
	 *
	 * @since  1.1.4.1
	 * @var    string
	 */
	public static $_domain = 'widget-subtitles';

	/**
	 * Possible locations of the subtitle.
	 *
	 * @since  0.1.0
	 * @var    array
	 */
	private $locations = array();

	/**
	 * The default subtitle location.
	 *
	 * @since  1.1.2
	 * @var    string
	 */
	private $default_location = 'after-inside';

	/**
	 * The capability required to modify subtitle locations.
	 *
	 * @since  1.1.2
	 * @var    string
	 */
	private $location_cap = 'edit_theme_options';

	/**
	 * Constructor.
	 *
	 * @since   0.1.0
	 * @access  private
	 */
	private function __construct() {
		self::$_instance = $this;
		self::$_basename = plugin_basename( __FILE__ );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Main Genesis Widget Subtitles.
	 *
	 * Ensures only one instance of Widget Subtitle is loaded or can be loaded.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @static
	 * @see     ws_widget_subtitles()
	 * @return  WS_Widget_Subtitles
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init function/action and register all used hooks and data.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  void
	 */
	public function init() {

		/**
		 * Change the capability required to modify subtitle locations.
		 * Default: `edit_theme_options`.
		 *
		 * @since  1.1.2
		 * @param  string  $location_cap  The capability.
		 * @return string
		 */
		$this->location_cap = apply_filters( 'widget_subtitles_edit_location_capability', $this->location_cap );

		$loc['before']  = __( 'Before title', self::$_domain );
		$loc['after']   = __( 'After title', self::$_domain );
		$loc['outside'] = __( 'Outside heading', self::$_domain );
		$loc['inside']  = __( 'Inside heading', self::$_domain );

		/**
		 * Sets the default location for subtitles.
		 * This will be overwritten once a widget is saved.
		 *
		 * @since   1.1.2
		 *
		 * @param   string  $subtitle_location  The subtitle location (default: 'after-inside').
		 * @return  string  Options: 'after-inside', 'after-outside', 'before-inside', 'before-outside'.
		 */
		$default = apply_filters( 'widget_subtitles_default_location', $this->default_location );
		$default = explode( '-', $default );

		foreach ( $default as $key => $value ) {
			if ( isset( $loc[ $value ] ) && 2 === count( $default ) ) {
				$default[ $key ] = $loc[ $value ];
				continue;
			} else {
				$default = array( $loc['after'], $loc['inside'] );
				break;
			}
		}

		$default = implode( ' - ', $default );

		$this->locations = array(
			'' => __( 'Default', self::$_domain ) . ' (' . $default . ')',
			// before title, outside title element.
			'before-outside' => $loc['before'] . ' - ' . $loc['outside'],
			// before title, inside title element
			'before-inside' => $loc['before'] . ' - ' . $loc['inside'],
			// after title, outside title element
			'after-outside' => $loc['after'] . ' - ' . $loc['outside'],
			// after title, inside title element
			'after-inside' => $loc['after'] . ' - ' . $loc['inside'],
		);

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'in_widget_form', array( $this, 'in_widget_form' ), 9, 3 );
		add_filter( 'widget_update_callback', array( $this, 'widget_update_callback' ), 10, 4 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) );

		// Add links to plugins page.
		add_action( 'plugin_row_meta', array( $this, 'action_plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Load the plugin's translated strings.
	 * @since   0.1.0
	 * @access  public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( self::$_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add a subtitle input field into the form.
	 *
	 * @since   0.1.0
	 * @access  public
	 *
	 * @param   \WP_Widget  $widget
	 * @param   null        $return
	 * @param   array       $instance
	 * @return  null
	 */
	public function in_widget_form( $widget, $return, $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'subtitle' => '',
				'subtitle_location' => '',
			)
		);

		$can_edit_location = current_user_can( $this->location_cap );
		?>

		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle' ); ?>"><?php esc_html_e( 'Subtitle', self::$_domain ); ?>:</label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'subtitle' ); ?>" name="<?php echo $widget->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['subtitle'] ) ); ?>"/>
		</p>

		<?php
		$locations = array();

		if ( $can_edit_location ) {
			$locations = $this->get_available_subtitle_locations( $widget, $instance );
		}

		if ( 1 < count( $locations ) ) {
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle_location' ); ?>"><?php esc_html_e( 'Subtitle location', self::$_domain ); ?>:</label>
			<select name="<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" id="<?php echo $widget->get_field_id( 'subtitle_location' ); ?>">
			<?php
			foreach ( (array) $locations as $location_key => $location_name ) {
				?>
				<option value="<?php echo $location_key; ?>" <?php selected( $instance['subtitle_location'], $location_key, true ); ?>>
					<?php echo $location_name; ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<?php } else { ?>
			<input type="hidden" name="<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" value="<?php echo $instance['subtitle_location']; ?>"/>
		<?php } ?>

		<script type="text/javascript">
			;( function( $ ) {
				var title = '#<?php echo $widget->get_field_id( 'title' ); ?>',
					subtitle = '#<?php echo $widget->get_field_id( 'subtitle' ); ?>',
					$title = $( title ),
					$subtitle = $( subtitle );
				<?php if ( $can_edit_location ) { ?>
				var subtitle_location = '#<?php echo $widget->get_field_id( 'subtitle_location' ); ?>',
					$subtitle_location = $( subtitle_location );

				// show/hide subtitle location input.
				if ( ! $subtitle.val() ) {
					$subtitle_location.parent().hide();
				}
				$(document).on( 'keyup', subtitle, function() {
					if ( $(this).val() ) {
						$subtitle_location.parent().slideDown('fast');
					} else {
						$subtitle_location.parent().slideUp('fast');
					}
				} );
				<?php } ?>
				// Relocate subtitle input after title if available.
				if ( $title.length && $title.parent('p').length ) {
					$subtitle.parent('p').detach().insertAfter( $title.parent('p') );
					<?php if ( $can_edit_location ) { ?>
					$subtitle_location.parent('p').detach().insertAfter( $subtitle.parent('p') );
					<?php } ?>
				}
			} ) ( jQuery );
		</script>

		<?php
		return $return;
	}

	/**
	 * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
	 *
	 * @since   0.1.0
	 * @access  public
	 *
	 * @param   array       $instance
	 * @param   array       $new_instance
	 * param   array       $old_instance
	 * param   \WP_Widget  $widget
	 * @return  array
	 */
	public function widget_update_callback( $instance, $new_instance ) {
		unset( $instance['subtitle'] );
		unset( $instance['subtitle_location'] );

		if ( ! empty( $new_instance['subtitle'] ) ) {
			$instance['subtitle'] = esc_html( strip_tags( $new_instance['subtitle'] ) );

			if ( ! empty( $new_instance['subtitle_location'] ) && array_key_exists( $new_instance['subtitle_location'], $this->locations ) ) {
				$instance['subtitle_location'] = strip_tags( $new_instance['subtitle_location'] );
			}
		}

		return $instance;
	}

	/**
	 * Gets called from within the dynamic_sidebar function which displays a widget container.
	 * This filter gets called for each widget instance in the sidebar.
	 *
	 * // Disable variable check because of global $wp_registered_widgets.
	 * @SuppressWarnings(PHPMD.LongVariables)
	 *
	 * @since   0.1.0
	 * @access  public
	 *
	 * @global  $wp_registered_widgets
	 * @param   array  $params
	 * @return  array
	 */
	public function dynamic_sidebar_params( $params ) {
		global $wp_registered_widgets;

		if ( ! isset( $params[0]['widget_id'] ) ) {
			return $params;
		}
		$widget_id = $params[0]['widget_id'];

		if ( empty( $wp_registered_widgets[ $widget_id ] ) ) {
			return $params;
		}
		$widget = $wp_registered_widgets[ $widget_id ];

		// Get instance settings.
		if ( empty( $widget['callback'][0]->option_name ) ) {
			return $params;
		}
		/** @var \WP_Widget $widget_obj */
		$widget_obj = $widget['callback'][0];
		$instance = get_option( $widget['callback'][0]->option_name );

		// Check if there's an instance of the widget.
		if ( ! array_key_exists( $params[1]['number'], $instance ) ) {
			return $params;
		}
		$instance = $instance[ $params[1]['number'] ];

		// Add the subtitle.
		if ( ! empty( $instance['subtitle'] ) ) {

			$sidebar_id = $this->get_widget_sidebar_id( $widget_id );

			// default.
			$subtitle_location = $this->default_location;
			$locations = $this->get_available_subtitle_locations( $widget_obj, $instance, $sidebar_id );

			// Get location value if it exists and is valid.
			if ( ! empty( $instance['subtitle_location'] ) && array_key_exists( $instance['subtitle_location'], $locations ) ) {
				$subtitle_location = $instance['subtitle_location'];
			}

			/**
			 * Filters subtitle element (default: span).
			 *
			 * @since  1.0.0
			 * @since  1.1.0  Add extra parameters.
			 * @since  1.1.3  Add WP_Widget instance parameter.
			 *
			 * @param  string      'span'       The HTML element.
			 * @param  string      $widget_id   The widget ID (widget name + instance number).
			 * @param  string      $sidebar_id  The sidebar ID where this widget is located.
			 * @param  array       $widget      All widget data.
			 * @param  \WP_Widget  $widget_obj  The Widget object.
			 * @return string  A valid HTML element.
			 */
			$subtitle_element = apply_filters( 'widget_subtitles_element', 'span', $widget_id, $sidebar_id, $widget, $widget_obj );

			$subtitle_classes = $this->get_subtitle_classes( $subtitle_location );
			/**
			 * Allow filter for subtitle classes to overwrite, remove or add classes.
			 *
			 * @since  1.0.0
			 * @since  1.1.0  Add extra parameters.
			 * @since  1.1.3  Add WP_Widget instance parameter.
			 *
			 * @param  array       $subtitle_classes  The default classes.
			 * @param  string      $widget_id         The widget ID (widget name + instance number).
			 * @param  string      $sidebar_id        The sidebar ID where this widget is located.
			 * @param  array       $widget            All widget data.
			 * @param  \WP_Widget  $widget_obj  The Widget object.
			 * @return array   An array of CSS classes.
			 */
			$subtitle_classes = apply_filters( 'widget_subtitles_classes', $subtitle_classes, $widget_id, $sidebar_id, $widget, $widget_obj );

			// Create class string to use.
			$subtitle_classes = is_array( $subtitle_classes ) ? '' . implode( ' ', $subtitle_classes ) . '' : '';

			// Start the output.
			$subtitle = '<' . $subtitle_element . ' class="' . $subtitle_classes . '">';
			/**
			 * Allow filter to change a widget subtitle.
			 *
			 * @since  1.2.0
			 *
			 * @param  string      $subtitle    The subtitle.
			 * @param  string      $widget_id   The widget ID (widget name + instance number).
			 * @param  string      $sidebar_id  The sidebar ID where this widget is located.
			 * @param  array       $widget      All widget data.
			 * @param  \WP_Widget  $widget_obj  The Widget object.
			 * @return string  The new subtitle.
			 */
			$subtitle .= apply_filters( 'widget_subtitle', $instance['subtitle'], $widget_id, $sidebar_id, $widget, $widget_obj );
			$subtitle .= '</' . $subtitle_element . '>';

			// Add the subtitle.
			$params[0] = $this->add_subtitle( $params[0], $subtitle, $subtitle_location, $widget, $sidebar_id );

		} // End if().

		return $params;
	}

	/**
	 * Add a subtitle in the widget parameters.
	 *
	 * @since   1.1.2
	 * @since   1.2.0  Added optional `$widget_obj` and `$sidebar_id` parameters.
	 * @access  public
	 * @param   array       $params             Widget parameters.
	 * @param   string      $subtitle           The subtitle, may contain HTML.
	 * @param   string      $subtitle_location  The subtitle location.
	 * @param   \WP_Widget  $widget_obj         The Widget object.
	 * @param   string      $sidebar_id         The sidebar ID where this widget is located.
	 * @return  array
	 */
	public function add_subtitle( $params, $subtitle, $subtitle_location = '', $widget_obj = null, $sidebar_id = '' ) {

		if ( empty( $subtitle_location ) ) {
			$subtitle_location = $this->default_location;
		}

		if ( empty( $params['before_title'] ) ) {
			$params['before_title'] = '';
		}
		if ( empty( $params['after_title'] ) ) {
			$params['after_title'] = '';
		}

		// Assign the output to the correct location in the correct order.
		switch ( $subtitle_location ) {

			case 'before-inside':
				// A space to separate subtitle from title.
				$params['before_title'] = $params['before_title'] . $subtitle . ' ';
				break;

			case 'before-outside':
				$params['before_title'] = $subtitle . $params['before_title'];
				break;

			case 'after-inside':
				// A space to separate subtitle from title.
				$params['after_title'] = ' ' . $subtitle . $params['after_title'];
				break;

			case 'after-outside':
				$params['after_title'] = $params['after_title'] . $subtitle;
				break;

			default:
				/**
				 * Add subtitle at a custom location
				 *
				 * @since  0.2.0
				 *
				 * @param  array       $params             Widget parameters.
				 * @param  string      $subtitle           The subtitle.
				 * @param  string      $subtitle_location  The selected subtitle location.
				 * @param  \WP_Widget  $widget_obj         The widget object.
				 * @param  string      $sidebar_id         The sidebar ID where this widget is located.
				 * @return array
				 */
				$params = apply_filters( 'widget_subtitles_add_subtitle', $params, $subtitle, $subtitle_location, $widget_obj, $sidebar_id );
				break;
		}

		return $params;
	}

	/**
	 * Get the sidebar ID related to a widget ID.
	 *
	 * @since   1.1.1
	 * @since   1.2.0   Make use of `wp_get_sidebars_widgets()`.
	 * @access  public
	 * @param   string  $widget_id  The widget identifier.
	 * @return  string
	 */
	public function get_widget_sidebar_id( $widget_id ) {
		//global $_wp_sidebars_widgets;
		$sidebars_widgets = wp_get_sidebars_widgets();
		$sidebar_id = '';
		if ( is_array( $sidebars_widgets ) ) {
			foreach ( $sidebars_widgets as $key => $widgets ) {
				if ( is_array( $widgets ) && in_array( (string) $widget_id, array_map( 'strval', $widgets ), true ) ) {
					// Found!
					$sidebar_id = $key;
					break;
				}
			}
		}
		return $sidebar_id;
	}

	/**
	 * Get the subtitle classes.
	 *
	 * @since   1.1.1
	 * @access  public
	 * @param   string  $location  The subtitle location.
	 * @return  array
	 */
	public function get_subtitle_classes( $location ) {
		// Create subtitle classes.
		$subtitle_classes = array( 'widget-subtitle', 'widgetsubtitle' );
		// Add subtitle location classes.
		$subtitle_classes[] = 'subtitle-' . $location;
		$location_classes = explode( '-', $location );
		foreach ( $location_classes as $location_class ) {
			$subtitle_classes[] = 'subtitle-' . $location_class;
		}
		return $subtitle_classes;
	}

	/**
	 * Get the available locations for a widget.
	 *
	 * @since   1.1.3
	 * @since   1.2.0  Optional `$sidebar_id` parameter.
	 * @param   \WP_Widget  $widget_obj
	 * @param   array       $instance
	 * @param   string      $sidebar_id  The sidebar ID where this widget is located.
	 * @return  array
	 */
	public function get_available_subtitle_locations( $widget_obj, $instance, $sidebar_id = '' ) {

		$widget_id = $widget_obj->id;
		if ( ! $sidebar_id ) {
			$sidebar_id = $this->get_widget_sidebar_id( $widget_id );
		}

		/**
		 * Filter the available locations.
		 *
		 * @since   1.1.3
		 * @since   1.2.0  Allow custom location + add `$widget_id` and `$sidebar_id` parameters.
		 *
		 * @param   array       $locations   The array of available locations.
		 * @param   \WP_Widget  $widget_obj  The widget object.
		 * @param   array       $instance    The widget instance.
		 * @param   string      $widget_id   The widget ID (widget name + instance number).
		 * @param   string      $sidebar_id  The sidebar ID where this widget is located.
		 * @return  array  $locations  The available locations: key => label.
		 */
		return (array) apply_filters( 'widget_subtitles_available_locations', $this->locations, $widget_obj, $instance, $widget_id, $sidebar_id );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @since   1.1.4
	 * @see     \WP_Plugins_List_Table::single_row()
	 * @param   array[]  $links  The existing links.
	 * @param   string   $file   The plugin file.
	 * @return  array
	 */
	public function action_plugin_row_meta( $links, $file ) {
		if ( self::$_basename === $file ) {
			foreach ( $this->get_links() as $id => $link ) {
				$icon = '<span class="dashicons ' . $link['icon'] . '" style="font-size: inherit; line-height: inherit; display: inline; vertical-align: text-top;"></span>';
				$title = $icon . ' ' . esc_html( $link['title'] );
				$links[ $id ] = '<a href="' . esc_url( $link['url'] ) . '" target="_blank">' . $title . '</a>';
			}
		}
		return $links;
	}

	/**
	 * Plugin links.
	 *
	 * @since   1.1.4
	 * @return  array[]
	 */
	public function get_links() {
		static $links;
		if ( ! empty( $links ) ) {
			return $links;
		}

		$links = array(
			'support' => array(
				'title' => __( 'Support', self::$_domain ),
				'description' => __( 'Need support?', self::$_domain ),
				'icon'  => 'dashicons-sos',
				'url'   => 'https://wordpress.org/support/plugin/widget-subtitles/',
			),
			'slack' => array(
				'title' => __( 'Slack', self::$_domain ),
				'description' => __( 'Quick help via Slack', self::$_domain ),
				'icon'  => 'dashicons-format-chat',
				'url'   => 'https://keraweb.slack.com/messages/plugin-ws/',
			),
			'review' => array(
				'title' => __( 'Review', self::$_domain ),
				'description' => __( 'Give 5 stars on WordPress.org!', self::$_domain ),
				'icon'  => 'dashicons-star-filled',
				'url'   => 'https://wordpress.org/support/plugin/widget-subtitles/reviews/',
			),
			'translate' => array(
				'title' => __( 'Translate', self::$_domain ),
				'description' => __( 'Help translating this plugin!', self::$_domain ),
				'icon'  => 'dashicons-translation',
				'url'   => 'https://translate.wordpress.org/projects/wp-plugins/widget-subtitles',
			),
			'issue' => array(
				'title' => __( 'Report issue', self::$_domain ),
				'description' => __( 'Have ideas or a bug report?', self::$_domain ),
				'icon'  => 'dashicons-lightbulb',
				'url'   => 'https://github.com/JoryHogeveen/widget-subtitles/issues',
			),
			'docs' => array(
				'title' => __( 'Documentation', self::$_domain ),
				'description' => __( 'Documentation', self::$_domain ),
				'icon'  => 'dashicons-book-alt',
				'url'   => 'https://github.com/JoryHogeveen/widget-subtitles/', //wiki
			),
			'github' => array(
				'title' => __( 'GitHub', self::$_domain ),
				'description' => __( 'Follow and/or contribute on GitHub', self::$_domain ),
				'icon'  => 'dashicons-editor-code',
				'url'   => 'https://github.com/JoryHogeveen/widget-subtitles/tree/dev',
			),
			'donate' => array(
				'title' => __( 'Donate', self::$_domain ),
				'description' => __( 'Buy me a coffee!', self::$_domain ),
				'icon'  => 'dashicons-smiley',
				'url'   => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YGPLMLU7XQ9E8&lc=NL&item_name=Widget%20Subtitles&item_number=JWPP%2dWS&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest',
			),
			'plugins' => array(
				'title' => __( 'Plugins', self::$_domain ),
				'description' => __( 'Check out my other WordPress plugins', self::$_domain ),
				'icon'  => 'dashicons-admin-plugins',
				'url'   => 'https://profiles.wordpress.org/keraweb/#content-plugins',
			),
		);

		return $links;
	}

	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  string
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  void
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to be cloned', self::$_domain ) ),
			null
		);
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  void
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to wake up', self::$_domain ) ),
			null
		);
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @param   string  $method
	 * @param   array   $args
	 * @return  null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong(
			esc_html( get_class( $this ) . "::{$method}" ),
			esc_html__( 'Method does not exist.', self::$_domain ),
			null
		);
		unset( $method, $args );
		return null;
	}

}

/**
 * Main instance of Widget Subtitle.
 *
 * Returns the main instance of Widget_Subtitles to prevent the need to use globals.
 *
 * @since   0.1.0
 * @return  \WS_Widget_Subtitles
 */
function ws_widget_subtitles() {
	return WS_Widget_Subtitles::get_instance();
}
ws_widget_subtitles();

} // End if().
