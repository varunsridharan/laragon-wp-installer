<?php
define( 'INSTALLER_PATH', __DIR__ . '/' );
define( 'PATH', __DIR__ );
define( 'MYSQL_USER', 'mysqldump' );
define( 'MYSQL_PASS', 'mysqldump' );

/**
 * Class WP_Installer
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WP_Installer {
	protected $install_path   = false;
	protected $template_wp    = false;
	protected $template_db    = false;
	protected $db_name        = false;
	protected $install_folder = false;
	protected $laragon_apache = false;
	protected $domain         = false;

	/**
	 * WP_Installer constructor.
	 */
	public function __construct() {
		if ( count( array_filter( $_POST ) ) < 7 ) {
			echo 'Required Arguments Are Missing';
			return false;
		}
		if ( empty( $_POST['install_path'] ) ) {
			echo 'Invalid Install Path';
			return false;
		}

		$response             = array();
		$this->install_path   = self::slashit( $_POST['install_path'] );
		$this->install_folder = basename( $this->install_path );
		$this->template_wp    = self::slashit( $_POST['template_wp_path'] );
		$this->template_db    = $_POST['template_db_name'];
		$this->db_name        = $_POST['db_name'];
		$this->domain         = $_POST['install_domain'];
		$this->laragon_apache = $_POST['laragon_apache_path'];

		$this->full_path          = self::slashit( self::slashit( PATH ) . $this->install_path );
		$this->template_full_path = self::slashit( self::slashit( PATH ) . $this->template_wp );

		// Creates Directory If Not Exists.
		@mkdir( $this->full_path, 0777, true );

		// Copy Template From template to main.
		$response[] = shell_exec( 'cp -r ' . $this->template_full_path . '* ' . $this->full_path );

		// Creates New Database.
		$response[] = shell_exec( 'mysql -u ' . MYSQL_USER . ' --password=' . MYSQL_PASS . ' -e "create database ' . $this->db_name . '" ' );

		// Copy Database
		$response[] = shell_exec( 'mysqldump --user=' . MYSQL_USER . ' ' . $this->template_db . ' > ' . $this->template_db . '.sql' );

		// Restores Backup DB.
		$response[] = shell_exec( 'mysql -u ' . MYSQL_USER . ' --password=' . MYSQL_PASS . ' ' . $this->db_name . ' < ' . $this->template_db . '.sql' );

		// Deletes Backup File.
		unlink( $this->template_db . '.sql' );

		// Deletes wp-config.php
		unlink( $this->full_path . 'wp-config.php' );

		// Create WP-Config.php
		$this->wpconfig();

		// Creates Apache Config.
		$this->apache();

		// Creates Host File.
		$this->hosts_files();

		var_dump( $response );

		// Reload Apache In Laragon
		//$response[] = shell_exec( self::slashit( $_POST['laragon_path'] ) . 'laragon reload apache' );
	}

	/**
	 * Creates WordPress Config.
	 */
	protected function wpconfig() {
		$content_replace = array(
			'{DB_USER}'      => 'root',
			'{DB_NAME}'      => $this->db_name,
			'{domains}'      => $this->domain,
			'{DB_PASSWORD}'  => '',
			'{AUTH_KEYS}'    => '',
			'{table_prefix}' => 'wp_',
		);
		$content         = str_replace( array_keys( $content_replace ), array_values( $content_replace ), file_get_contents( INSTALLER_PATH . 'sample-wp-config.php' ) );
		file_put_contents( $this->full_path . 'wp-config.php', $content );
	}

	/**
	 * Creates Apache Config.
	 */
	protected function apache() {
		$content_replace = array(
			'{WEB_PATH}' => str_replace( '\\', '/', $this->full_path ),
			'{DOMAIN}'   => $this->domain,
			'{Alias}'    => '',
			'{SSL_CERT}' => '
	SSLEngine on
	SSLCertificateFile      E:/localhost/etc/ssl/laragon.crt
	SSLCertificateKeyFile   E:/localhost/etc/ssl/laragon.key',
		);
		$config          = str_replace( array_keys( $content_replace ), array_values( $content_replace ), file_get_contents( INSTALLER_PATH . 'apache-vhosts.conf' ) );
		file_put_contents( self::slashit( $this->laragon_apache ) . $this->domain . '.conf', $config );

	}

	/**
	 * Updates Windows Hosts File.
	 */
	protected function hosts_files() {
		$domain  = $this->domain;
		$content = <<<HOSTS

127.0.0.1		$domain # VSP Magic !

HOSTS;
		$_c      = file_get_contents( $_POST['hosts_file'] );
		$_c      .= $content;
		file_put_contents( $_POST['hosts_file'], $_c );
	}

	/**
	 * @param $string
	 *
	 * @static
	 * @return string
	 */
	public static function unslashit( $string ) {
		return rtrim( rtrim( $string, '\\' ), '/' );
	}

	/**
	 * @param $string
	 *
	 * @static
	 * @return string
	 */
	public static function slashit( $string ) {
		return self::unslashit( $string ) . '/';
	}
}


