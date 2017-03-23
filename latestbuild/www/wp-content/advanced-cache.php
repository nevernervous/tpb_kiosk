<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define( 'WP_ROCKET_ADVANCED_CACHE', true );
$rocket_cache_path = 'G:\tpb.waaark.com/wp-content/cache/wp-rocket/';
$rocket_config_path = 'G:\tpb.waaark.com/wp-content/wp-rocket-config/';

if ( file_exists( 'G:\tpb.waaark.com\wp-content\plugins\wp-rocket\inc\front/process.php' ) ) {
	include( 'G:\tpb.waaark.com\wp-content\plugins\wp-rocket\inc\front/process.php' );
} else {
	define( 'WP_ROCKET_ADVANCED_CACHE_PROBLEM', true );
}