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
}
