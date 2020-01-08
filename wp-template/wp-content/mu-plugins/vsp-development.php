<?php
/**
 * Plugin Name: VSP Dev Plugin
 * Description: Localhost Development Plugin Source. if(!file_exists(ABSPATH.'wp-content/mu-plugins/vsp-development.php')){ symlink('E:\localhost\www\wp\template\wp-content\mu-plugins\vsp-development.php',ABSPATH.'wp-content/mu-plugins/vsp-development.php'); }
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

define( 'VPS_TPLF', 'E:\localhost\www\wp\template\wp-content\plugins\\' );

if ( ! function_exists( 'vsp_dev_copy' ) ) {
	/**
	 * @param $src
	 * @param $dst
	 */
	function vsp_dev_copy( $src, $dst ) {
		$dir = opendir( $src . '/' );
		@mkdir( $dst );
		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( ! in_array( $file, array( '.', '..', '.git', 'node_module' ), true ) ) {
				if ( is_dir( $src . '/' . $file ) ) {
					vsp_dev_copy( $src . '/' . $file, $dst . '/' . $file );
				} else {
					copy( $src . '/' . $file, $dst . '/' . $file );
				}
			}
		}
		closedir( $dir );
	}
}

/**
 * Class VSP_DEV
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class VSP_DEV {
	/**
	 * @var array
	 * @access
	 */
	protected $copy_folders = array();

	/**
	 * @var array
	 * @access
	 */
	protected $copy_muplugins = array();

	/**
	 * VSP_DEV constructor.
	 */
	public function __construct() {
		add_filter( 'qm/built-in-collectors', array( &$this, 'modify_outputter' ), 999 );
		add_action( 'phpmailer_init', array( &$this, 'update_smtp' ) );
		if ( defined( 'DB_NAME' ) && DB_NAME !== 'wp_template' ) {
			$this->copy_folders   = array(
				VPS_TPLF . 'query-monitor' => 'wp-content/plugins/query-monitor/query-monitor.php',
			);
			$this->copy_muplugins = array(
				VPS_TPLF . 'query-monitor-extend' => 'wp-content/mu-plugins/query-monitor-extend/query-monitor-extend.php',
				VPS_TPLF . 'theme-inspector'      => 'wp-content/mu-plugins/theme-inspector/theme-inspector.php',
				VPS_TPLF . 'classic-editor'       => 'wp-content/mu-plugins/classic-editor/classic-editor.php',
				VPS_TPLF . 'wordpress-importer'   => 'wp-content/mu-plugins/wordpress-importer/wordpress-importer.php',
				VPS_TPLF . 'user-switching'       => 'wp-content/mu-plugins/user-switching/user-switching.php',
				VPS_TPLF . 'inspector'            => 'wp-content/mu-plugins/inspector/inspector.php',
				VPS_TPLF . 'debug-quick-look'     => 'wp-content/mu-plugins/debug-quick-look/debug-quick-look.php',
			);
			$this->load_other_plugins();
			$this->do_nontemplate();
		}
		$this->handle_debug_log();
	}

	public function modify_outputter( $a ) {
		unset( $a['block_editor'] );
		//unset( $a['cache'] );
		unset( $a['caps'] );
		unset( $a['conditionals'] );
		unset( $a['debug_bar'] );
		unset( $a['environment'] );
		unset( $a['languages'] );
		unset( $a['logger'] );
		unset( $a['redirects'] );
		unset( $a['request'] );
		unset( $a['theme'] );
		//unset( $a['timing'] );
		//unset( $a['transients'] );
		return $a;
	}

	/**
	 * @param \PHPMailer $phpmailer
	 */
	public function update_smtp( $phpmailer ) {
		$phpmailer->Host     = '127.0.0.1';
		$phpmailer->Port     = '2525';
		$phpmailer->SMTPAuth = false;
		$phpmailer->isSMTP();
	}

	/**
	 * DO If its not template.
	 */
	public function do_nontemplate() {
		$this->copy_folders();
		add_action( 'wp_dashboard_setup', array( &$this, 'remove_dashboard_widgets' ), 9999 );
		add_action( 'plugins_loaded', array( &$this, 'copy_muplugins' ), -100 );
	}

	/**
	 * Remove Existing Dashboard Widegts.
	 */
	public function remove_dashboard_widgets() {
		// Get global obj.
		global $wp_meta_boxes;

		// Left side metaboxes.
		$wp_meta_boxes['dashboard']['normal']['core'] = array();

		// Right side metaboxes.
		$wp_meta_boxes['dashboard']['side']['core'] = array();

	}

	/**
	 * Copy Plugins From Template Site To Current Sites Plugins Folder
	 */
	protected function copy_folders() {
		foreach ( $this->copy_folders as $orginal_path => $new_path ) {
			if ( ! file_exists( ABSPATH . '\\' . $new_path ) ) {
				vsp_dev_copy( $orginal_path, ABSPATH . '/' . dirname( $new_path ) );
			}
		}
	}

	/**
	 * Copy Plugins From Template To Sites MU Folder.
	 */
	public function copy_muplugins() {
		foreach ( $this->copy_muplugins as $orginal_path => $new_path ) {
			if ( ! file_exists( ABSPATH . '\\' . $new_path ) ) {
				vsp_dev_copy( $orginal_path, ABSPATH . '/' . dirname( $new_path ) );
			}

			if ( file_exists( ABSPATH . '/' . $new_path ) ) {
				include ABSPATH . $new_path;
			}
		}
	}

	/**
	 * Handles Debug Log.
	 */
	public function handle_debug_log() {
		$ctime = date( 'D-d-M-Y-h-i-s-a' );
		$size  = ( file_exists( ABSPATH . 'wp-content/debug.log' ) ) ? filesize( ABSPATH . 'wp-content/debug.log' ) : 0;
		if ( $size >= 10000 ) {
			@mkdir( ABSPATH . 'wp-content/debuglog/' );
			copy( ABSPATH . 'wp-content/debug.log', ABSPATH . 'wp-content/debuglog/' . $ctime . '.log' );
			@file_put_contents( ABSPATH . 'wp-content/debug.log', '' );
		}
	}

	/**
	 * Loads Other Plugins.
	 */
	public function load_other_plugins() {
		if ( defined( 'VS_WPONION' ) ) {
			$load = VS_WPONION;
			$load = ( true === $load ) ? 'E:\localhost\www\wp\framework\wponion\wp-content\plugins\wponion\wponion.php' : $load;
			if ( false !== $load ) {
				defined( 'WPONION_URL' ) or define( 'WPONION_URL', 'https://wponion.pc/wp-content/plugins/wponion/' );
				//$this->copy_muplugins[ $load ] = 'wp-content/mu-plugins/wponion/wponion.php';
				require_once $load;
			}
		}

		if ( defined( 'VS_VSP' ) ) {
			$load = VS_VSP;
			$load = ( true === $load ) ? 'E:\localhost\www\wp\framework\vsp-framework\wp-content\plugins\vsp-sample-plugin\vsp-framework\vsp-init.php' : $load;
			if ( false !== $load ) {
				defined( 'VSP_URL' ) or define( 'VSP_URL', 'https://vsp-framework.pc/wp-content/plugins/vsp-sample-plugin/vsp-framework' );
				require_once $load;
			}
		}
	}
}

new VSP_DEV();

if ( ! function_exists( 'console' ) ) {
	/**
	 * @param mixed ...$arg
	 */
	function console( ...$arg ) {
		error_log( print_r( $arg, true ) );
	}
}