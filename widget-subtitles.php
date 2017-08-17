<?php
/**
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Widget_Subtitles
 * @since   0.1
 * @version 1.1.2
 * @licence GPL-2.0+
 * @link    https://github.com/JoryHogeveen/widget-subtitles
 *
 * @wordpress-plugin
 * Plugin Name:       Widget Subtitles
 * Plugin URI:        https://wordpress.org/plugins/widget-subtitles/
 * Description:       Add a customizable subtitle to your widgets
 * Version:           1.1.2
 * Author:            Jory Hogeveen
 * Author URI:        http://www.keraweb.nl
 * Text Domain:       widget-subtitles
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/JoryHogeveen/widget-subtitles
 *
 * @copyright 2015-2017 Jory Hogeveen
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
 * @since   0.1
 * @version 1.1.2
 */
final class WS_Widget_Subtitles {

	/**
	 * The single instance of the class.
	 *
	 * @since  0.1
	 * @var    WS_Widget_Subtitles
	 */
	private static $_instance = null;

	/**
	 * Possible locations of the subtitle.
	 *
	 * @since  0.1
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
	 * @since   0.1
	 * @access  private
	 */
	private function __construct() {
		self::$_instance = $this;

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Main Genesis Widget Subtitles.
	 *
	 * Ensures only one instance of Widget Subtitle is loaded or can be loaded.
	 *
	 * @since   0.1
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
	 * @since   0.1
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

		$loc['before']  = __( 'Before title', 'widget-subtitles' );
		$loc['after']   = __( 'After title', 'widget-subtitles' );
		$loc['outside'] = __( 'Outside heading', 'widget-subtitles' );
		$loc['inside']  = __( 'Inside heading', 'widget-subtitles' );

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
			'' => __( 'Default', 'widget-subtitles' ) . ' (' . $default . ')',
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
	}

	/**
	 * Load the plugin's translated strings.
	 * @since   0.1
	 * @access  public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'widget-subtitles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add a subtitle input field into the form.
	 *
	 * @since   0.1
	 * @access  public
	 *
	 * @param   WP_Widget  $widget
	 * @param   null       $return
	 * @param   array      $instance
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
			<label for="<?php echo $widget->get_field_id( 'subtitle' ); ?>"><?php esc_html_e( 'Subtitle', 'widget-subtitles' ); ?>:</label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'subtitle' ); ?>" name="<?php echo $widget->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['subtitle'] ) ); ?>"/>
		</p>

		<?php if ( $can_edit_location ) { ?>
		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle_location' ); ?>"><?php esc_html_e( 'Subtitle location', 'widget-subtitles' ); ?>:</label>
			<select name="<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" id="<?php echo $widget->get_field_id( 'subtitle_location' ); ?>">
			<?php
			foreach ( (array) $this->locations as $location_key => $location_name ) {
				?>
				<option value="<?php echo $location_key; ?>" <?php selected( $instance['subtitle_location'], $location_key, true ); ?>><?php echo $location_name; ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<?php } else { ?>
			<input type="hidden" name="<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" value="<?php echo $instance['subtitle_location']; ?>"/>
		<?php } ?>

		<script type="text/javascript">
			;(function($){
				<?php if ( $can_edit_location ) { ?>
				// show/hide subtitle location input.
				if ( ! $('#<?php echo $widget->get_field_id( 'subtitle' ); ?>').val() ) {
					$('#<?php echo $widget->get_field_id( 'subtitle_location' ); ?>').parent().hide();
				}
				$(document).on( 'keyup', '#<?php echo $widget->get_field_id( 'subtitle' ); ?>', function() {
					if ( $(this).val() ) {
						$('#<?php echo $widget->get_field_id( 'subtitle_location' ); ?>').parent().slideDown('fast');
					} else {
						$('#<?php echo $widget->get_field_id( 'subtitle_location' ); ?>').parent().slideUp('fast');
					}
				} );
				<?php } ?>
				// Relocate subtitle input after title if available.
				if ( $('#<?php echo $widget->get_field_id( 'title' ); ?>').parent('p').length ) {
					$('#<?php echo $widget->get_field_id( 'subtitle' ); ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'title' ); ?>').parent('p') );
					<?php if ( $can_edit_location ) { ?>
					$('#<?php echo $widget->get_field_id( 'subtitle_location' ); ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'subtitle' ); ?>').parent('p') );
					<?php } ?>
				}
			})( jQuery );
		</script>

		<?php
		return $return;
	}

	/**
	 * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
	 *
	 * @since   0.1
	 * @access  public
	 *
	 * @param   array      $instance
	 * @param   array      $new_instance
	 * param   array      $old_instance
	 * param   WP_Widget  $widget
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
	 * @since   0.1
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
			// Get location value if it exists and is valid.
			if ( ! empty( $instance['subtitle_location'] ) && array_key_exists( $instance['subtitle_location'], $this->locations ) ) {
				$subtitle_location = $instance['subtitle_location'];
			}

			/**
			 * Filters subtitle element (default: span).
			 *
			 * @since  1.0
			 * @since  1.1  Add extra parameters.
			 *
			 * @param  string  'span'       The HTML element.
			 * @param  string  $widget_id   The widget ID (widget name + instance number).
			 * @param  string  $sidebar_id  The sidebar ID where this widget is located.
			 * @param  array   $widget      All widget data.
			 * @return string  A valid HTML element.
			 */
			$subtitle_element = apply_filters( 'widget_subtitles_element', 'span', $widget_id, $sidebar_id, $widget );

