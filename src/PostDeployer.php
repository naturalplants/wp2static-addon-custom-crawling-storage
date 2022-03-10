<?php

namespace WP2StaticCustomCrawlingStorage;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use WP2Static\WsLog;

class PostDeployer {

    public function __construct() {}

    public function perpetuateFiles(
        string $processed_site_path, string $perpetuate_storage_path = '' ) : void
    {
        // check if dir exists
        if ( ! is_dir( $processed_site_path ) || ! is_dir( $perpetuate_storage_path ) ) {
            WsLog::w( 'No directory found to perpetuate.' );
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $processed_site_path,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ( $iterator as $filename => $file_object ) {
            $base_name = basename( $filename );
            if ( $base_name != '.' && $base_name != '..' ) {
                $real_filepath = realpath( $filename );
                if ( ! $real_filepath ) {
                    $err = 'Trying to move unknown file to the perpetuate storage: ' . $filename;
                    WsLog::l( $err );
                    continue;
                }

                // Standardise all paths to use / (Windows support)
                $relative_filepath = str_replace( $processed_site_path, '', $real_filepath );
                $destination_filepath =
                    trailingslashit( $perpetuate_storage_path ) . ltrim( $relative_filepath, '/' );
                $destination_dir = dirname( $destination_filepath );

                if ( ! is_string( $filename ) ) {
                    continue;
                }
                if ( ! file_exists( $destination_dir ) ) {
                    if ( ! mkdir( $destination_dir, 0755, true ) ) {
                        WsLog::l(
                            sprintf(
                                'Fail to create the destination directory: %s.',
                                $destination_dir
                            )
                        );
                        continue;
                    }
                }
                if ( ! rename( $real_filepath, $destination_filepath ) ) {
                    WsLog::l( "Fail to move to the perpetuate storage: {$real_filepath}." );
                }
            }
        }
    }
}
