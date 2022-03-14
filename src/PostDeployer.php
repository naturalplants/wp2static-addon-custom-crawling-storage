<?php

namespace WP2StaticCustomCrawlingStorage;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use WP2Static\WsLog;

class PostDeployer {

    public function __construct() {}

    public function perpetuateFiles(
        string $source_path, string $taraget_path = '' ) : void
    {
        // check if dir exists
        if ( ! is_dir( $source_path ) || ! is_dir( $taraget_path ) ) {
            WsLog::w(
                sprintf(
                    'One of directories are not found to perpetuate. 
                    $source_path: %s, $taraget_path: %s',
                    $source_path,
                    $taraget_path
                )
            );

            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $source_path,
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
                $relative_filepath = str_replace( $source_path, '', $real_filepath );
                $destination_filepath =
                    trailingslashit( $taraget_path ) . ltrim( $relative_filepath, '/' );
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
