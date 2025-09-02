<?php

namespace WLYR\App;


use WLYR\App\Helpers\Plugin;

defined( 'ABSPATH' ) || exit;

class Setup {
	/**
	 * Init setup.
	 */
	public static function init() {
		register_activation_hook( WLYR_PLUGIN_FILE, [ __CLASS__, 'activate' ] );
		register_deactivation_hook( WLYR_PLUGIN_FILE, [ __CLASS__, 'deactivate' ] );
		register_uninstall_hook( WLYR_PLUGIN_FILE, [ __CLASS__, 'uninstall' ] );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'getPluginRowMeta' ], 10, 2 );
	}

	/**
	 * Run plugin activation scripts.
	 */
	public static function activate() {
		Plugin::checkDependencies( true );
	}

	/**
	 * Run plugin activation scripts.
	 */
	public static function deactivate() {
		// silence is golden
	}

	/**
	 * Run plugin activation scripts.
	 */
	public static function uninstall() {
		// silence is golden
	}

	/**
	 * Retrieves the plugin row meta to be displayed on the WordPress plugin page.
	 *
	 * @param array $links The existing plugin row meta links.
	 * @param string $file The path to the plugin file.
	 *
	 * @return array
	 */
	public static function getPluginRowMeta( $links, $file ) {
		if ( $file != plugin_basename( WLYR_PLUGIN_NAME ) ) {
			return $links;
		}
		$row_meta = [
			'support' => '<a href="' . esc_url( 'https://wployalty.net/support/' ) . '" aria-label="' . esc_attr__( 'Support', 'wp-loyalty-yuko-review' ) . '">' . esc_html__( 'Support', 'wp-loyalty-yuko-review' ) . '</a>',
		];

		return array_merge( $links, $row_meta );
	}
}