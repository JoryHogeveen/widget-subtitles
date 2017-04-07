<?php
/**
 * Plugin Name: Widget Subtitles
 * Description: Add a customizable subtitle to your widgets
 * Plugin URI:  https://wordpress.org/plugins/widget-subtitles/
 * Version:     1.1.1
 * Author:      Jory Hogeveen
 * Author URI:  http://www.keraweb.nl
 * Text Domain: widget-subtitles
 * Domain Path: /languages
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
	 * @var	   WS_Widget_Subtitles
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
	 * Init function/action and register all used hooks
	 *
	 * @since   0.1
	 * @return  void
	 */
	public function init() {

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
				$default = array( $loc['after'] . ' - ' . $loc['inside'] );
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
	 * Load the plugin's translated strings
	 * @since  0.1
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'widget-subtitles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add a subtitle input field into the form
	 * @since  0.1
	 *
	 * @param   WP_Widget  $widget
	 * @param   null       $return
	 * @param   array      $instance
	 * @return  null
	 */
	function in_widget_form( $widget, $return, $instance ) {

		$instance = wp_parse_args( (array) $instance, array(
			'subtitle' => '',
			'subtitle_location' => '',
		) );
		?>

		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle' ) ?>"><?php esc_html_e( 'Subtitle', 'widget-subtitles' ); ?>:</label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'subtitle' ) ?>" name="<?php echo $widget->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['subtitle'] ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle_location' ) ?>"><?php esc_html_e( 'Subtitle location', 'widget-subtitles' ) ?>:</label>
			<select name="<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" id="<?php echo $widget->get_field_id( 'subtitle_location' ) ?>">
			<?php
			foreach ( (array) $this->locations as $location_key => $location_name ) {
				?>
				<option value="<?php echo $location_key ?>" <?php selected( $instance['subtitle_location'], $location_key, true ) ?>><?php echo $location_name ?></option>
				<?php
			}
			?>
			</select>
		</p>

		<script type="text/javascript">
			;(function($){
				// show/hide subtitle location input
				if ( ! $('#<?php echo $widget->get_field_id( 'subtitle' ) ?>').val() ) {
					$('#<?php echo $widget->get_field_id( 'subtitle_location' ) ?>').parent().hide();
				}
				$(document).on('keyup', '#<?php echo $widget->get_field_id( 'subtitle' ) ?>', function() {
					if ( $(this).val() ) {
						$('#<?php echo $widget->get_field_id( 'subtitle_location' ) ?>').parent().slideDown('fast');
					} else {
						$('#<?php echo $widget->get_field_id( 'subtitle_location' ) ?>').parent().slideUp('fast');
					}
				} );
				// Relocate subtitle input after title if available
				if ( $('#<?php echo $widget->get_field_id( 'title' ) ?>').parent('p').length ) {
					$('#<?php echo $widget->get_field_id( 'subtitle' ) ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'title' ) ?>').parent('p') );
					$('#<?php echo $widget->get_field_id( 'subtitle_location' ) ?>').parent('p').detach().insertAfter( $('#<?php echo $widget->get_field_id( 'subtitle' ) ?>').parent('p') );
				}
			})( jQuery );
		</script>

		<?php
		return $return;
	}

	/**
	 * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
	 *
	 * @since  0.1
	 *
	 * @param  array      $instance
	 * @param  array      $new_instance
	 * @param  array      $old_instance
	 * @param  WP_Widget  $widget
	 *
	 * @return array
	 */
	function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {
		unset( $instance['subtitle'] );
		unset( $instance['subtitle_location'] );

		if ( isset( $new_instance['subtitle'] ) ) {
			$instance['subtitle'] = esc_html( strip_tags( $new_instance['subtitle'] ) );

			if ( isset( $new_instance['subtitle_location'] ) ) {
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
	 * @since  0.1
	 *
	 * @global $wp_registered_widgets
	 * @param  array  $params
	 * @return array
	 */
	function dynamic_sidebar_params( $params ) {
		global $wp_registered_widgets;

		if ( ! isset( $params[0]['widget_id'] ) ) {
			return $params;
		}

		$widget_id = $params[0]['widget_id'];
		$widget = $wp_registered_widgets[ $widget_id ];

		// Get instance settings.
		if ( ! array_key_exists( 'callback', $widget ) ) {
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
			 */
			$subtitle_classes = apply_filters( 'widget_subtitles_classes', $subtitle_classes, $widget_id, $sidebar_id, $widget );

			// Create class string to use.
			$subtitle_classes = is_array( $subtitle_classes ) ? '' . implode( ' ', $subtitle_classes ) . '' : '';

			// Start the output
			$subtitle = '<' . $subtitle_element . ' class="' . $subtitle_classes . '">';
			$subtitle .= $instance['subtitle'];
			$subtitle .= '</' . $subtitle_element . '>';

			// Assign the output to the correct location in the correct order.
			switch ( $subtitle_location ) {

				case 'before-inside':
					// A space to separate subtitle from title.
					$params[0]['before_title'] = $params[0]['before_title'] . $subtitle . ' ';
					break;

				case 'before-outside':
					$params[0]['before_title'] = $subtitle . $params[0]['before_title'];
					break;

				case 'after-inside':
					// A space to separate subtitle from title.
					$params[0]['after_title'] = ' ' . $subtitle . $params[0]['after_title'];
					break;

				case 'after-outside':
					$params[0]['after_title'] = $params[0]['after_title'] . $subtitle;
					break;
			}
		} // End if().
		return $params;
	}

	/**
	 * Get the sidebar ID related to a widget ID.
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  string  $widget_id  The widget identifier.
	 * @return string
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
	 * @since  1.1.1
	 * @access public
	 * @param  string  $location  The subtitle location.
	 * @return array
	 */
	public function get_subtitle_classes( $location ) {
		// Create subtitle classes
		$subtitle_classes = array( 'widget-subtitle', 'widgetsubtitle' );
		// Add subtitle location classes
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
	 * @since  0.1
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  0.1
	 * @access public
	 * @return void
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
	 * @since  0.1
	 * @access public
	 * @return void
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
	 * @since  0.1
	 * @access public
	 * @param  string $method
	 * @param  array  $args
	 * @return null
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
