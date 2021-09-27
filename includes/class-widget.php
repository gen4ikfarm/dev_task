<?php
/**
 * Widget class file
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

use WP_Rocket\Engine\Support\Data;

/**
 * Widget Class
 */
class Widget extends \WP_Widget {

	/**
	 * Register a widget
	 */
	public function __construct() {
		parent::__construct(
			'dev_test_widget',
			__( 'Dev Test Widget', 'saucal-dev-test' ),
			array( 'description' => __( 'A SAU/CAL Widget', 'saucal-dev-test' ) )
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @see WP_Widget::widget()
	 */
	public function widget( $args, $instance ) {

		$data_fetch_class = new DataFetch();
		$data             = $data_fetch_class->get_data();
		$output           = '<ul>';
		foreach ( $data as $key => $value ) {
			$output .= '<li>' . esc_html( $key ) . ': ' . esc_html( $value ) . '</li>';
		}
		$output .= '</ul>';
		include SAUCALDEVTEST_PLUGIN_PATH . '/includes/templates/widget.php';
	}


	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @see WP_Widget::form()
	 */
	public function form( $instance ) {
		?>
		<p>
			<?php esc_html_e( 'The Widget will display data received from a remote host.', 'saucal-dev-test' ); ?>
		</p>
		<?php
	}

}
