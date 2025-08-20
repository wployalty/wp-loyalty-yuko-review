<?php
/**
 * @author      Wployalty (Alagesan)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace WLYR\App;

use WLYR\App\Controllers\Common;

defined( 'ABSPATH' ) or die;

class Router {

	/**
	 * Init action and filter
	 *
	 * @return void
	 */
	function init() {
		if(is_admin()){
			add_action( 'admin_menu', [ Common::class, 'addMenu' ] );
			add_action( 'admin_enqueue_scripts', [ Common::class, 'adminScripts' ], 100 );
			//add_action( 'admin_footer', [ Common::class, 'hideMenu' ] );
			add_action( 'wp_ajax_wlyr_save_settings', [ Common::class, 'save' ] );
		}
		add_action( 'rest_api_init', [ Common::class, 'registerRestApi' ] );
		/*self::$controller = empty( self::$controller ) ? new Controller() : self::$controller;
		if ( is_admin() ) {
			add_action( 'admin_menu', [ Controller::class, 'addMenu' ] );
			add_action( 'network_admin_menu', [ Controller::class, 'addMenu' ] );
			add_action( 'admin_enqueue_scripts', [ Controller::class, 'adminScripts' ], 100 );
			add_action( 'admin_footer', [ Controller::class, 'hideMenu' ] );
			add_action( 'wp_ajax_wljm_webhook_delete', [ Controller::class, 'deleteWebHook' ] );
			add_action( 'wp_ajax_wljm_webhook_create', [ Controller::class, 'createWebHook' ] );
		}

		add_action( 'rest_api_init', [ Controller::class, 'registerRestApi' ] );
		$hide_widget = get_option( 'judgeme_option_hide_widget' );
		if ( ! $hide_widget ) {
			add_action( 'woocommerce_after_single_product_summary', [
				Controller::class,
				'displayProductReviewMessage'
			], 13 );
		}*/
	}
}