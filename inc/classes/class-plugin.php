<?php
/**
 * Plugin manifest class
 *
 * @package linkrel-defender
 */

namespace Linkrel_Defender\Inc;

use Linkrel_Defender\Inc\Traits\Singleton;

/**
 * Main plugin class
 */
class Plugin {

	use Singleton;

	/**
	 * Construct method
	 */
	protected function __construct() {

		Settings::get_instance();
        Link_Processor::get_instance();
	}
}
