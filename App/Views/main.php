<?php
defined( 'ABSPATH' ) or die;
?>
<div id="wlyr-main-page">
    <div class="wlyr-main-header">
        <h1><?php echo esc_html( WLYR_PLUGIN_NAME ); ?> </h1>
        <div><b><?php echo esc_html( "v" . WLYR_PLUGIN_VERSION ); ?></b></div>
    </div>
    <div class="wlyr-parent">
        <div class="wlyr-body-content">
            <div id="wlyr-settings" class="wlyr-body-active-content active-content">
                <div class="wlyr-heading-data">
                    <div class="headings">
                        <div class="heading-section">
                            <h3><?php esc_html_e( "Settings", 'wp-loyalty-yuko-review' ); ?></h3>
                        </div>
                        <div class="heading-buttons">
                            <a type="button" class="wlyr-button-action non-colored-button"
                               href="<?php echo ! empty( $back_to_apps_url ) ? $back_to_apps_url : '#'; ?>">
                                <i class="wlr wlrf-back"></i>
                                <span><?php esc_html_e( "Back to WPLoyalty", 'wp-loyalty-yuko-review' ); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wlyr-input-section table-content">
                <div class="wlyr-input-content">
                    <label><?php esc_html_e( 'Secret key:', 'wp-loyalty-yuko-review' ) ?></label>
                    <input type="password" id="wlyr-secret_key" name="secret_key"
                           value="<?php echo esc_attr( $secret_key ); ?>"
                           placeholder="<?php esc_attr_e( 'Enter Secret Key', 'wp-loyalty-yuko-review' ); ?>">
                </div>
                <div class="wlyr-button-section">
                    <button id="wlyr-save-settings" class="wlyr-button-action">
                        <i class="wlr wlrf-add"></i>
                        <span><?php esc_html_e( "Save Settings", 'wp-loyalty-yuko-review' ); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>