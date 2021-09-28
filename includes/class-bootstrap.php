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

	const ROUTE_NAMESPACE = 'saucal_dev_test/v1';

	const ROUTE_NORMAL = 'put-data/';

	/**
	 * MyAccount class
	 *
	 * @var object MyAccount
	 */
	private $my_account_class;

	/**
	 * Plugin init
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		$this->my_account_class = new MyAccount();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Registers REST API Routes
	 */
	public function register_route() {

		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/' . self::ROUTE_NORMAL,
			array(
				'method'              => 'GET',
				'callback'            => array( $this, 'save_options_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Route Callback
	 *
	 * @param object $request WP_REST_Request.
	 *
	 * @return \WP_REST_Response
	 */
	public function save_options_callback( $request ) {
		$data = $this->sanitize_array_values( $request->get_params() );
		update_user_meta( $this->my_account_class->data_fetch_class->user_id, $this->my_account_class->data_fetch_class::USER_META_OPTION_KEY, $data );
		ob_start();
		$this->my_account_class->account_custom_tab_content( true );
		$output = ob_get_contents();
		ob_end_clean();

		return new \WP_REST_Response( $output );
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

	/**
	 * Recursively sanitize array values
	 *
	 * @param array $array Force data reload param.
	 *
	 * @return array
	 */
	public function sanitize_array_values( $array ) {

		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->sanitize_array_values( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $array;
	}
}
