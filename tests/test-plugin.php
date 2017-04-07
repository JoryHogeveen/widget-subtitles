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
}
