<?php
/**
 * Link processor class
 *
 * @package linkrel-defender
 */

namespace Linkrel_Defender\Inc;

use Linkrel_Defender\Inc\Traits\Singleton;

/**
 * Link_Processor class
 */
class Link_Processor {

	use Singleton;

	/**
	 * Options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Construct method
	 */
	protected function __construct() {

		$this->options = get_option(
			'linkrel-defender-settings',
			array(
				'internal_links'   => '',
				'dofollow_links'   => '',
				'nofollow_links'   => '',
				'nofollow_new_tab' => '0',
			)
		);

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	protected function setup_hooks() {

		add_filter( 'the_content', array( $this, 'process_links' ) );
	}

	/**
	 * Process links
	 *
	 * @param string $content Content.
	 * @return string
	 */
	public function process_links( $content ) {

		if ( empty( $content ) ) {
			return $content;
		}

		$internal_links = array_filter( explode( '\n', str_replace( ',', '\n', $this->options['internal_links'] ) ) );
		$dofollow_links = array_filter( explode( '\n', str_replace( ',', '\n', $this->options['dofollow_links'] ) ) );
		$nofollow_links = array_filter( explode( '\n', str_replace( ',', '\n', $this->options['nofollow_links'] ) ) );

		$processor = new \WP_HTML_Tag_Processor( $content );

		while ( $processor->next_tag( 'a' ) ) {
			$href = $processor->get_attribute( 'href' );

			if ( empty( $href ) ) {
				continue;
			}

			$href = trim( $href );

			foreach ( $internal_links as $internal ) {
				if ( strpos( $href, trim( $internal ) ) !== false ) {
					$processor->set_attribute( 'target', '_self' );
					continue 2;
				}
			}

			foreach ( $dofollow_links as $dofollow ) {
				if ( strpos( $href, trim( $dofollow ) ) !== false ) {
					$processor->set_attribute( 'rel', 'dofollow' );
					continue 2;
				}
			}

			foreach ( $nofollow_links as $nofollow ) {
				if ( strpos( $href, trim( $nofollow ) ) !== false ) {
					$processor->set_attribute( 'rel', 'nofollow' );

					if ( '1' === $this->options['nofollow_new_tab'] ) {
						$processor->set_attribute( 'target', '_blank' );
					}

					continue 2;
				}
			}
		}

		return $processor->get_updated_html();
	}
}
