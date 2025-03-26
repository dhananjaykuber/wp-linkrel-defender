<?php
/**
 * Plugin Name:       LinkRel Defender
 * Description:       Protect your site from malicious links.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Dhananjay Kuber
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       linkrel-defender
 *
 * @package          linkrel-defender
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LINKREL_DEFENDER_VERSION', '0.1.0' );
define( 'LINKREL_DEFENDER_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'LINKREL_DEFENDER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once LINKREL_DEFENDER_PATH . '/inc/helpers/autoloader.php';

/**
 * Main instance of LinkRel_Defender\Inc\Plugin.
 */
function linkrel_defender() {
	\Linkrel_Defender\Inc\Plugin::get_instance();
}

add_action( 'plugins_loaded', 'linkrel_defender' );
