<?php
/**
 * PHP Unit tests bootstrap file.
 *
 * @package SAUCAL_Test_Plugin
 */

const SAUCALDEVTEST_TESTS_MAIN_FILE = __DIR__ . '/../dev-task.php';
define( 'SAUCALDEVTEST_PLUGIN_PATH', dirname( SAUCALDEVTEST_TESTS_MAIN_FILE ) );
const SAUCALDEVTEST_PLUGIN_URL = SAUCALDEVTEST_PLUGIN_PATH;

if ( ! defined( 'SAUCALDEVTEST_VERSION' ) ) {
	define( 'SAUCALDEVTEST_VERSION', '0.1' );
}

require_once SAUCALDEVTEST_PLUGIN_PATH . '/vendor/autoload.php';


tad\FunctionMocker\FunctionMocker::init(
	[
		'blacklist' => [
			realpath( SAUCALDEVTEST_PLUGIN_PATH ),
		],
		'whitelist' => [
			realpath( SAUCALDEVTEST_PLUGIN_PATH . '/includes' ),
		],
		'redefinable-internals' => [
			'class_exists',
			'define',
			'defined',
			'constant',
			'file_exists',
			'filter_input',
			'time',
		],
	]
);

\WP_Mock::bootstrap();