?>
<title>Laragon Quick WP Installer</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
	  integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
		integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
		crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
		integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
		crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
		integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
		crossorigin="anonymous"></script>
<div class="container" style="margin-top:30px;">
	<form method="post">

		<div class="accordion" id="installer_form">

			<?php

			if ( isset( $_REQUEST['install_path'] ) ) {
				?>
				<div class="card">
					<div class="card-header" id="results1">
						<h2 class="mb-0">
							<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#resultd">
								Result : WP Install
							</button>
						</h2>
					</div>

					<div id="resultd" class="collapse show" aria-labelledby="results1" data-parent="#installer_form">
						<div class="card-body">
							<?php
							var_dump( $_POST );

							new WP_Installer();
							?>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="card">
				<div class="card-header" id="headingOne">
					<h2 class="mb-0">
						<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#iconfig">
							New WP Install Config
						</button>
					</h2>
				</div>

				<div id="iconfig" class="collapse show" aria-labelledby="headingOne" data-parent="#installer_form">
					<div class="card-body">
						<div class="form-group">
							<label>Install Path</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend"><span
											class="input-group-text"><?php echo PATH . '/'; ?></span></div>
								<input type="text" name="install_path" class="form-control" placeholder="Install Path">
							</div>
						</div>

						<div class="form-group">
							<label>Database Name.</label>
							<input type="text" name="db_name" class="form-control" placeholder="wp_wc_">
						</div>

						<div class="form-group">
							<label>Domain Name</label>
							<input type="text" name="install_domain" class="form-control" placeholder="someplace.pc">
						</div>

						<div class="form-group">
							<label>Domain Alias</label>
							<input type="text" name="install_domain_alias" class="form-control"
								   placeholder="somedomain.com,abc.com">
						</div>
					</div>
				</div>
			</div>


			<div class="card">
				<div class="card-header" id="heading2">
					<h2 class="mb-0">
						<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#tplconfig">
							WP Template Config
						</button>
					</h2>
				</div>

				<div id="tplconfig" class="collapse" aria-labelledby="headingOne" data-parent="#installer_form">
					<div class="card-body">
						<div class="form-group">
							<label>Template WordPress Path</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend"><span
											class="input-group-text"><?php echo PATH . '/'; ?></span></div>
								<input type="text" name="template_wp_path" class="form-control" value="wp/template">
							</div>
						</div>

						<div class="form-group">
							<label>Template WP Database.</label>
							<input type="text" name="template_db_name" class="form-control" value="wp_template">
						</div>
					</div>
				</div>
			</div>


			<div class="card">
				<div class="card-header" id="heading3">
					<h2 class="mb-0">
						<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#laragoncfg">
							Laragon Config
						</button>
					</h2>
				</div>

				<div id="laragoncfg" class="collapse" aria-labelledby="headingOne" data-parent="#installer_form">
					<div class="card-body">
						<div class="form-group">
							<label>laragon path</label>
							<input type="text" name="laragon_path" class="form-control" value="E:\localhost">
						</div>

						<div class="form-group">
							<label>Apache (sites-enabled).conf laragon path</label>
							<input type="text" name="laragon_apache_path" class="form-control"
								   value="E:\localhost\etc\apache2\sites-enabled">
						</div>

						<div class="form-group">
							<label>Hosts File</label>
							<input type="text" name="hosts_file" class="form-control"
								   value="C:\Windows\System32\drivers\etc\hosts">
						</div>
					</div>
				</div>
			</div>
		</div>


		<button name="generate_wp_install" type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
