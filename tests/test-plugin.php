<?php
/**
 * Widget Subtitles - Unit tests
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Widget_Subtitles
 */


class PluginTest extends WP_UnitTestCase {

	// Check that that activation doesn't break
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( TEST_WS_PLUGIN_PATH ) );
	}

	// Check for PHP errors
	function test_general() {
		ws_widget_subtitles()->get_links();
	}

	// Check add_subtitle() method.
	function test_add_subtitle() {

		$tests = array(
			array(
				'start'  => array(),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => ' <div>SUBTITLE</div>',
					'before_title' => '',
				),
				'location' => null, // default
			),
			array(
				'start'  => array(),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '',
					'before_title' => '<div>SUBTITLE</div> ',
				),
				'location' => 'before-inside', // default
			),
			array(
				'start'  => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title">',
				),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title"><div>SUBTITLE</div> ',
				),
				'location' => 'before-inside', // default
			),
			array(
				'start'  => array(),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '',
					'before_title' => '<div>SUBTITLE</div>',
				),
				'location' => 'before-outside', // default
			),
			array(
				'start'  => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title">',
				),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '</div>',
					'before_title' => '<div>SUBTITLE</div><div class="title">',
				),
				'location' => 'before-outside', // default
			),
			array(
				'start'  => array(),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => ' <div>SUBTITLE</div>',
					'before_title' => '',
				),
				'location' => 'after-inside', // default
			),
			array(
				'start'  => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title">',
				),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => ' <div>SUBTITLE</div></div>',
					'before_title' => '<div class="title">',
				),
				'location' => 'after-inside', // default
			),
			array(
				'start'  => array(),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '<div>SUBTITLE</div>',
					'before_title' => '',
				),
				'location' => 'after-outside', // default
			),
			array(
				'start'  => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title">',
				),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '</div><div>SUBTITLE</div>',
					'before_title' => '<div class="title">',
				),
				'location' => 'after-outside', // default
			),
			array(
				'start'  => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title">',
				),
				'data'   => '<div>SUBTITLE</div>',
				'result' => array(
					'after_title' => '</div>',
					'before_title' => '<div class="title"><div>SUBTITLE</div> test ',
				),
				'location' => 'custom', // default
			),
		);

		add_filter( 'widget_subtitles_add_subtitle', array( $this, 'filter_widget_subtitles_add_subtitle' ), 10, 3 );

		foreach ( $tests as $test ) {
			$this->assertEquals(
				$test['result'],
				ws_widget_subtitles()->add_subtitle( $test['start'], $test['data'], $test['location'] )
			);
		}
	}

	function filter_widget_subtitles_add_subtitle( $params, $subtitle, $subtitle_location ) {
		if ( 'custom' === $subtitle_location ) {
			$params['before_title'] = $params['before_title'] . $subtitle . ' test ';
		}
		return $params;
	}

	// Check get_subtitle_classes() method.
	function test_get_subtitle_classes() {

		$tests = array(
			array(
				'result' => array(
					'widgetsubtitle',
					'widget-subtitle',
					'subtitle-after-outside',
					'subtitle-after',
					'subtitle-outside',
				),
				'location' => 'after-outside', // default
			),
			array(
				'result' => array(
					'widgetsubtitle',
					'widget-subtitle',
					'subtitle-custom',
				),
				'location' => 'custom', // default
			),
		);

		foreach ( $tests as $test ) {
			$this->assertEquals(
				$test['result'],
				ws_widget_subtitles()->get_subtitle_classes( $test['location'] )
			);
		}
	}

	// Check filter_widget_update_callback() method
	function test_widget_update_callback() {

		$tests = array(
			array(
				'start'  => array(),
				'data'   => array(
					'subtitle' => 'test',
					'subtitle_location' => '', // Not valid.
				),
				'result' => array(
					'subtitle' => 'test',
				),
			),
			array(
				'start'  => array(),
				'data'   => array(
					'subtitle_location' => 0, // Not valid.
				),
				'result' => array(),
			),
			// Valid data.
			array(
				'start'  => array(),
				'data'   => array(
					'subtitle' => 'subtitle',
					'subtitle_location' => 'after-inside',
				),
				'result' => array(
					'subtitle' => 'subtitle',
					'subtitle_location' => 'after-inside',
				),
			),
			// Empty data.
			array(
				'start'  => array(),
				'data'   => array(
					'subtitle' => '',
					'subtitle_location' => 0,
				),
				'result' => array(),
			),
			// Remove empty data.
			array(
				'start'  => array(
					'subtitle' => '',
					'subtitle_location' => true,
				),
				'data'   => array(),
				'result' => array(),
			),
			array(
				'start'  => array(
					'subtitle' => 'test',
					'subtitle_location' => true,
				),
				'data'   => array(
					'subtitle' => '',
				),
				'result' => array(),
			),
		);

		// Run tests
		foreach ( $tests as $test ) {
			$this->assertEquals(
				$test['result'],
				ws_widget_subtitles()->filter_widget_update_callback( $test['start'], $test['data'] )
			);
		}
	}
}
