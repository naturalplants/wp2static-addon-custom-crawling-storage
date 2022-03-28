<?php

/**
 * Plugin Name:       WP2Static Add-on: Custom Crawling Storage
 * Plugin URI:        https://wp2static.com
 * Description:       Use temporary directory on crawling for WP2Static.
 * Version:           1.1.1-dev
 * Requires PHP:      7.3
 * Author:            Natural Plants
 * Author URI:        https://www.n-plants.co.jp
 * License:           Unlicense
 * License URI:       http://unlicense.org
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP2STATIC_CUSTOM_STORAGE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2STATIC_CUSTOM_STORAGE_VERSION', '1.1.1-dev' );

if ( file_exists( WP2STATIC_CUSTOM_STORAGE_PATH . 'vendor/autoload.php' ) ) {
    require_once WP2STATIC_CUSTOM_STORAGE_PATH . 'vendor/autoload.php';
}

function run_wp2static_addon_custom_crawling_storage() : void {
    $controller = new WP2StaticCustomCrawlingStorage\Controller();
    $controller->run();
}

register_activation_hook(
    __FILE__,
    [ 'WP2StaticCustomCrawlingStorage\Controller', 'activate' ]
);

register_deactivation_hook(
    __FILE__,
    [ 'WP2StaticCustomCrawlingStorage\Controller', 'deactivate' ]
);

add_action( 'plugins_loaded', 'run_wp2static_addon_custom_crawling_storage', 10, 0 );
