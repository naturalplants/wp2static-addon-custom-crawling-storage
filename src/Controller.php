<?php

namespace WP2StaticCustomCrawlingStorage;

require_once plugin_dir_path( WP2STATIC_CUSTOM_STORAGE_PATH ) . 'wp2static/src/Addons.php';
require_once plugin_dir_path( WP2STATIC_CUSTOM_STORAGE_PATH ) . 'wp2static/src/SiteInfo.php';

use WP2Static\FilesHelper;
use WP2Static\ProcessedSite;
use WP2Static\SiteInfo;
use WP2static\Addons;
use WP2Static\StaticSite;
use WP2Static\WsLog;

class Controller {
    public function run() : void {
        $this->hooks_before_activate();
        $post_deploy_addons = Addons::getType( 'post_deploy' );
        if ( array_filter(
            $post_deploy_addons,
            fn ( $addon ) => 'wp2static-addon-custom-crawling-storage' === $addon->slug
        ) ) {
            $this->hooks_after_activate();
        }
    }
    private function hooks_before_activate(): void {
        add_filter(
            'wp2static_add_menu_items',
            [ 'WP2StaticCustomCrawlingStorage\Controller', 'addSubmenuPage' ]
        );

        add_action(
            'admin_post_wp2static_custom_crawling_storage_save_options',
            [ $this, 'saveOptionsFromUI' ],
            15,
            1
        );
        add_action(
            'admin_menu',
            [ $this, 'addOptionsPage' ],
            15,
            1
        );

        do_action(
            'wp2static_register_addon',
            'wp2static-addon-custom-crawling-storage',
            'post_deploy',
            'Custom Crawling Storage',
            'https://wwww.n-plants.co.jp',
            'Use a custom directory for crawling.'
        );
    }

    private function hooks_after_activate(): void {
        add_action(
            'wp2static_post_deploy_trigger',
            [ $this, 'postDeploy' ],
            15,
            2
        );
        add_filter(
            'wp2static_crawled_site_path',
            [ $this, 'filterCrawledPath' ],
            15,
            1
        );
        add_filter(
            'wp2static_processed_site_path',
            [ $this, 'filterProcessedPath' ],
            15,
            1
        );
        add_filter(
            'wp2static_deleting_path_prefix',
            [ $this, 'filterDeletingPathPrefix' ],
            15,
            1
        );
    }

    /**
     *  Get all add-on options
     *
     *  @return mixed[] All options
     */
    public static function getOptions() : array {
        global $wpdb;
        $options = [];

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

        $rows = $wpdb->get_results( "SELECT * FROM $table_name" );

        foreach ( $rows as $row ) {
            $options[ $row->name ] = $row;
        }

        return $options;
    }

    /**
     * Seed options
     */
    public static function seedOptions() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';
        $upload_path_and_url = wp_upload_dir();
        $site_id = get_current_blog_id();
        $crawled_site_path = 'wp2static-crawled-site';
        $processed_site_path = 'wp2static-processed-site';
        $queries = [];

        $query_string =
            "INSERT IGNORE INTO $table_name (name, value, label, description) " .
            'VALUES (%s, %s, %s, %s);';

        $queries[] = $wpdb->prepare(
            $query_string,
            'crawlingStoragePath',
            "/tmp/wp2static/sites/{$site_id}/{$crawled_site_path}",
            'Crawling Storage Path',
            'Specify a directory for the crawler to save static files at `crawl` job.'
        );

        $queries[] = $wpdb->prepare(
            $query_string,
            'processingStoragePath',
            "/tmp/wp2static/sites/{$site_id}/{$processed_site_path}",
            'Processing Storage Path',
            'Specify a directory for the processor to save static files at `post_process` job.'
        );

        $queries[] = $wpdb->prepare(
            $query_string,
            'perpetuatedStoragePathForCrawledSite',
            trailingslashit( $upload_path_and_url['basedir'] ) . $crawled_site_path,
            'Perpetuated Storage Path for `crawl`',
            'Specify a directory to perpetuate crawled files after "post_deploy". 
            Leave it blank to ignore.'
        );

        $queries[] = $wpdb->prepare(
            $query_string,
            'perpetuatedStoragePathForProcessedSite',
            trailingslashit( $upload_path_and_url['basedir'] ) . $processed_site_path,
            'Perpetuated Storage Path for `post_process`',
            'Specify a directory to perpetuate post processed files after "post_deploy". 
            Leave it blank to ignore.'
        );

