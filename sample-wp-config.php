<?php
// Domain Names : {domains}
$table_prefix   = '{table_prefix}';
$request_scheme = $_SERVER['REQUEST_SCHEME'];
$request_domain = $_SERVER['HTTP_HOST'];
$url            = $request_scheme . '://' . $request_domain . '/';

/* Database connection */
define( 'DB_NAME', '{DB_NAME}' );
define( 'DB_USER', '{DB_USER}' );
define( 'DB_PASSWORD', '{DB_PASSWORD}' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_general_ci' );

/* Security Keys */
// {AUTH_KEYS}

define( 'WP_DISABLE_FATAL_ERROR_HANDLER', false );

define( 'WP_SITEURL', $url );
define( 'WP_HOME', $url );
define( 'WP_CONTENT_URL', $url . 'wp-content' );
define( 'WP_PLUGIN_URL', $url . 'wp-content/plugins' );

define( 'COOKIE_DOMAIN', $request_domain );
define( 'TEST_COOKIE', 'wordpress_test_cookie' );
define( 'COOKIEHASH', 'wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'LOGGED_IN_COOKIE', 'wordpress_logged_in_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'SECURE_AUTH_COOKIE', 'wordpress_logged_in_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'AUTH_COOKIE', 'wordpress_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'PASS_COOKIE', 'wordpresspass_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'USER_COOKIE', 'wordpressuser_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );
define( 'RECOVERY_MODE_COOKIE', 'wordpress_rec_wVHucbZ46pFOQ3zkaNx5J40QZBhGSlMarqqmwfD3kp5Z5Xya8BpuDi9WyLS7hiO7' );

define( 'AUTOSAVE_INTERVAL', 30 );
define( 'WP_POST_REVISIONS', true );
define( 'MEDIA_TRASH', true );
define( 'EMPTY_TRASH_DAYS', 7 );
define( 'WP_MAIL_INTERVAL', 86400 );

define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

define( 'DISALLOW_FILE_MODS', false );
define( 'DISALLOW_FILE_EDIT', false );
define( 'IMAGE_EDIT_OVERWRITE', true );

define( 'WP_CACHE', false );
define( 'WP_CACHE_KEY_SALT', 'd4syrys6ufiifzanlb:' );
define( 'COMPRESS_CSS', false );
define( 'COMPRESS_SCRIPTS', false );
define( 'CONCATENATE_SCRIPTS', false );
define( 'ENFORCE_GZIP', false );

define( 'DISABLE_WP_CRON', false );
define( 'ALTERNATE_WP_CRON', false );
define( 'WP_CRON_LOCK_TIMEOUT', 60 );

define( 'WPMU_PLUGIN_DIR', __DIR__ . '/wp-content/mu-plugins/' );
define( 'WPMU_PLUGIN_URL', $url . 'wp-content/mu-plugins/' );
define( 'MUPLUGINDIR', __DIR__ . '/wp-content/mu-plugins/' );

define( 'WP_ALLOW_MULTISITE', false );
define( 'WP_DEFAULT_THEME', 'twentytwenty' );

define( 'WP_HTTP_BLOCK_EXTERNAL', false );
if ( WP_HTTP_BLOCK_EXTERNAL ) {
	define( 'WP_ACCESSIBLE_HOSTS', '*.wordpress.org,*.github.com' );
}

define( 'WP_DEBUG', true );
if ( WP_DEBUG ) {
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'SCRIPT_DEBUG', true );
	define( 'SAVEQUERIES', true );
}


if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];

	if ( 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
		$_SERVER['HTTPS'] = 'on';
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

require_once ABSPATH . 'wp-settings.php';
