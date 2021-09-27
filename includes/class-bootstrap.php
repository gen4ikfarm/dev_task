<?php
/**
 * Bootstrap class file
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

/**
 * Bootstrap class
 */
class Bootstrap {

	const STYLES_HANDLE = 'saucal-test-style';

	/**
	 * Plugin init
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}


	/**
	 * Register a widget
	 */
	public function register_widget() {
		register_widget( 'SAUCAL\Dev_Test\Widget' );
	}


	/**
	 * Register and enqueue styles
	 */
	public function enqueue_styles() {

		wp_register_style(
			self::STYLES_HANDLE,
			SAUCALDEVTEST_PLUGIN_URL
			. '/assets/css/front-end.css',
			array(),
			SAUCALDEVTEST_VERSION
		);
		wp_enqueue_style( self::STYLES_HANDLE );
	}
}
