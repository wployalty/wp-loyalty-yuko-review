<?php
namespace WLYR\App\Helpers;

defined( 'ABSPATH' ) or die;
class WC {
	public static $instance = null;

	/**
	 * Get instance.
	 *
	 * @param   array  $config
	 *
	 * @return self|null
	 */
	public static function getInstance( array $config = [] ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $config );
		}

		return self::$instance;
	}

	/**
	 * Check have access to page.
	 *
	 * @return bool
	 */
	public static function hasAdminPrivilege() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * render template.
	 *
	 * @param string $file File path.
	 * @param array $data Template data.
	 * @param bool $display Display or not.
	 *
	 * @return string|void
	 */
	public static function renderTemplate( string $file, array $data = [], bool $display = true ) {
		$content = '';
		if ( file_exists( $file ) ) {
			ob_start();
			extract( $data );
			include $file;
			$content = ob_get_clean();
		}
		if ( $display ) {
			echo $content;
		} else {
			return $content;
		}
	}

	/**
	 * Verify nonce.
	 *
	 * @param string $nonce nonce value.
	 * @param string $nonce_name nonce name.
	 *
	 * @return bool
	 */
	public static function verifyNonce( $nonce, $nonce_name = - 1 ) {
		if ( wp_verify_nonce( $nonce, $nonce_name ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if basic security is valid.
	 *
	 * @param string $nonce_name Name of the nonce.
	 *
	 * @return bool Indicates if basic security is valid or not.
	 */
	public static function isBasicCheckValid( $nonce_name = '' ) {
		$nonce = (string) Input::get( 'wlyr_nonce' );
		if ( ! self::hasAdminPrivilege() || ! self::verifyNonce( $nonce, $nonce_name ) ) {
			return false;
		}

		return true;
	}
}