<?php
/**
 * Settings page class
 *
 * @package linkrel-defender
 */

namespace Linkrel_Defender\Inc;

use Linkrel_Defender\Inc\Traits\Singleton;

/**
 * Settings class
 */
class Settings {

	use Singleton;

	/**
	 * Construct method
	 */
	protected function __construct() {

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	protected function setup_hooks() {

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page
	 */
	public function add_settings_page() {

		add_options_page(
			__( 'LinkRel Defender Settings', 'linkrel-defender' ),
			__( 'LinkRel Defender', 'linkrel-defender' ),
			'manage_options',
			'linkrel-defender',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {

		register_setting(
			'linkrel-defender-settings',
			'linkrel-defender-settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'linkrel-defender-settings-section',
			__( 'General Settings', 'linkrel-defender' ),
			array( $this, 'render_settings_section' ),
			'linkrel-defender-settings'
		);

		$this->add_settings_fields();
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'LinkRel Defender Settings', 'linkrel-defender' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'linkrel-defender-settings' );
				do_settings_sections( 'linkrel-defender-settings' );
				submit_button();
				?>
			</form>
		</div>

		<?php
	}

	/**
	 * Render settings section
	 */
	public function render_settings_section() {

		esc_html_e( 'General settings for LinkRel Defender plugin. (Enter each url one per line)', 'linkrel-defender' );
	}

	/**
	 * Add settings fields
	 */
	private function add_settings_fields() {

		$fields = array(
			'internal_links'   => array(
				'label'       => __( 'Internal Links', 'linkrel-defender' ),
				'description' => __( 'Enter internal domain which should open in same window.', 'linkrel-defender' ),
			),
			'dofollow_links'   => array(
				'label'       => __( 'Dofollow Links', 'linkrel-defender' ),
				'description' => __( 'Enter domain which should be dofollow.', 'linkrel-defender' ),
			),
			'nofollow_links'   => array(
				'label'       => __( 'Nofollow Links', 'linkrel-defender' ),
				'description' => __( 'Enter domain which should be nofollow regardless of other settings.', 'linkrel-defender' ),
			),
			'nofollow_new_tab' => array(
				'label'       => __( 'Open Nofollow Links in New Tab', 'linkrel-defender' ),
				'description' => __( 'Check this box to open all nofollow links in a new tab.', 'linkrel-defender' ),
				'type'        => 'checkbox',
			),
		);

		foreach ( $fields as $field => $args ) {

			add_settings_field(
				$field,
				$args['label'],
				array( $this, 'render_settings_field' ),
				'linkrel-defender-settings',
				'linkrel-defender-settings-section',
				array(
					'field'       => $field,
					'label'       => $args['label'],
					'description' => $args['description'],
					'type'        => isset( $args['type'] ) ? $args['type'] : 'textarea',
				)
			);
		}
	}

	/**
	 * Render settings field
	 */
	public function render_settings_field( $args ) {

		$field = $args['field'];
		$value = get_option( 'linkrel-defender-settings', array() );

		$value = isset( $value[ $field ] ) ? $value[ $field ] : '';

		?>

		<?php if ( isset( $args['type'] ) && 'checkbox' === $args['type'] ) : ?>
			<input type="checkbox" name="linkrel-defender-settings[<?php echo esc_attr( $field ); ?>]" value="1" <?php checked( $value, '1' ); ?> />
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
			<?php
		else :
			$value            = str_replace( ',', "\n", $value );
			$values           = array_filter( explode( "\n", $value ) );
			$formatted_values = implode( "\n", array_map( 'trim', $values ) );
			?>
			<textarea name="linkrel-defender-settings[<?php echo esc_attr( $field ); ?>]" rows="5" cols="50"><?php echo esc_textarea( $formatted_values ); ?></textarea>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Input data.
	 */
	public function sanitize_settings( $input ) {

		$output = array();

		foreach ( $input as $key => $value ) {
			$values = array_filter( array_map( 'trim', explode( "\n", $value ) ) );

			$output[ $key ] = sanitize_text_field( implode( ',', $values ) );
		}

		return $output;
	}
}
