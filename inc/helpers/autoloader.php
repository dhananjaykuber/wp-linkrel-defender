<?php
/**
 * Autoloader file for plugin.
 *
 * @package Linkrel_Defender
 */

namespace Linkrel_Defender\Inc\Helpers;

/**
 * Auto loader function.
 *
 * @param string $resource Source namespace.
 *
 * @return void
 */
function autoloader(
	// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.resourceFound
	$resource = ''
) {

	$resource_path  = false;
	$namespace_root = 'Linkrel_Defender\\';
	$resource       = trim( $resource, '\\' );

	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource ) )
	);

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if ( empty( $path[0] ) || empty( $path[1] ) ) {
		return;
	}

	$directory = '';
	$file_name = '';

	if ( 'inc' === $path[0] ) {

		switch ( $path[1] ) {
			case 'traits':
				$directory = 'traits';
				$file_name = sprintf( 'trait-%s', trim( strtolower( $path[2] ) ) );
				break;

			default:
				$directory = 'classes';
				$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
				break;
		}

		$resource_path = sprintf( '%s/inc/%s/%s.php', untrailingslashit( LINKREL_DEFENDER_PATH ), $directory, $file_name );

	}

	if ( 'tests' === $path[0] ) {

		switch ( $path[1] ) {
			case 'testcase':
				$directory = 'php';
				$file_name = 'testcase';
				break;
			default:
				break;
		}

		// Create resource path for `TestCase` class as it is not in `tests/php/inc` directory.
		$resource_path = sprintf( '%s/tests/php/%s.php', untrailingslashit( LINKREL_DEFENDER_PATH ), $file_name );

	}

	$resource_path_valid = validate_file( $resource_path );
	// For Windows platform, validate_file returns 2 so we've added this condition as well.
	if ( ! empty( $resource_path ) && file_exists( $resource_path ) && ( 0 === $resource_path_valid || 2 === $resource_path_valid ) ) {
		// We are already making sure that the file exists and it's valid.
		require_once( $resource_path ); // phpcs:ignore
	}
}

spl_autoload_register( '\Linkrel_Defender\Inc\Helpers\autoloader' );
