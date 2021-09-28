<?php
/**
 * Tests Case class.
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

/**
 * DataFetch tests.
 *
 * @group API
 */
class TestDataFetch extends TestCase {

	/**
	 * Checks if valid data received
	 *
	 * @test
	 */
	public function itChecksDataCaching() {
		\WP_Mock::userFunction(
			'get_current_user_id',
			array(
				'times' => 1,
				'return' => '1',
			)
		);

		$subject = new DataFetch();

		$data = '{"args":{"genre":"crime","tags":"recent","user":"1"},"data":"","files":{},"form":{},"headers":{"Accept":"*/*","Accept-Encoding":"deflate, gzip, br","Content-Length":"0","Content-Type":"application/x-www-form-urlencoded","Host":"httpbin.org","Referer":"https://httpbin.org/post?user=1&tags=recent&genre=crime","User-Agent":"WordPress/5.8.1; http://my.com","X-Amzn-Trace-Id":"Root=1-6152e232-37a103661e0411d718c00347"},"json":null,"origin":"188.138.167.245","url":"https://httpbin.org/post?user=1&tags=recent&genre=crime"}';

		$data = array(
			'body' => $data,
			'response' => array( 'code' => 200 ),
		);

		\WP_Mock::userFunction(
			'add_query_arg',
			array(
				'return' => 'http://www.test.com/?params=1',
			)
		);

		\WP_Mock::userFunction(
			'get_user_meta',
			array(
				'return' => [ 'tags' => 0, 'genre' => '' ],
			)
		);

		\WP_Mock::userFunction(
			'get_transient',
			array(
				'return' => false,
			)
		);

		\WP_Mock::userFunction(
			'wp_remote_get',
			array(
				'return' => $data,
			)
		);

		\WP_Mock::userFunction(
			'is_wp_error',
			array(
				'return' => false,
			)
		);

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => 'test',
			)
		);

		\WP_Mock::userFunction(
			'set_transient',
			array(
				'return' => true,
			)
		);

		$result = $subject->get_data();

		$this->assertIsArray( $result );

	}


}
