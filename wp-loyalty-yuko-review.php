<?php
/**
 * Plugin Name: WPLoyalty - Yuko Review
 * Plugin URI: https://www.wployalty.net
 * Description: The add-on integrates WPLoyalty with the Yuko and allows you to reward customers with points for writing reviews in Yuko
 * Version: 1.0.2
 * Author: WPLoyalty
 * Slug: wp-loyalty-yuko-review
 * Text Domain: wp-loyalty-yuko-review
 * Domain Path: /i18n/languages/
 * Requires at least: 6.0
 * WC requires at least: 6.5
 * WC tested up to: 10.1
 * Contributors: Alagesan
 * Author URI: https://wployalty.net/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WPLoyalty: 1.2.14
 * WPLoyalty Page Link: wp-loyalty-yuko-review
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WLYR\App\Router;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

defined( 'ABSPATH' ) or die;
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
	}
} );
if ( ! function_exists( 'isWPLoyaltyActiveOrNot' ) ) {
	/**
	 * Check WPLoyalty active or not.
	 *
	 * @return bool
	 */
	function isWPLoyaltyActiveOrNot() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins,
				false ) || in_array( 'wp-loyalty-rules-lite/wp-loyalty-rules-lite.php', $active_plugins,
				false ) || in_array( 'wployalty/wp-loyalty-rules-lite.php', $active_plugins, false );
	}
}

if ( ! function_exists( 'isWoocommerceActive' ) ) {
	/**
	 * Check WooCommerce active.
	 *
	 * @return bool
	 */
	function isWoocommerceActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'woocommerce/woocommerce.php',
				$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}

if ( ! isWoocommerceActive() || ! isWployaltyActiveOrNot() ) {
	return;
}

//Define the plugin version
defined( 'WLYR_PLUGIN_VERSION' ) or define( 'WLYR_PLUGIN_VERSION', '1.0.2' );
defined( 'WLYR_PLUGIN_NAME' ) or define( 'WLYR_PLUGIN_NAME', 'WPLoyalty - Yuko Review' );
defined( 'WLYR_MINIMUM_PHP_VERSION' ) or define( 'WLYR_MINIMUM_PHP_VERSION', '7.4' );
defined( 'WLYR_MINIMUM_WP_VERSION' ) or define( 'WLYR_MINIMUM_WP_VERSION', '6.0' );
defined( 'WLYR_MINIMUM_WC_VERSION' ) or define( 'WLYR_MINIMUM_WC_VERSION', '6.5' );
defined( 'WLYR_MINIMUM_WLR_VERSION' ) or define( 'WLYR_MINIMUM_WLR_VERSION', '1.2.14' );
defined( 'WLYR_TEXT_DOMAIN' ) or define( 'WLYR_TEXT_DOMAIN', 'wp-loyalty-yuko-review' );
defined( 'WLYR_PLUGIN_SLUG' ) or define( 'WLYR_PLUGIN_SLUG', 'wp-loyalty-yuko-review' );
defined( 'WLYR_PLUGIN_PATH' ) or define( 'WLYR_PLUGIN_PATH', __DIR__ . '/' );
defined( 'WLYR_PLUGIN_URL' ) or define( 'WLYR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'WLYR_PLUGIN_FILE' ) or define( 'WLYR_PLUGIN_FILE', __FILE__ );

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}
require __DIR__ . '/vendor/autoload.php';

if ( class_exists( Router::class ) ) {
	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://github.com/wployalty/wp-loyalty-yuko-review',
		__FILE__,
		'wp-loyalty-yuko-review'
	);
	$myUpdateChecker->getVcsApi()->enableReleaseAssets();
	if ( ! class_exists( \WLYR\App\Helpers\Plugin::class ) || ! class_exists( \WLYR\App\Setup::class ) ) {
		return;
	}
	\WLYR\App\Setup::init();
	$router = new Router();
	if ( method_exists( $router, 'init' ) && \WLYR\App\Helpers\Plugin::checkDependencies() ) {
		$router->init();
	}
}