        array_map( fn( $query ) => $wpdb->query( $query ), $queries );

    }

    /**
     * Save options
     *
     * @param mixed $value option value to save
     */
    public static function saveOption( string $name, $value ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

        $wpdb->update(
            $table_name,
            [ 'value' => $value ],
            [ 'name' => $name ]
        );
    }

    public static function renderOptionPage() : void {
        self::createOptionsTable();
        self::seedOptions();

        $view = [];
        $view['nonce_action'] = 'wp2static-custom-crawling-storage-options';
        $view['options'] = self::getOptions();

        require_once __DIR__ . '/../views/option-page.php';
    }


    public function postDeploy() : void {
        $crawled_site_path = StaticSite::getPath();
        $processed_site_path = ProcessedSite::getPath();
        $perpetuated_storage_path_for_crawl =
            self::getValue( 'perpetuatedStoragePathForCrawl' );
        $perpetuated_storage_path_for_post_process =
            self::getValue( 'perpetuatedStoragePathForPostProcess' );
        WsLog::l( 'Custom Crawling Storage Addon taking post deploy action.' );
        $post_deployer = new PostDeployer();
        $post_deployer->perpetuateFiles(
            $crawled_site_path,
            $perpetuated_storage_path_for_crawl
        );
        $post_deployer->perpetuateFiles(
            $processed_site_path,
            $perpetuated_storage_path_for_post_process
        );
        WsLog::l( sprintf( 'Cleaning upload directory %s.', SiteInfo::getPath( 'uploads' ) ) );
        FilesHelper::deleteDirWithFiles( SiteInfo::getPath( 'uploads' ) );
    }

    public static function createOptionsTable() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            value VARCHAR(255) NOT NULL,
            label VARCHAR(255) NULL,
            description VARCHAR(255) NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // dbDelta doesn't handle unique indexes well.
        $indexes = $wpdb->query( "SHOW INDEX FROM $table_name WHERE key_name = 'name'" );
        if ( 0 === $indexes ) {
            $result = $wpdb->query( "CREATE UNIQUE INDEX name ON $table_name (name)" );
            if ( false === $result ) {
                \WP2Static\WsLog::l( "Failed to create 'name' index on $table_name." );
            }
        }
    }

    public static function activateForSingleSite(): void {
        self::createOptionsTable();
        self::seedOptions();
    }

    public static function deactivateForSingleSite() : void {
    }

    public static function deactivate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::deactivateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::deactivateForSingleSite();
        }
    }

    public static function activate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::activateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::activateForSingleSite();
        }
    }

    /**
     * Add WP2Static submenu
     *
     * @param mixed[] $submenu_pages array of submenu pages
     * @return mixed[] array of submenu pages
     */
    public static function addSubmenuPage( array $submenu_pages ) : array {
        $submenu_pages['custom_crawling_storage'] = [
            'WP2StaticCustomCrawlingStorage\Controller',
            'renderOptionPage',
        ];

        return $submenu_pages;
    }

    public static function saveOptionsFromUI() : void {
        check_admin_referer( 'wp2static-custom-crawling-storage-options' );

        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['crawlingStoragePath'] ) ],
            [ 'name' => 'crawlingStoragePath' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['perpetuatedStoragePathForCrawledSite'] ) ],
            [ 'name' => 'perpetuatedStoragePathForCrawledSite' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['processingStoragePath'] ) ],
            [ 'name' => 'processingStoragePath' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['perpetuatedStoragePathForProcessedSite'] ) ],
            [ 'name' => 'perpetuatedStoragePathForProcessedSite' ]
        );

        wp_safe_redirect( admin_url( 'admin.php?page=wp2static-addon-custom-crawling-storage' ) );
        exit;
    }

    /**
     * Get option value
     *
     * @return string option value
     */
    public static function getValue( string $name ) : string {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_custom_crawling_storage_options';

        $sql = $wpdb->prepare(
            "SELECT value FROM $table_name WHERE" . ' name = %s LIMIT 1',
            $name
        );

        $option_value = $wpdb->get_var( $sql );

        if ( ! is_string( $option_value ) ) {
            return '';
        }

        return $option_value;
    }

    public function addOptionsPage() : void {
        add_submenu_page(
            '',
            'Custom Crawling Storage Options',
            'Custom Crawling Storage Options',
            'manage_options',
            'wp2static-addon-custom-crawling-storage',
            [ $this, 'renderOptionPage' ]
        );
    }

    public function filterUploadPath( array $site_info ): array {
        $options = self::getOptions();

        $site_info['uploads_path'] = $options['crawlingStoragePath']->value;
        return $site_info;
    }

    public function filterDeletingPathPrefix( string $path ): string {
        $options = self::getOptions();
        return $options['perpetuatedStoragePath']->value
            ? $options['perpetuatedStoragePath']->value
            : $path;
    }
}

