<?php
if (!defined( 'ABSPATH')) {
    exit;
}

// For woocommerce
function woocpa_WooCommerce_register_required_plugins() {
    $w_check_display = get_current_screen();
	if (isset( $w_check_display->parent_file) && 'plugins.php' === $w_check_display->parent_file && 'update' === $w_check_display->id) {
		return;
	}
	$bwd_w_plugin_plugin = 'woocommerce/woocommerce.php';
	if (woocpa_WooCommerce_addon_install()) {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		$bwd_w_plugin_active_url = wp_nonce_url('plugins.php?action=activate&plugin=' . $bwd_w_plugin_plugin . '&plugin_status=all&paged=1&s', 'activate-plugin_' . $bwd_w_plugin_plugin );
		$bwd_w_plugin_the_notice_is = '<p><b>WooCommerce Product Accordion</b> requires WooCommerce to be activated.</p>';
        $bwd_w_plugin_the_notice_is .= '<p><a href="'. $bwd_w_plugin_active_url .'" class="button-primary">Activate WooCommerce</a></p>';
	} else {
		if (!current_user_can('install_plugins')) {
			return;
		}
		$w_install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
		$bwd_w_plugin_the_notice_is = '<p><b>WooCommerce Product Accordion</b> requires WooCommerce to be installed and activated.</p>';
		$bwd_w_plugin_the_notice_is .= '<p><a href="'. $w_install_url .'" class="button-primary">Install WooCommerce</a></p>';
	}
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ):
	echo '<div class="notice notice-error"><p>' . $bwd_w_plugin_the_notice_is . '</p></div>';
	endif;
}

function woocpa_WooCommerce_addon_install() {
    $w_file_path = 'woocommerce/woocommerce.php';
    $w_installed_plugins = get_plugins();
    return isset($w_installed_plugins[$w_file_path]);
}

// For Elementor
function woocpa_admin_notice_missing_main_plugin() {
    $check_display = get_current_screen();
	if (isset( $check_display->parent_file) && 'plugins.php' === $check_display->parent_file && 'update' === $check_display->id) {
		return;
	}
	$bwd_plugin_plugin = 'elementor/elementor.php';
	if (woocpa_addon_install()) {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		$bwd_plugin_active_url = wp_nonce_url('plugins.php?action=activate&plugin=' . $bwd_plugin_plugin . '&plugin_status=all&paged=1&s', 'activate-plugin_' . $bwd_plugin_plugin );
		$bwd_plugin_the_notice_is = '<p><b>Accordion</b> requires Elementor to be activated.</p>';
        $bwd_plugin_the_notice_is .= '<p><a href="'. $bwd_plugin_active_url .'" class="button-primary">Activate Elementor</a></p>';
	} else {
		if (!current_user_can('install_plugins')) {
			return;
		}
		$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
		$bwd_plugin_the_notice_is = '<p><b>Accordion</b> requires Elementor to be installed and activated.</p>';
		$bwd_plugin_the_notice_is .= '<p><a href="'. $install_url .'" class="button-primary">Install Elementor</a></p>';
	}
	echo '<div class="notice notice-error"><p>' . $bwd_plugin_the_notice_is . '</p></div>';
}

function woocpa_admin_notice_minimum_elementor_version() {

	if (!current_user_can('update_plugins')) {
		return;
	}
	$file_path = 'elementor/elementor.php';
    $upgrade_link = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file_path, 'upgrade-plugin_' . $file_path);
	$bwd_plugin_the_notice_is = '<p><b>Accordion</b> does not work since you are using an older version of Elementor</p>';
    $bwd_plugin_the_notice_is .= '<p><a href="'. $upgrade_link .'" class="button-primary">Update Elementor</a></p>';
	echo '<div class="notice notice-error">' . $bwd_plugin_the_notice_is . '</div>';
}

function woocpa_addon_install() {
    $file_path = 'elementor/elementor.php';
    $installed_plugins = get_plugins();

    return isset($installed_plugins[$file_path]);
}
