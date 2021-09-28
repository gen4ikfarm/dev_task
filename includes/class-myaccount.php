<?php
/**
 * Widget class file
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

/**
 * Widget Class
 */
class MyAccount {

	const TEMPLATE_FILE_NAME = 'template-saucal-my-account.php';

	const SCRIPT_HANDLE = 'saucal-test-script';

	const LOCALIZATION_OPTION = 'SauCalOptions';

	/**
	 * Class DataFetch
	 *
	 * @var object DataFetch
	 */
	public $data_fetch_class;


	/**
	 * MyAccount construct
	 */
	public function __construct() {

		$this->data_fetch_class = new DataFetch();
		add_action( 'init', array( $this, 'account_custom_tab_endpoint' ), 0 );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'account_custom_tab_query_vars' ), 0 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'account_custom_tab' ), 0 );
		add_action( 'woocommerce_account_user-headers_endpoint', array( $this, 'account_custom_tab_content' ) );
		add_filter( 'the_title', array( $this, 'account_custom_tab_title' ) );
	}


	/**
	 * Adds a title for the User Headers tab
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 */
	public function account_custom_tab_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars['user-headers'] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			$title = esc_html__( 'User Headers', 'saucal-dev-test' );
			remove_filter( 'the_title', array( $this, 'account_custom_tab_title' ) );
		}

		return $title;
	}


	/**
	 * Generates a content for the User Headers tab
	 *
	 * @param bool $force Reload data from the remote host.
	 */
	public function account_custom_tab_content( $force = false ) {
		$data = $this->data_fetch_class->get_data( $force );

		$output = $this->generate_user_settings_form();

		$output .= '<ul>';
		foreach ( $data as $key => $value ) {
			$output .= '<li>' . esc_html( $key ) . ': ' . esc_html( $value ) . '</li>';
		}
		$output .= '</ul>';

		if ( file_exists( get_template_directory() . '/' . self::TEMPLATE_FILE_NAME ) ) {
			include get_template_directory() . '/' . self::TEMPLATE_FILE_NAME;
		} else {
			include SAUCALDEVTEST_PLUGIN_PATH . '/includes/templates/' . self::TEMPLATE_FILE_NAME;
		}
		$this->enqueue_scripts();
	}

	/**
	 * Generates localization data array
	 */
	public function generate_localization_data() {
		$data = array(
			'string_array'  => array(
				'no_data'    => esc_attr__( 'Can not get remote data, please try again later.', 'saucal-dev-test' ),
				'wrong_data' => esc_attr__( 'Wrong data received, please try again later.', 'saucal-dev-test' ),
			),
			'restUrlNormal' => rest_url() . Bootstrap::ROUTE_NAMESPACE . '/' . Bootstrap::ROUTE_NORMAL,
			'nonce'         => wp_create_nonce( 'wp_rest' ),
		);

		wp_localize_script( self::SCRIPT_HANDLE, self::LOCALIZATION_OPTION, $data );
	}


	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css', '', '5.5.0' );
		wp_register_script(
			self::SCRIPT_HANDLE,
			SAUCALDEVTEST_PLUGIN_URL . '/assets/js/front-end.js',
			array( 'jquery' ),
			1.0,
			true
		);
		$this->generate_localization_data();
		wp_enqueue_script( self::SCRIPT_HANDLE );
	}


	/**
	 * Generates the user form
	 *
	 * @return string
	 */
	public function generate_user_settings_form() {
		$settings      = $this->get_user_options_array();
		$user_settings = $this->get_user_settings();
		$output        = '<form id="saucal-user-settings-form">';
		foreach ( $settings as $setting_key => $setting ) {
			$output .= '<p>' . $setting['title'] . ': <select name="' . esc_attr( $setting_key ) . '">';

			foreach ( $setting['data'] as $option_key => $option_value ) {
				$output .= '<option value="'
					. esc_attr( $option_key )
					. '" '
					. selected( $option_key, $user_settings[ $setting_key ], false )
					. '>'
					. $option_value
					. '</option>';
			}
			$output .= '</select></p>';
		}
		$output .= '<p><input type="submit" value="' . esc_attr__( 'Save', 'saucal-dev-test' ) . '"></p>';
		$output .= '</form>';

		return $output;
	}


	/**
	 * Returns the current user saved options
	 *
	 * @return array
	 */
	public function get_user_settings() {
		$defaults = array(
			'tags'  => '',
			'genre' => '',
		);
		$settings = get_user_meta( $this->data_fetch_class->user_id, $this->data_fetch_class::USER_META_OPTION_KEY, true );

		return wp_parse_args( $settings, $defaults );
	}


	/**
	 * The My Account page user options array
	 *
	 * @return array[]
	 */
	public function get_user_options_array() {
		return array(
			'tags'  => array(
				'title' => esc_html__( 'Tags', 'saucal-dev-test' ),
				'data'  => array(
					''          => esc_html__( 'None', 'saucal-dev-test' ),
					'popular'   => esc_html__( 'Popular', 'saucal-dev-test' ),
					'recent'    => esc_html__( 'Recent', 'saucal-dev-test' ),
					'top_rated' => esc_html__( 'Top rated', 'saucal-dev-test' ),
				),
			),
			'genre' => array(
				'title' => esc_html__( 'Genre', 'saucal-dev-test' ),
				'data'  => array(
					''          => esc_html__( 'None', 'saucal-dev-test' ),
					'crime'     => esc_html__( 'Crime', 'saucal-dev-test' ),
					'detective' => esc_html__( 'Detective', 'saucal-dev-test' ),
					'fantasy'   => esc_html__( 'Fantasy', 'saucal-dev-test' ),
				),
			),
		);
	}


	/**
	 * Adds a query vars for the User Headers tab
	 *
	 * @param array $vars Array of query vars.
	 *
	 * @return array
	 */
	public function account_custom_tab_query_vars( $vars ) {
		$vars['user-headers'] = 'user-headers';

		return $vars;
	}


	/**
	 * Adds a end-point for the User Headers tab
	 */
	public function account_custom_tab_endpoint() {
		add_rewrite_endpoint( 'user-headers', EP_ROOT | EP_PAGES );
	}


	/**
	 * Adds a custom tab to the My account page
	 *
	 * @param array $items Array of My Account tabs.
	 *
	 * @return array
	 */
	public function account_custom_tab( $items ) {
		return array_merge(
			array_slice( $items, 0, 1 ),
			array( 'user-headers' => esc_html__( 'User Headers', 'saucal-dev-test' ) ),
			array_slice( $items, 1, null )
		);
	}


}
