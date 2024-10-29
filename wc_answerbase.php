<?php
/**
 * @package Answerbase
 * @version 1.0
 */
/*
Plugin Name: Answerbase for Woocommerce
Plugin URI: http://wordpress.org/plugins/answerbase/
Description: Add Q&A to your Woocommerce store's product pages so you can answer your customers' questions, increase sales conversions and attract new search engine traffic to your online store.
Author: Answerbase
Version: 1.0
Author URI: http://www.answerbase.com/

In order to locate the widget in a custome location open 'wp-content/plugins/woocommerce/templates/content-single-product.php' 
and add the following line wc_answerbase_show_widget(); in the requested location.

*/

register_activation_hook(__FILE__, 'wc_answerbase_activate');
register_uninstall_hook(__FILE__, 'wc_answerbase_uninstall');
add_action('plugins_loaded', 'wc_answerbase_init');
add_action('init', 'wc_answerbase_check');

function answerbase_settings_link($links) {
    $mylinks = array('<a href="' . admin_url( 'admin.php?page=woocommerce-answerbase-settings-page' ) . '">Settings</a>',);
    return array_merge($mylinks, $links);
}

function wc_answerbase_activate() {
	if(current_user_can('activate_plugins')) {
		$default_settings = get_option('answerbase_settings', false);
		if(!is_array($default_settings)) {
			add_option('answerbase_settings', wc_answerbase_get_default_settings());
		}
        update_option('wc_answerbase_init', true);
	}
}

function wc_answerbase_check() {
    if (get_option('wc_answerbase_init', false)) {
        delete_option('wc_answerbase_init');
        wp_redirect(admin_url('admin.php?page=woocommerce-answerbase-settings-page'));
        exit;
    }
}

function wc_answerbase_uninstall() {
	if(current_user_can('activate_plugins')) {
    	delete_option('answerbase_settings');
	}
}

function wc_answerbase_init() {
	include(plugin_dir_path( __FILE__ ) . 'lib/answerbase_widget.php');
	include(plugin_dir_path( __FILE__ ) . 'templates/wc_answerbase_admin.php');

	if (is_admin()) {
        add_action('admin_menu', 'wc_answerbase_admin_menu');
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'answerbase_settings_link' );
    }
	else if(wc_answerbase_is_registered()) {
		add_action('template_redirect', 'wc_answerbase_gui_init');	
	}
}

function wc_answerbase_admin_menu() {
    add_action( 'admin_enqueue_scripts', 'wc_answerbase_logo' );
    add_menu_page( 'Answerbase', 'Answerbase', 'manage_options', 'woocommerce-answerbase-settings-page', 'wc_answerbase_settings_page', 'none', null );
}

function wc_answerbase_logo() {
    wp_enqueue_style('answerbaseLogo', plugins_url('assets/css/logo.css', __FILE__));
}

function wc_answerbase_gui_init() {
	if(is_product()) {
        $answerbase_settings = get_option('answerbase_settings',wc_answerbase_get_default_settings());

        switch ($answerbase_settings['location']) {
            case "after_product":
                add_action('woocommerce_after_single_product_summary', 'wc_answerbase_get_widget');
                break;
			case "bottom":
                add_action('woocommerce_after_single_product', 'wc_answerbase_get_widget');
                break;
            case "tab":
                add_action('woocommerce_product_tabs', 'wc_answerbase_get_widget_tab');
                break;
        }
	}
}

function wc_answerbase_get_widget() {
	echo answerbase_get_widget();
}

function wc_answerbase_get_widget_tab($tabs) {
	$answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
	
	$tabs['answerbase_qa'] = array(
			'title' => $answerbase_settings['tab_name'],
			'priority' => 50,
			'callback' => 'wc_answerbase_get_widget'
	 	);
	return $tabs;
}

function wc_answerbase_is_registered() {
    $answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
	return $answerbase_settings['service_url'] != '';
}

function wc_answerbase_get_default_settings() {
	return array( 'service_url' => '',
				  'admin_url' => '',
				  'location' => 'after_product',
				  'tab_name' => 'Q & A'
				  );
}

?>
