<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
