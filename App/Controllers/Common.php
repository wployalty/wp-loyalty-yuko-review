<?php
/**
 * @author      Wployalty (Alagesan)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace WLYR\App\Controllers;

defined( 'ABSPATH' ) or die;

use Wlr\App\Premium\Helpers\ProductReview;
use WLYR\App\Helpers\Input;
use WLYR\App\Helpers\WC;

class Common {
	/**
	 * Add plugin menu.
	 * @return void
	 */
	public static function addMenu() {
		if ( WC::hasAdminPrivilege() ) {
			add_menu_page( WLYR_PLUGIN_NAME, WLYR_PLUGIN_NAME, 'manage_woocommerce', WLYR_PLUGIN_SLUG, [
				self::class,
				'displayMainPage'
			], 'dashicons-megaphone', 57 );
		}
	}


	/**
	 * Main page.
	 * @return void
	 */
	public static function displayMainPage() {
		if ( ! WC::hasAdminPrivilege() ) {
			wp_die( esc_html( __( "Don't have access permission", 'wp-loyalty-yuko-review' ) ) );
		}
		//it will automatically add new table column,via auto generate alter query
		if ( Input::get( 'page' ) != WLYR_PLUGIN_SLUG ) {
			return;
		}
		$path     = WLYR_PLUGIN_PATH . 'App/Views/main.php';
		$settings = get_option( 'wplyr_settings', [] );
		$params   = [
			'back_to_apps_url'   => admin_url( 'admin.php?' . http_build_query( [ 'page' => WLR_PLUGIN_SLUG ] ) ) . '#/apps',
			'secret_key'         => $settings['secret_key'] ?? '',
			'webhook_url'        => rest_url( 'wployalty/yuko/v1/review/approved' ),
			'tutorial_video_url' => 'https://youtu.be/X9tmvnIbuS0',
		];
		WC::renderTemplate( $path, $params );
	}

	/**
	 * Enqueue admin js and css.
	 * @return void
	 */
	public static function adminScripts() {
		if ( ! WC::hasAdminPrivilege() || Input::get( 'page' ) != WLYR_PLUGIN_SLUG ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		wp_enqueue_style( WLYR_PLUGIN_SLUG . '-admin-css', WLYR_PLUGIN_URL . 'assets/css/admin.css', [],
			WLYR_PLUGIN_VERSION );
		wp_enqueue_script( WLYR_PLUGIN_SLUG . '-wlyr-admin', WLYR_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery' ],
			WLYR_PLUGIN_VERSION . '&t=' . time(), true );
		wp_enqueue_style( WLR_PLUGIN_SLUG . '-alertify', WLR_PLUGIN_URL . 'Assets/Admin/Css/alertify.min.css', [],
			WLR_PLUGIN_VERSION );
		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script( WLR_PLUGIN_SLUG . '-alertify', WLR_PLUGIN_URL . 'Assets/Admin/Js/alertify.min.js', [],
			WLR_PLUGIN_VERSION . '&t=' . time() );
		wp_enqueue_style( WLR_PLUGIN_SLUG . '-wlr-font', WLR_PLUGIN_URL . 'Assets/Site/Css/wlr-fonts.min.css', [],
			WLR_PLUGIN_VERSION );
		wp_localize_script( WLYR_PLUGIN_SLUG . '-wlyr-admin', 'wlyr_localize_data', [
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'plugin_url'                => WLYR_PLUGIN_URL,
			'home_url'                  => home_url(),
			'nonce'                     => wp_create_nonce( 'wlyr_admin_nonce' ),
			'saving_button_label'       => __( 'Saving...', 'wp-loyalty-yuko-review' ),
			'saved_button_label'        => __( 'Save Settings', 'wp-loyalty-yuko-review' ),
			'copied_button_label'       => __( 'Copied!', 'wp-loyalty-yuko-review' ),
			'copied_notification_label' => __( 'Webhook URL copied to clipboard!', 'wp-loyalty-yuko-review' ),
			'copy_error_label'          => __( 'Failed to copy webhook URL. Please copy it manually.',
				'wp-loyalty-yuko-review' ),
		] );
	}

	/**
	 * Hide plugin menu.
	 * @return void
	 */
	public static function hideMenu() {
		?>
        <style>
            #toplevel_page_wp-loyalty-yuko-review {
                display: none !important;
            }
        </style>
		<?php
	}

	public static function save() {
		if ( ! WC::isBasicCheckValid( 'wlyr_admin_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Basic check failed', 'wp-loyalty-yuko-review' ) ] );
		}

		$secret_key = (string) Input::get( 'secret_key' );
		if ( empty( $secret_key ) ) {
			wp_send_json_error( [ 'message' => __( 'Secret key required', 'wp-loyalty-yuko-review' ) ] );
		}

		$data = [
			'secret_key' => (string) Input::get( 'secret_key' )
		];
		update_option( 'wplyr_settings', $data );
		wp_send_json_success( [ 'message' => __( 'Setting saved successfully', 'wp-loyalty-yuko-review' ) ] );
	}

	public static function registerRestApi() {
		$namespace = 'wployalty/yuko/v1';
		register_rest_route( $namespace, '/review/approved', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'handleApprovedReview' ],
			'permission_callback' => '__return_true', // authentication is handled in the callback
		] );
	}

	protected static function verifySignature( \WP_REST_Request $request ) {
		$settings   = get_option( 'wplyr_settings', [] );
		$secret_key = $settings['secret_key'] ?? '';

		$signature = $request->get_header( 'x-yuko-hmac-sha256' );
		if ( empty( $signature ) ) {
			return false;
		}
		$params  = $request->get_body();
		$params  = json_decode( $params, true );
		$payload = json_encode( $params, JSON_UNESCAPED_SLASHES );
		// Calculate HMAC signature
		$calculated_signature = base64_encode( hash_hmac( 'sha256', $payload, $secret_key, true ) );

		return hash_equals( $signature, $calculated_signature );
	}

	public static function handleApprovedReview( \WP_REST_Request $request ) {

		if ( ! self::verifySignature( $request ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Signature verification failed', 'wp-loyalty-yuko-review' )
			],400 );
		}
		$body = $request->get_body();
		$data = json_decode( $body, true );

		$email = $data['email'] ?? '';
		if ( empty( $email ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Customer email invalid', 'wp-loyalty-yuko-review' )
			],400 );
		}

		$product_id = $data['platform_product_id'] ?? 0;
		if ( empty( $product_id ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Platform product id missing', 'wp-loyalty-yuko-review' )
			],400 );
		}

		if ( empty( $data['status'] ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Review status missing', 'wp-loyalty-yuko-review' )
			],400 );
		}

		if ( ! empty( $data['deleted_at'] ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Review is deleted', 'wp-loyalty-yuko-review' )
			],400 );
		}

		if ( strtolower( $data['status'] ) !== 'approved' ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => __( 'Review status invalid', 'wp-loyalty-yuko-review' )
			],400 );
		}

		$validation = apply_filters( 'wlyr_validate_review_before_earn', [
			'valid'   => true,
			'message' => ''
		], $data );

		if ( ! $validation['valid'] ) {
			$error_message = ! empty( $validation['message'] ) ? $validation['message'] : __( 'Review validation failed',
				'wp-loyalty-yuko-review' );

			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error_message
			] );
		}

		$product_review_helper = new ProductReview();
		$action_data           = [
			'user_email'         => $email,
			'product_id'         => $product_id,
			'is_calculate_based' => 'product',
			'product'            => function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false
		];
		$product_review_helper->applyEarnProductReview( $action_data );

		return new \WP_REST_Response( [
			'success' => true,
			'message' => __( 'Webhook received successfully', 'wp-loyalty-yuko-review' )
		] );
	}
}