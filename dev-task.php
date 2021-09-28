<?php
/**
 * SAU/CAL plugin development challenge.
 *
 * @package SAUCAL_Test_Plugin
 * @author  Ghennadi Iolchin
 *
 * Plugin Name: SAU/CAL Test
 * Plugin URI:
 * Description: SAU/CAL Dev Test
 * Author: Ghennadi Iolchin
 * Author URI: https://saucal.com
 * Version: 0.1
 * Plugin Slug: saucal-dev-test
 */

defined( 'ABSPATH' ) || die( esc_html( __( 'Direct script access disallowed.', 'saucal-dev-test' ) ) );

if ( defined( 'SAUCALDEVTEST_VERSION' ) ) {
	return;
}

const SAUCALDEVTEST_VERSION = '0.1';
define( 'SAUCALDEVTEST_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'SAUCALDEVTEST_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
const SAUCALDEVTEST_LOCALE_PATH = SAUCALDEVTEST_PLUGIN_PATH . '/locale';

if ( ! file_exists( SAUCALDEVTEST_PLUGIN_PATH . '/vendor/autoload.php' ) ) {
	return;
}

require_once SAUCALDEVTEST_PLUGIN_PATH . '/vendor/autoload.php';

/**
 * Plugin main loader
 */
function saucaldevtest_loader() {
	$bootstrap = new \SAUCAL\Dev_Test\Bootstrap();
	$bootstrap->init();

}
add_action( 'plugins_loaded', 'saucaldevtest_loader' );
if ( ! function_exists( 'saucal_flush_rewrite_rules' ) ) {
	/**
	 * Flush WordPress rewrite rules.
	 */
	function my_custom_flush_rewrite_rules() {
		add_rewrite_endpoint( 'my-custom-endpoint', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
}

register_activation_hook( __FILE__, 'saucal_flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'saucal_custom_flush_rewrite_rules' );
