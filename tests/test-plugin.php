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
		$ws = ws_widget_subtitles();
		$ws->get_links();
	}

	// Check add_subtitle() method.
	function test_add_subtitle() {
		$ws = ws_widget_subtitles();

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
		);

		foreach ( $tests as $test ) {
			$this->assertEquals( $test['result'], $ws->add_subtitle( $test['start'], $test['data'], $test['location'] ) );
		}
	}

	// Check widget_update_callback() method
	function test_widget_update_callback() {
		$ws = ws_widget_subtitles();

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
			$this->assertEquals( $test['result'], $ws->filter_widget_update_callback( $test['start'], $test['data'] ) );
		}
	}
}
