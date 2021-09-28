<?php
/**
 * DataFetch class file
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

/**
 * Gets data from remote host
 */
class DataFetch {

	const API_URI = 'https://httpbin.org/post';

	const TRANSIENT_OPTION_NAME_PREFIX = 'saucal-stored-data';

	const TRANSIENT_EXPIRATION = 3600;

	const USER_META_OPTION_KEY = 'saucal-user-option';

	/**
	 * The current user ID
	 *
	 * @var int Current User ID
	 */
	public $user_id;


	/**
	 * DataFetch Construct
	 */
	public function __construct() {
		$this->user_id = get_current_user_id();
	}


	/**
	 *  Load data array
	 *
	 * @param boolean $force Force data reload param.
	 *
	 * @return array
	 */
	public function get_data( $force = false ) {
		$data = false;

		$transient_name = self::TRANSIENT_OPTION_NAME_PREFIX . $this->user_id;

		if ( ! $force ) {
			$data = get_transient( $transient_name );
		}

		if ( ! $data || $force ) {
			$data = $this->get_fresh_data();

			if ( is_array( $data ) ) {
				$data = $this->sanitize_array_values( $data );
				if ( ! isset( $data['error'] ) ) {
					set_transient( $transient_name, $data, self::TRANSIENT_EXPIRATION );
				}
			}
		}

		return $data;
	}


	/**
	 * Fetch data from a remote host
	 *
	 * @return array|mixed
	 */
	public function get_fresh_data() {

		$url          = add_query_arg( 'user', $this->user_id, self::API_URI );
		$user_options = $this->get_user_options();
		if ( ! empty( $user_options ) ) {
			$url = add_query_arg( $user_options, $url );
		}
		$data = wp_remote_get( $url, [ 'method' => 'POST' ] );
		if ( is_wp_error( $data ) ) {
			return array( 'error' => esc_html( $data->get_error_message() ) );
		}

		if ( ! array_key_exists( 'body', $data ) ) {
			return array( 'error' => esc_html__( 'Can not receive data from the remote server, please try again later.', 'saucal-dev-test' ) );
		}

		$data_array = json_decode( $data['body'], JSON_OBJECT_AS_ARRAY );
		if ( 200 !== $data['response']['code'] && isset( $data_array['error'] ) ) {
			return array( 'error' => esc_html( $data['response']['code'] . ': ' . $data_array['message'] ) );
		}
		if ( array_key_exists( 'headers', $data_array ) ) {
			return $data_array['headers'];
		}
	}


	/**
	 *  Get current user option for a remote query.
	 *
	 * @return null|string
	 */
	public function get_user_options() {
		return get_user_meta( $this->user_id, self::USER_META_OPTION_KEY, true );
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
