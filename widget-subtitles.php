<?php
/**
 * Plugin Name: Widget Subtitles
 * Description: Add a customizable subtitle to your widgets
 * Plugin URI: http://github.com/JoryHogeveen/widget-subtitles
 * Version: 1.0
 * Author: Jory Hogeveen
 * Author URI: http://www.keraweb.nl
 * Text Domain: widget-subtitles
 * Domain Path: /languages
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

! defined( 'ABSPATH' ) and die();

if ( ! class_exists( 'Widget_Subtitles' ) ) :

final class Widget_Subtitles {

	/**
	 * The single instance of the class.
	 *
	 * @since	0.1
	 * @var		Widget_Subtitles
	 */
	private static $_instance = null;

	/**
	 * Plugin version
	 *
	 * @since	0.1
	 * @var 	string
	 */
	private $version = '1.0';

	/**
	 * Possible locations of the subtitle
	 *
	 * @since	0.1
	 * @var 	string
	 */
	private $locations = array();
	
	/**
	 * PHP5 constructor that calls specific hooks within WordPress
	 *
	 * @since	0.1
	 * @return	void
	 */
	function __construct( ) {
		self::$_instance = $this;

		$this->locations = array(
			'before-outside' => __('Before title', 'widget-subtitles') . ' - ' . __('Outside heading', 'widget-subtitles'), // before title, outside title element
			'before-inside' => __('Before title', 'widget-subtitles') . ' - ' . __('Inside heading', 'widget-subtitles'), // before title, inside title element
			'after-outside' => __('After title', 'widget-subtitles') . ' - ' . __('Outside heading', 'widget-subtitles'), // after title, outside title element
			'after-inside' => __('After title', 'widget-subtitles') . ' - ' . __('Inside heading', 'widget-subtitles'), // after title, inside title element
		);

		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	 * Main Genesis Widget Subtitles.
	 *
	 * Ensures only one instance of Widget Subtitle is loaded or can be loaded.
	 *
	 * @since	0.1
	 * @static
	 * @see		Widget_Subtitles()
	 * @return	Genesis Widget Column Classes - Main instance.
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
	 * @since	0.1
	 * @return	void
	 */
	public function init() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'in_widget_form', array( $this, 'in_widget_form'), 9, 3 );
		add_filter( 'widget_update_callback', array( $this, 'widget_update_callback' ), 10, 4 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) );
	}

	/**
	 * Load the plugin's translated strings
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'widget-subtitles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

	/**
     * Add a subtitle input field into the form
     */
	function in_widget_form( $widget, $return, $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'subtitle' => '', 'subtitle_location' => '' ) );
		$return = null;
		?>

		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle' ) ?>"><?php _e( 'Subtitle', 'widget-subtitles' ); ?>:</label> 
			<input class="widefat" id="<?php echo $widget->get_field_id( 'subtitle' ) ?>" name="<?php echo $widget->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $instance['subtitle'] ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $widget->get_field_id( 'subtitle_location' ) ?>"><?php _e('Subtitle location', 'widget-subtitles') ?>:</label> 
			<select name=<?php echo $widget->get_field_name( 'subtitle_location' ); ?>" id="<?php echo $widget->get_field_id( 'subtitle_location' ) ?>">
			<?php 
				foreach ( $this->locations as $locationKey => $locationName ) {
					?>
					<option value="<?php echo $locationKey ?>" <?php selected( $instance['subtitle_location'], $locationKey, true ) ?>><?php echo $locationName ?></option>
					<?php
				}
			?>
			</select> 
		</p>

        <script type="text/javascript">
			;(function($){ 
				// show/hide subtitla location if no value is 
				if ( '' == $('#<?php echo $widget->get_field_id( 'subtitle' ) ?>').val() ) {
					$('#<?php echo $widget->get_field_id( 'subtitle_location' ) ?>').parent().hide();
				}
				$(document).on('keyup', '#<?php echo $widget->get_field_id( 'subtitle' ) ?>', function() {
					if ( '' != $(this).val() ) {
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
	}

	/**
     * Filter the widget’s settings before saving, return false to cancel saving (keep the old settings if updating).
     */
	function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {

		// Subtitle
		if ( isset( $new_instance['subtitle'] ) ) {
			$instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
		} else { 
			$instance['subtitle'] = ''; 
		}

		// Subtitle location
		if ( isset( $new_instance['subtitle_location'] ) ) {
			$instance['subtitle_location'] = strip_tags( $new_instance['subtitle_location'] );
		} else { 
			$instance['subtitle_location'] = ''; 
		}

		return $instance;

	}
	
	/**
     * Gets called from within the dynamic_sidebar function which displays a widget container.
     * This filter gets called for each widget instance in the sidebar.
     */
	function dynamic_sidebar_params( $params ) {

		global $wp_registered_widgets;

		if ( ! isset( $params[0]['widget_id'] ) ) {
			return $params;
		}

		$widget_id = $params[0]['widget_id'];
		$widget = $wp_registered_widgets[ $widget_id ];
	
		// Get instance settings
		if ( array_key_exists( 'callback', $widget ) ) {

			$instance = get_option( $widget['callback'][0]->option_name );
		
			// Check if there's an instance of the widget
			if ( array_key_exists( $params[1]['number'], $instance ) ) {

				$instance = $instance[$params[1]['number']];
			
				// Add the subtitle
				if ( ! empty( $instance['subtitle'] ) ) {

					$subtitle_location = 'after-inside'; // default
					// Get location value if it exists and is valid
					if ( ! empty( $instance['subtitle_location'] ) && array_key_exists( $instance['subtitle_location'], $this->locations ) ) {
						$subtitle_location = $instance['subtitle_location'];
					}

					// Filters subtitle element (default: span)
					$subtitle_element = apply_filters( 'widget_subtitles_element', 'span', $widget_id );

					// Create subtitle classes
					$subtitle_classes = array( 'widget-subtitles', 'widgetsubtitle' );
					// Add subtitle location classes
					$subtitle_classes[] = 'subtitle-' . $subtitle_location;
					$subtitle_location_classes = explode( '-', $subtitle_location );
					foreach( $subtitle_location_classes as $location ) {
						$subtitle_classes[] = 'subtitle-' . $location;
					}
					// Allow filter for subtitle classes to overwrite, remove or add classes
					$subtitle_classes = apply_filters( 'widget_subtitles_classes', $subtitle_classes, $widget_id );
					// Create class string to use
					$subtitle_classes = is_array( $subtitle_classes ) ? '' . implode( ' ', $subtitle_classes ) . '' : '';

					// Start the output
					$subtitle = '<' . $subtitle_element . ' class="' . $subtitle_classes . '">';
					$subtitle .= $instance['subtitle'];
					$subtitle .= '</' . $subtitle_element . '>';

					// Assign the output to the correct location in the correct order
					switch ( $subtitle_location ) {

						case 'before-inside':
							$params[0]['before_title'] = $params[0]['before_title'] . $subtitle . ' '; // a space to separate subtitle from title
							break;

						case 'before-outside':
							$params[0]['before_title'] = $subtitle . $params[0]['before_title'];
							break;

						case 'after-inside':
							$params[0]['after_title'] = ' ' . $subtitle . $params[0]['after_title']; // a space to separate subtitle from title
							break;

						case 'after-outside':
							$params[0]['after_title'] = $params[0]['after_title'] . $subtitle;
							break;
					}

				}
			}
		}
		return $params;
	}
	
	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since  0.1
	 * @access public
	 * @return void
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
			get_class( $this ) . ': ' . esc_html__( 'This class does not want to be cloned', 'view-admin-as' ),
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
			get_class( $this ) . ': ' . esc_html__( 'This class does not want to wake up', 'view-admin-as' ),
			null
		);
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  0.1
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong(
			get_class( $this ) . "::{$method}",
			esc_html__( 'Method does not exist.', 'view-admin-as' ),
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
 * @since	0.1
 * @return 	Widget_Subtitles
 */
function Get_Widget_Subtitles() {
	return Widget_Subtitles::get_instance();
}
Get_Widget_Subtitles();

endif; // if ( ! class_exists( 'Widget_Subtitles' ) )