			$subtitle_classes = $this->get_subtitle_classes( $subtitle_location );
			/**
			 * Allow filter for subtitle classes to overwrite, remove or add classes.
			 *
			 * @since  1.0
			 * @since  1.1  Add extra parameters.
			 *
			 * @param  array   $subtitle_classes  The default classes.
			 * @param  string  $widget_id         The widget ID (widget name + instance number).
			 * @param  string  $sidebar_id        The sidebar ID where this widget is located.
			 * @param  array   $widget            All widget data.
			 * @return array   An array of CSS classes.
			 */
			$subtitle_classes = apply_filters( 'widget_subtitles_classes', $subtitle_classes, $widget_id, $sidebar_id, $widget );

			// Create class string to use.
			$subtitle_classes = is_array( $subtitle_classes ) ? '' . implode( ' ', $subtitle_classes ) . '' : '';

			// Start the output.
			$subtitle = '<' . $subtitle_element . ' class="' . $subtitle_classes . '">';
			$subtitle .= $instance['subtitle'];
			$subtitle .= '</' . $subtitle_element . '>';

			// Add the subtitle.
			$params[0] = $this->add_subtitle( $params[0], $subtitle, $subtitle_location );

		} // End if().

		return $params;
	}

	/**
	 * Add a subtitle in the widget parameters.
	 *
	 * @since   1.1.2
	 * @access  public
	 * @param   array   $params             Widget parameters.
	 * @param   string  $subtitle           The subtitle, may contain HTML.
	 * @param   string  $subtitle_location  The subtitle location.
	 * @return  array
	 */
	public function add_subtitle( $params, $subtitle, $subtitle_location = '' ) {

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
		}

		return $params;
	}

	/**
	 * Get the sidebar ID related to a widget ID.
	 *
	 * @since   1.1.1
	 * @access  public
	 * @param   string  $widget_id  The widget identifier.
	 * @return  string
	 */
	public function get_widget_sidebar_id( $widget_id ) {
		global $_wp_sidebars_widgets;
		$sidebar_id = '';
		if ( is_array( $_wp_sidebars_widgets ) ) {
			foreach ( $_wp_sidebars_widgets as $key => $widgets ) {
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
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since   0.1
	 * @access  public
	 * @return  string
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since   0.1
	 * @access  public
	 * @return  void
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to be cloned', 'widget-subtitles' ) ),
			null
		);
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since   0.1
	 * @access  public
	 * @return  void
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to wake up', 'widget-subtitles' ) ),
			null
		);
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since   0.1
	 * @access  public
	 * @param   string $method
	 * @param   array  $args
	 * @return  null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong(
			esc_html( get_class( $this ) . "::{$method}" ),
			esc_html__( 'Method does not exist.', 'widget-subtitles' ),
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
 * @since   0.1
 * @return  WS_Widget_Subtitles
 */
function ws_widget_subtitles() {
	return WS_Widget_Subtitles::get_instance();
}
ws_widget_subtitles();

} // End if().
