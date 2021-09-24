<?php
/**
 * Tests Case class.
 *
 * @package SAUCAL_Test_Plugin
 */

namespace SAUCAL\Dev_Test;

use Mockery;
use WP_Mock;

/**
 * Tests for the Router class.
 */
class TestCase extends WP_Mock\Tools\TestCase {

	use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

}
