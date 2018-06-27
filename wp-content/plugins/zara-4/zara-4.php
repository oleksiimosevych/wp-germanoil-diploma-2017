<?php
/*
 * Plugin Name: Zara 4
 * Plugin URI: https://zara4.com
 * Description: Compress your images.
 * Author: Zara 4
 * Version: 1.1.18
 * Author URI: https://zara4.com
 * License GPL2
 */
use Zara4\WordPress\Settings;

if( ! class_exists( 'Zara4_WordPressPlugin' ) ) {


	/**
	 * Class Zara4_WordPressPlugin
	 *
	 * @author	support@zara4.com
	 * @version 1.1.18
	 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GNU General Public License, version 2
	 */
	define( 'ZARA4_DEV', isset( $_SERVER['ZARA4_DEV'] ) && $_SERVER['ZARA4_DEV'] );
	define( 'ZARA4_VERSION', '1.1.18' );
	class Zara4_WordPressPlugin {

		const SETTINGS_OPTION_NAME = '_zara4_settings';
		const OPTIMISATION_OPTION_NAME = '_zara4_optimisation';

		private $settings = array();




		/**
		 * Construct a new Zara4_WordPressPlugin
		 */
		function __construct() {

			//
			// Import libraries
			//
			$plugin_directory = dirname( __FILE__ );

			require_once( $plugin_directory . '/lib/API/Communication/Util.php' );
			require_once( $plugin_directory . '/lib/API/Exception.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Exception.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Config.php' );
			require_once( $plugin_directory . '/lib/API/Communication/AccessDeniedException.php' );
			require_once( $plugin_directory . '/lib/API/Communication/AccessToken/AccessToken.php' );
			require_once( $plugin_directory . '/lib/API/Communication/AccessToken/RefreshableAccessToken.php' );
			require_once( $plugin_directory . '/lib/API/Communication/AccessToken/ReissuableAccessToken.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Authentication/Authenticator.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Authentication/ApplicationAuthenticator.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Authentication/UserAuthenticator.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Grant/GrantRequest.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Grant/ClientCredentialsGrantRequest.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Grant/PasswordGrant.php' );
			require_once( $plugin_directory . '/lib/API/Communication/Grant/RefreshTokenGrant.php' );
			require_once( $plugin_directory . '/lib/API/ImageProcessing/Image.php' );

			require_once( $plugin_directory . '/lib/WordPress/Settings.php' );



			//
			// Load Settings
			//
			$this->settings = new Zara4\WordPress\Settings();





			// Settings page
			add_action( 'admin_menu', array( &$this, 'menu' ) );

			// Add settings link to plugin
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'add_settings_link' ) );

			// Enqueue assets used by Zara 4
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_assets' ) );


			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---

			//
			// Notices
			//
			if(!$this->settings->has_api_credentials()) {
				add_action( 'admin_notices', array( &$this, 'add_admin_notice__continue_setup' ) );
			}


			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---

			//
			// Dashboard Widget - coming soon
			//
			//if($this->settings->has_api_credentials()) {
			//	add_action( 'wp_dashboard_setup', array( &$this, 'add_zara4_widget' ) );
			//}


			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---

			//
			// Media Columns
			//

			// Specify additional media columns
			add_filter( 'manage_media_columns', array( &$this, 'add_media_columns' ) );

			// Add media row data for new columns
			add_action( 'manage_media_custom_column', array( &$this, 'fill_media_columns' ), 10, 2 );


			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---

			//
			// AJAX routes
			//

			add_action( 'wp_ajax_zara4_optimise', array( &$this, 'ajax_optimise' ) );
			add_action( 'wp_ajax_zara4_restore_original', array( &$this, 'ajax_restore_original' ) );
			add_action( 'wp_ajax_zara4_delete_original', array( &$this, 'ajax_delete_original' ) );
			add_action( 'wp_ajax_zara4_uncompressed_images', array( &$this, 'ajax_uncompressed_images' ) );
			add_action( 'wp_ajax_zara4_delete_all_original', array( &$this, 'ajax_delete_all_original' ) );
			add_action( 'wp_ajax_zara4_backed_up_images', array( &$this, 'ajax_backed_up_images' ) );


			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---

			//
			// Event hooks
			//

			// This function generates metadata for an image attachment. It also creates a thumbnail and other intermediate
			// sizes of the image attachment based on the sizes defined on the Settings_Media_Screen.
			// See https://codex.wordpress.org/Function_Reference/wp_generate_attachment_metadata
			add_filter( 'wp_generate_attachment_metadata', array( &$this, 'handle_event__upload_attachment'), 10, 2 );


			// Handle when an attachment (including media image) is deleted
			add_action( 'delete_attachment', array( &$this, 'handle_event__delete_attachment' ) );

		}


		/**
		 * Add the Zara 4 settings link under the WordPress settings section.
		 */
		function menu() {
			add_options_page( 'Zara 4 Settings', 'Zara 4', 'manage_options', 'zara-4', array( &$this, 'settings_page' ) );
		}


		/**
		 * Add a 'Settings' link to the plugins page for Zara 4.
		 *
		 * @param $links
		 * @return array
		 */
		function add_settings_link ( $links ) {
			return array_merge( $links, array(
				'<a href="' . admin_url( 'options-general.php?page=zara-4' ) . '">Settings</a>',
			) );
		}




		/**
		 *
		 *
		 * @param $clientId
		 * @param $clientSecret
		 * @return string
		 */
		function generate_access_token( $clientId, $clientSecret ) {

			$url = \Zara4\API\Communication\Util::url( '/oauth/access_token' );

			$fields = array(
				'client_id'     => urlencode( $clientId ),
				'client_secret' => urlencode( $clientSecret ),
				'grant_type'    => urlencode( 'client_credentials' ),
				'scope'         => 'image-processing,usage',
			);

			$result = json_decode( \Zara4\API\Communication\Util::post( $url, $fields ) );

			//$fields_string = http_build_query( $fields );

			//$ch = curl_init();

			//curl_setopt( $ch, CURLOPT_URL, $url );
			//curl_setopt( $ch, CURLOPT_CAINFO, __DIR__.DIRECTORY_SEPARATOR.'cacert.pem');
			//curl_setopt( $ch, CURLOPT_CAPATH, __DIR__.DIRECTORY_SEPARATOR.'cacert.pem');
			//curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			//curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
			//curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );

			//$result = json_decode( curl_exec( $ch ) );

			//curl_close( $ch );

			return $result->{'access_token'};
		}


		/**
		 * Handle post of settings_page.
		 *
		 * @return boolean
		 */
		private function post_settings_page() {

			// Clear settings
			if ( isset( $_POST['clear-settings'] ) && $_POST['clear-settings'] ) {
				Settings::clear();
				$this->settings = new Settings();
				return true;
			}


			$options = $_POST['_zara4_settings'];
			$imageSizes = Settings::thumbnail_size_names();

			// --- --- ---

			// Only save other settings if already have api credentials (since they are hidden when no api credentials)
			if ( $this->settings->has_api_credentials() ) {

				// Auto optimise
				$this->settings->set_auto_optimise( $options['auto-optimise'] );

				// Back up original images
				$this->settings->set_back_up_original_images( $options['back-up-original-image'] );

				// Image sizes
				foreach ( $imageSizes as $name ) {
					$this->settings->set_image_size_should_be_compressed(
						$name, isset( $options['compress-size'][$name] ) ? $options['compress-size'][$name] : false
					);
				}

				// Force original image
				$this->settings->set_image_size_should_be_compressed( "original", true );

			}

			// API credentials
			$this->settings->set_api_client_id( trim( $options['api-client-id'] ) );
			$this->settings->set_api_client_secret( trim( $options['api-client-secret'] ) );

			return $this->settings->save();
		}




		/**
		 * The Zara 4 Settings Page
		 */
		function settings_page() {

			global $_wp_additional_image_sizes;

			//
			// Update settings if POST given
			//
			$saved = false;
			if ( ! empty( $_POST ) ) {
				$saved = $this->post_settings_page();
				$this->settings->reload();
			}

			// --- --- --- --- ---

			$api_client_id            = $this->settings->api_client_id();
			$api_client_secret        = $this->settings->api_client_secret();
			$auto_optimise            = $this->settings->auto_optimise();
			$back_up_original_images  = $this->settings->back_up_original_images();

			$accessToken = $this->generate_access_token( $api_client_id, $api_client_secret );
			?>
			<script>
				var ZARA4_API_BASE_URL = "<?php echo \Zara4\API\Communication\Config::BASE_URL() ?>";
				var ZARA4_API_ACCESSTOKEN = "<?php echo $accessToken ?>";
			</script>

			<div class="wrap">

				<div style="margin:30px 0 20px 0">
					<span style="float: right" id="debug-info-btn" class="button">Debug Info</span>
					<h1 style="padding:0">
						<a target="_blank" href="https://zara4.com">
							<img style="height: 25px" src="https://zara4.com/img/logo.png" alt="Zara 4" />
						</a>
					</h1>

				</div>

				<hr/>

				<?php if ( ! empty( $_POST ) ): ?>
					<?php if ( $saved ): ?>
						<div class="zara-4 alert-boxed alert alert-success w-600">
							<b>Settings Saved!</b>
						</div>
					<?php else: ?>
						<div class="zara-4 alert-boxed alert alert-danger w-600">
							<b>Error :</b> Cannot save your settings. Write permission was denied. <a target="_blank" href="https://codex.wordpress.org/Changing_File_Permissions">Read about permissions</a>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<div id="error-message" class="zara-4 alert-boxed alert alert-danger hidden w-600"></div>
				<div id="warning-message" class="zara-4 alert-boxed alert alert-warning hidden w-600"></div>


				<?php if ( ZARA4_DEV ): ?>
					<div id="warning-message" class="zara-4 alert-boxed alert alert-warning w-600">
						<b>Warning :</b> Running in development mode
					</div>
				<?php endif; ?>

				<?php if ( ! function_exists('curl_version') ): ?>
					<div class="zara-4 alert-boxed alert alert-danger w-600">
						<b>Error :</b> Your WordPress server does not have cURL installed.
					</div>
				<?php endif; ?>

				<?php if ( $this->settings->has_api_credentials() ): ?>
					<div id="account-usage-wrapper" class="hidden">
						<h2>Account Usage</h2>
						<iframe style="height: 300px; width: 600px" src="<?php echo \Zara4\API\Communication\Config::BASE_URL() ?>/v1/view/user/usage-graph?access_token=<?php echo $accessToken ?>"></iframe>
						<table class="form-table" style="margin-top: 0">
							<tr>
								<th>
									Allowance Remaining
								</th>
								<td id="allowance-remaining">
								</td>
							</tr>
						</table>
						<hr/>
					</div>
				<?php else: ?>
					<div class="zara-4 alert-boxed alert alert-danger w-600">
						<b>You need to register</b> to get your API credentials - <a target="_blank" href="https://zara4.com/auth/api-register">Click here</a>
					</div>
				<?php endif; ?>


				<h2 style="margin-top: 40px">Settings</h2>
				<form method="post">

					<table class="form-table">

						<tr>
							<th>
								Account Status
							</th>
							<td>
								<p id="zara-4-account-status">
									<img src="<?php echo plugins_url( 'img/loading.gif', __FILE__ ); ?>"/> Please wait...
								</p>
							</td>
						</tr>

						<tr>
							<th>
								API Key
							</th>
							<td>
								<input id="zara4-client-id" name="_zara4_settings[api-client-id]" type="text" size="40" placeholder="Client Id" value="<?php echo esc_attr($api_client_id) ?>">
							</td>
						</tr>

						<tr>
							<th>
								API Secret
							</th>
							<td>
								<input id="zara4-client-secret" name="_zara4_settings[api-client-secret]" type="text" size="40" placeholder="Client Secret" value="<?php echo esc_attr($api_client_secret) ?>">
							</td>
						</tr>

						<?php if ( $this->settings->has_api_credentials() ): ?>
						<tr>
							<th scope="row">Auto optimise uploads</th>
							<td>
								<label>
									<input type="checkbox" id="auto_optimize" name="_zara4_settings[auto-optimise]" value="1" <?php checked( 1, $auto_optimise, true ); ?>/>
									Automatically optimise new uploads
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row">Back up original images</th>
							<td>
								<label>
									<input type="checkbox" id="back-up-original-image" name="_zara4_settings[back-up-original-image]" value="1" <?php checked( 1, $back_up_original_images, true ); ?>/>
									Keep a back up of original uncompressed images
								</label>
								<div class="mt-10 ml-20 hidden" id="delete-all-wrapper">
									(<span class="zara-4 a delete" id="delete-all-btn">Delete all</span> existing backed up images - <span class="number-of-images"></span> can be deleted)
								</div>

							</td>
						</tr>

						<tr>
							<th>
								Compression Sizes
							</th>
							<td>
								<p>
									Select images sizes to compress. Each selected counts towards your quota.
								</p>
								<p>
									<label>
										<input type="checkbox" name="_zara4_settings[compress-size][original]" value="1" disabled="true" <?php checked( 1, $this->settings->image_size_should_be_compressed( 'original' ), true ); ?>>
										Original Image
									</label>
								</p>
								<p>
									<label>
										<input type="checkbox" name="_zara4_settings[compress-size][thumbnail]" value="1" <?php checked( 1, $this->settings->image_size_should_be_compressed( 'thumbnail' ), true ); ?>>
										Thumbnail (150x150)
									</label>
								</p>
								<p>
									<label>
										<input type="checkbox" name="_zara4_settings[compress-size][medium]" value="1" <?php checked( 1, $this->settings->image_size_should_be_compressed( 'medium' ), true ); ?>>
										Medium (300x300)
									</label>
								</p>
								<p>
									<label>
										<input type="checkbox" name="_zara4_settings[compress-size][large]" value="1" <?php checked( 1, $this->settings->image_size_should_be_compressed( 'large' ), true ); ?>>
										Large (1024x1024)
									</label>
								</p>
								<?php if ( is_array( $_wp_additional_image_sizes ) ): ?>
									<?php foreach ( $_wp_additional_image_sizes as $name => $details ) : ?>
										<?php
										$widthIsSet = isset( $details['width'] ) && $details['width'] !== null;
										$heightIsSet = isset( $details['height'] ) && $details['height'] !== null;
										$width = $widthIsSet ? $details['width'] : null;
										$height = $heightIsSet ? $details['height'] : null;
										?>
										<p>
											<label>
												<input type="checkbox" name="_zara4_settings[compress-size][<?php echo $name ?>]" value="1" <?php checked( 1, $this->settings->image_size_should_be_compressed( $name ), true ); ?>>
												<?php echo ucwords(str_replace("-", " ", $name)); ?>
												<?php if ( $widthIsSet && $heightIsSet ): ?>
												(<?php echo $width ?>x<?php echo $height ?>)
												<?php endif; ?>
											</label>
										</p>
									<?php endforeach; ?>
								<?php endif; ?>
							</td>
						</tr>
						<?php endif; ?>

					</table>

					<button type="submit" class="button button-primary">Save Settings</button>
					<button type="submit" value="1" name="clear-settings" class="button button-default">Clear Settings</button>

				</form>






				<div id="zara-4-info-modal" class="zara-4" style="display: none">
					<h2>Debug Info</h2>

					<table class="zara-4 table">
						<tr>
							<td style="width: 130px"><b>Zara 4 Version</b></td>
							<td><?php echo ZARA4_VERSION; ?></td>
						</tr>
						<tr>
							<td><b>Zara 4 Settings</b></td>
							<td><?php echo $this->settings; ?></td>
						</tr>
						<tr>
							<td><b>Zara 4 Mode</b></td>
							<td><?php echo ZARA4_DEV ? 'Development' : 'Production' ?></td>
						</tr>
					</table>
					<hr/>
					<table class="zara-4 table">
						<tr>
							<td style="width: 130px"><b>WordPress Version</b></td>
							<td><?php echo get_bloginfo( 'version' ); ?></td>
						</tr>
						<tr>
							<td><b>PHP Version</b></td>
							<td><?php echo phpversion(); ?></td>
						</tr>
						<tr>
							<td><b>PHP Extensions</b></td>
							<td><?php echo implode(', ', get_loaded_extensions()); ?></td>
						</tr>
						<tr>
							<td><b>Machine Info</b></td>
							<td><?php echo php_uname(); ?></td>
						</tr>
					</table>

					<div class="text-center mt-15">
						<a href="#" class="button button-primary" rel="modal:close">Close</a>
					</div>

				</div>




			</div>
		<?php
		}


		/**
		 * Enqueue assets (css and js)
		 *
		 * @param $hook
		 */
		function enqueue_assets( $hook ) {
			$isMediaPage = $hook == 'upload.php';
			$isSettingsPage = $hook == 'settings_page_zara-4';

			if ( $isMediaPage || $isSettingsPage ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_style( 'zara-4-css', plugins_url( '/css/zara-4.css', __FILE__ ) );

				// Media Page Imports
				if ( $isMediaPage ) {
					wp_enqueue_script( 'media-page', plugins_url( '/js/media-page.js', __FILE__ ), array( 'jquery' ) );
					wp_localize_script( 'media-page', 'LOADING_URL', plugins_url( 'img/loading.gif', __FILE__ ) );

					wp_enqueue_script( 'modal-js', plugins_url( '/packages/jquery-modal/v0.7.0/modal.min.js', __FILE__ ), array( 'jquery' ) );
					wp_enqueue_style( 'modal-css', plugins_url( '/packages/jquery-modal/v0.7.0/modal.css', __FILE__ ) );
				}

				// Settings Page Imports
				if ( $isSettingsPage ) {
					wp_enqueue_script( 'settings-page', plugins_url( '/js/settings-page.js', __FILE__ ), array( 'jquery' ) );
					wp_enqueue_script( 'chart-js', plugins_url( '/packages/chart-js/v1.0.2/Chart.min.js', __FILE__ ), array( 'jquery' ) );

					wp_enqueue_script( 'modal-js', plugins_url( '/packages/jquery-modal/v0.7.0/modal.min.js', __FILE__ ), array( 'jquery' ) );
					wp_enqueue_style( 'modal-css', plugins_url( '/packages/jquery-modal/v0.7.0/modal.css', __FILE__ ) );
				}
			}
		}





		/*
		 *                _             _            _   _         _    _
		 *      /\       | |           (_)          | \ | |       | |  (_)
		 *     /  \    __| | _ __ ___   _  _ __     |  \| |  ___  | |_  _   ___  ___  ___
		 *    / /\ \  / _` || '_ ` _ \ | || '_ \    | . ` | / _ \ | __|| | / __|/ _ \/ __|
		 *   / ____ \| (_| || | | | | || || | | |   | |\  || (_) || |_ | || (__|  __/\__ \
		 *  /_/    \_\\__,_||_| |_| |_||_||_| |_|   |_| \_| \___/  \__||_| \___|\___||___/
		 *
		 */

		/**
		 *
		 */
		function add_admin_notice__continue_setup() {
			$settings_url = admin_url( 'options-general.php' );
			$settings_url_parts = parse_url($settings_url);

			$protocol = $_SERVER["HTTPS"] == "on" ? "https" : "http";
			$current_url = $protocol."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			$current_url_parts = parse_url($current_url);

			$on_zara4_settings_page = $current_url_parts["path"] == $settings_url_parts["path"] && $_GET["page"] == "zara-4";


			if(!$on_zara4_settings_page) {
				?>
				<div class="notice notice-warning is-dismissible">
					<p>
						<b style="color:#777">Zara 4</b> needs to be setup before you can compress your images.
						<a href="<?php echo admin_url( 'options-general.php?page=zara-4' ); ?>">Continue Setup</a>
					</p>
				</div>
			<?php
			}
		}



		/*
		 *   _____               _      _                             _
		 *  |  __ \             | |    | |                           | |
		 *  | |  | |  __ _  ___ | |__  | |__    ___    __ _  _ __  __| |
		 *  | |  | | / _` |/ __|| '_ \ | '_ \  / _ \  / _` || '__|/ _` |
		 *  | |__| || (_| |\__ \| | | || |_) || (_) || (_| || |  | (_| |
		 *  |_____/  \__,_||___/|_| |_||_.__/  \___/  \__,_||_|   \__,_|
		 *
		 */

		function add_zara4_widget() {
			wp_add_dashboard_widget('zara4_widget', 'Zara 4', function() {
				echo 'Hello world';
			});
		}



		/*
		 *   __  __            _  _             _____        _
		 *  |  \/  |          | |(_)           / ____|      | |
		 *  | \  / |  ___   __| | _   __ _    | |      ___  | | _   _  _ __ ___   _ __   ___
		 *  | |\/| | / _ \ / _` || | / _` |   | |     / _ \ | || | | || '_ ` _ \ | '_ \ / __|
		 *  | |  | ||  __/| (_| || || (_| |   | |____| (_) || || |_| || | | | | || | | |\__ \
		 *  |_|  |_| \___| \__,_||_| \__,_|    \_____|\___/ |_| \__,_||_| |_| |_||_| |_||___/
		 *
		 */


		/**
		 * Add 'Original Size' and 'Zara 4 Size' columns to media table.
		 *
		 * @param $columns
		 * @return array
		 */
		function add_media_columns( $columns ) {
			$columns['original_size'] = 'Original Size';
			$columns['zara4_size'] = 'Zara 4 Size';
			return $columns;
		}


		/**
		 * Fill the media library columns added by Zara 4.
		 *
		 * @param $column_name
		 * @param $id
		 */
		function fill_media_columns( $column_name, $id ) {

			//
			// 'Original Size' Column
			//
			if ( 'original_size' == $column_name ) {
				$file_path = get_attached_file( $id );
				$backup_file_path = self::generate_backup_path( $file_path );
				$original_file_path = file_exists( $backup_file_path ) ? $backup_file_path : $file_path;
				echo '<span id="zara4-original-size-' . $id . '">' . self::format_bytes( filesize( $original_file_path ) ) . '</span>';
			}


			//
			// 'Zara 4 Size' Column
			//
			else if ( 'zara4_size' == $column_name ) {
				if ( wp_attachment_is_image( $id ) ) {

					$file_path = get_attached_file( $id );

					$meta = get_post_meta( $id, '_zara4_optimisation', true );
					$optimised = isset( $meta['bytes_compressed'] );

					$compressed_size   = isset( $meta['bytes_compressed'] ) ? $meta['bytes_compressed'] : null;
					$percentage_saving = isset( $meta['percentage_saving'] ) ? $meta['percentage_saving'] : null;

					// Ensure display data is available.
					$optimised = ( $compressed_size != null ) && ( $percentage_saving != null )
						? $optimised : false;

					// --- --- ---

					echo '<div class="zara-4 size-column" id="zara4-optimise-wrapper-' . $id . '">';

					// Loading
					echo '<div class="loading-wrapper hidden"><img src="' . plugins_url( 'img/loading.gif', __FILE__ ) . '"/> Please wait</div>';


					// Optimised stats and restore link
					echo '<div class="restore-original-wrapper' . ($optimised ? '' : ' hidden') . '">';
						echo '<div class="compressed-size">' . self::format_bytes( $compressed_size ) . '</div>';
						echo '<div>Saved <span class="percentage-saving">' . number_format( floatval( $percentage_saving ), 1 ) . '</span>%</div>';

						if( self::has_backup_image( $file_path ) ) {
							echo '<div class="mt-5 zara-4 original-image-group">';
								echo '<div><span data-id="' . $id . '" class="zara-4 link restore-original">Restore original</span></div>';
								echo '<div><span data-id="' . $id . '" class="delete zara-4 link delete-original">Delete original</span></div>';
							echo '</div>';
						}

					echo '</div>';



					// Optimise button
					echo '<div class="optimise-wrapper' . ($optimised ? ' hidden' : '') . '">';
						echo '<button id="zara4-optimise-btn-' . $id . '" type="button" data-id="' . $id . '" class="zara-4 optimise button button-primary">Optimise Now</button>';
					echo '</div>';


					echo '</div>';
				}
			}

		}


		/*
		 *
		 *   ______                   _       _    _                _
		 *  |  ____|                 | |     | |  | |              | |
		 *  | |__ __   __ ___  _ __  | |_    | |__| |  ___    ___  | | __ ___
		 *  |  __|\ \ / // _ \| '_ \ | __|   |  __  | / _ \  / _ \ | |/ // __|
		 *  | |____\ V /|  __/| | | || |_    | |  | || (_) || (_) ||   < \__ \
		 *  |______|\_/  \___||_| |_| \__|   |_|  |_| \___/  \___/ |_|\_\|___/
		 *
		 */


		/**
		 * @param $attachment_id
		 */
		function handle_event__delete_attachment( $attachment_id ) {
			if ( wp_attachment_is_image( $attachment_id ) ) {
				$this->delete_image_from_id( $attachment_id );
			}
		}


		/**
		 *
		 *
		 * @param $data
		 * @param null $image_id
		 * @return mixed
		 */
		function handle_event__upload_attachment( $data, $image_id = null ) {
			if ( wp_attachment_is_image( $image_id ) ) {

				if ( $this->settings->auto_optimise() && $this->settings->has_api_credentials() ) {

					//
					// Optimise Thumbnails
					//
					$sizes = isset( $data['sizes'] ) ? $data['sizes'] : array();
					if ( ! empty( $sizes ) ) {
						$this->optimise_thumbnails_from_sizes( $sizes );
					}


					//
					// Optimise Original
					//
					$upload_dir_info = wp_upload_dir();
					if( isset( $data['file'] ) ) {
						$image_path = $upload_dir_info['basedir'] . "/" . $data['file'];
						$response = $this->optimise_image_from_path( $image_path );
						$this->set_image_optimisation_meta_data( $image_id, $response );
					}

				}


			}
			return $data;
		}











		/**
		 * Replace an image with a url
		 *
		 * @param $image_path
		 * @param $url
		 * @param null $access_token
		 */
		function replace_image( $image_path, $url , $access_token = null) {
			file_put_contents( $image_path, \Zara4\API\Communication\Util::get( $url, array('access_token' => $access_token) ) );
		}




		/**
		 * Set the optimisation meta data for the image with the given id.
		 *
		 * @param $image_id
		 * @param $response
		 */
		function set_image_optimisation_meta_data( $image_id, $response ) {

			if ( ! $response ) { return; }
			$request_id = isset( $response->{'request-id'} ) ? $response->{'request-id'} : null;

			$compressionData = isset( $response->{'compression'} ) ? $response->{'compression'} : null;
			if ( ! $compressionData ) { return; }


			$bytes_original = isset( $response->{'compression'}->{'bytes-original'} ) ? $response->{'compression'}->{'bytes-original'} : null;
			$bytes_compressed = isset( $response->{'compression'}->{'bytes-compressed'} ) ? $response->{'compression'}->{'bytes-compressed'} : null;
			$bytes_saving = isset( $response->{'compression'}->{'bytes-saving'} ) ? $response->{'compression'}->{'bytes-saving'} : null;
			$percentage_saving = isset( $response->{'compression'}->{'percentage-saving'} ) ? $response->{'compression'}->{'percentage-saving'} : null;

			$meta = array(
				'request_id'        => $request_id,
				'bytes_original'    => $bytes_original,
				'bytes_compressed'  => $bytes_compressed,
				'bytes_saving'      => $bytes_saving,
				'percentage_saving' => $percentage_saving,
				'meta'              => wp_get_attachment_metadata( $image_id ),
			);

			update_post_meta( $image_id, self::OPTIMISATION_OPTION_NAME, $meta );
		}


		/**
		 * Is the given image id optimised?
		 *
		 * @param $image_id
		 * @return bool
		 */
		function is_optimised( $image_id ) {
			$meta_data = wp_get_attachment_metadata( $image_id );
			if ( ! $meta_data ) { return false; }

			$file = $meta_data['file'];
			if ( ! $file ) { return false; }


			$file_path = get_attached_file( $image_id );

			$meta = get_post_meta( $image_id, '_zara4_optimisation', true );
			//$optimised = isset( $meta['bytes_compressed'] );

			return $meta != null;


			// NOTE: wp_upload_dir()['basedir'] is not supported until after PHP v5.4
			//       temp variable $dir_info must be used for compatibility.
			//$dir_info = wp_upload_dir();
			//$upload_dir = $dir_info['basedir'];
			//$path = $upload_dir . '/' . $file;
			//$backup_path = self::generate_backup_path($path);

			//return file_exists( $backup_path );
		}


		/**
		 * Optimise an image from the given path.
		 *
		 * @param $path
		 * @return mixed
		 */
		function optimise_image_from_path( $path ) {

			$access_token = null;

			$api_client_id = $this->settings->api_client_id();
			$api_client_secret = $this->settings->api_client_secret();


			$params = array();


			if ( $api_client_id && $api_client_secret ) {
				$access_token = self::generate_access_token( $api_client_id, $api_client_secret );
				$params['access_token'] = $access_token;
			}

			$response = json_decode( \Zara4\API\ImageProcessing\Image::optimise_image_from_file( $path, $params ) );


			// --- --- ---


			if( isset( $response->{'error'} ) ) {
				return $response;
			}


			$url = isset( $response->{'generated-images'}->{'urls'}[0] ) ? $response->{'generated-images'}->{'urls'}[0] : null;
			if ( $url ) {

				// Make a back up file
				if($this->settings->back_up_original_images()) {
					self::backup_image( $path );
				}

				$this->replace_image( $path, $url, $access_token );
			}

			return $response;
		}


		/**
		 * Optimise an image from the given WordPress id.
		 *
		 * @param $image_id
		 * @return array
		 */
		private function optimise_image_from_id( $image_id ) {
			if ( wp_attachment_is_image( $image_id ) ) {

				self::validate_current_user_capabilities();
				self::validate_image_id( $image_id );

				// Catch where the image is already optimised.
				if ( $this->is_optimised( $image_id ) ) {
					$all_meta_data = get_post_meta( $image_id, self::OPTIMISATION_OPTION_NAME );
					$meta_data = $all_meta_data[0];
					return self::generate_ajax_upload_response(
						$meta_data["request_id"], $meta_data["bytes_compressed"], $meta_data["bytes_original"],
						$meta_data["bytes_saving"], $meta_data["percentage_saving"]
					);
				}


				$image = get_post( $image_id );

				$full_size_path = get_attached_file( $image->ID );

				self::validate_file_path( $full_size_path );

				// Limit execution to 900 seconds (15 minutes)
				@set_time_limit( 900 );

				// --- --- --- ---

				/*
				 * Thumbnails should be optimised first
				 */

				//
				// Optimise Thumbnail Images
				//
				$metadata = wp_get_attachment_metadata( $image->ID );
				$metadata_sizes = isset( $metadata['sizes'] ) ? $metadata['sizes'] : null;
				if ( $metadata_sizes != null && is_array( $metadata_sizes ) ) {
					$this->optimise_thumbnails_from_sizes( $metadata_sizes );
				}



				//
				// Optimise Original Image
				//
				$response = $this->optimise_image_from_path( $full_size_path );
				if(!isset($response->{'error'})) {
					$this->set_image_optimisation_meta_data( $image_id, $response );
				}

				return $response;

			}
			return false;
		}


		/**
		 * @param array $sizes
		 */
		private function optimise_thumbnails_from_sizes( array $sizes ) {
			$upload_dirs = wp_upload_dir();
			$upload_dir = $upload_dirs['path'];
			foreach( $sizes as $size_name => $size ) {

				// Only process enabled image sizes
				if ( $this->settings->image_size_should_be_compressed( $size_name ) ) {
					$path = $upload_dir . '/' . $size["file"];
					if ( file_exists( $path ) !== false ) {
						$this->optimise_image_from_path( $path );
					}
				}
			}
		}


		/**
		 * @param $image_id
		 */
		function delete_image_from_id( $image_id ) {
			if ( wp_attachment_is_image($image_id) ) {

				// Original file path
				$paths = array( get_attached_file( $image_id ) );

				// Thumbnail file paths (excludes original)
				$metadata = wp_get_attachment_metadata( $image_id );
				$upload_dirs = wp_upload_dir();
				$upload_dir = $upload_dirs['path'];
				foreach ( $metadata['sizes'] as $size ) {
					$paths[] = $upload_dir . '/' . $size["file"];
				}

				// Delete each backup file (if exists)
				foreach ( $paths as $path ) {
					$backup_image_path = $this->generate_backup_path( $path );
					if ( file_exists( $backup_image_path ) ) {
						unlink( $backup_image_path );
					}
				}

			}
		}


		/**
		 * Removes Zara 4 images and restores originals (if possible)
		 *
		 * @param $image_id
		 * @throws Exception
		 */
		private function restore_original_image_from_id( $image_id ) {
			if ( wp_attachment_is_image($image_id) ) {

				self::validate_current_user_capabilities();
				self::validate_image_id( $image_id );

				//$image_path = get_attached_file( $image_id );
				$image = get_post( $image_id );
				$full_size_path = get_attached_file( $image->ID );

				self::validate_file_path( $full_size_path );

				// Limit execution to 900 seconds (15 minutes)
				@set_time_limit( 900 );

				// --- --- --- ---


				//
				// Restore Original Image
				//
				self::restore_image( $full_size_path );


				//
				// Restore Thumbnail Images
				//
				$metadata = wp_get_attachment_metadata( $image->ID );
				$upload_dirs = wp_upload_dir();
				$upload_dir = $upload_dirs['path'];
				$metadata_sizes = $metadata['sizes'];
				if ($metadata_sizes) {
					foreach( $metadata_sizes as $data ) {
						self::restore_image( $upload_dir . '/' . $data['file'] );
					}
				}


				// Clear Zara 4 meta data
				update_post_meta( $image_id, self::OPTIMISATION_OPTION_NAME, null );

			}
		}



		/**
		 * Removes Zara 4 images and restores originals (if possible)
		 *
		 * @param $image_id
		 * @throws Exception
		 */
		private function delete_original_image_from_id( $image_id ) {
			if ( wp_attachment_is_image($image_id) ) {

				self::validate_current_user_capabilities();
				self::validate_image_id( $image_id );

				$image = get_post( $image_id );
				$full_size_path = get_attached_file( $image->ID );

				self::validate_file_path( $full_size_path );


				// --- --- --- ---


				//
				// Delete Original Image
				//
				self::delete_original_image( $full_size_path );

			}
		}



		/*
		 *   ____                _        _    _
		 *  |  _ \              | |      | |  | |
		 *  | |_) |  __ _   ___ | | __   | |  | | _ __
		 *  |  _ <  / _` | / __|| |/ /   | |  | || '_ \
		 *  | |_) || (_| || (__ |   <    | |__| || |_) |
		 *  |____/  \__,_| \___||_|\_\    \____/ | .__/
		 *                                       | |
		 *                                       |_|
		 */


		/**
		 * Generate the back up path for the given path.
		 *
		 * @param $path
		 * @return string
		 */
		private static function generate_backup_path( $path ) {
			return $path . '.zara4-backup';
		}


		/**
		 *
		 *
		 * @param $path
		 * @return bool
		 */
		private static function has_backup_image( $path ) {
			return file_exists( self::generate_backup_path( $path ) );
		}


		/**
		 * Backup an image.
		 *
		 * @param $path
		 * @return bool
		 */
		private static function backup_image( $path ) {
			$backup_path = self::generate_backup_path( $path );

			if ( ! file_exists( $path ) ) {
				return false;
			}
			if ( file_exists( $backup_path ) && ! unlink( $backup_path ) ) {
				return false;
			}
			return copy( $path, $backup_path );
		}


		/**
		 * Restore an image.
		 *
		 * @param $path
		 * @return bool
		 */
		private static function restore_image( $path ) {
			$backup_path = self::generate_backup_path( $path );
			if ( ! file_exists( $path ) || ! file_exists( $backup_path ) ) {
				return false;
			}
			if ( ! unlink( $path ) ) {
				return false;
			}
			if ( ! copy( $backup_path, $path ) ) {
				return false;
			}
			return unlink( $backup_path );
		}


		/**
		 * Delete original image.
		 *
		 * @param $path
		 * @return bool
		 */
		private static function delete_original_image( $path ) {
			$backup_path = self::generate_backup_path( $path );
			if ( ! file_exists( $backup_path ) ) {
				return false;
			}
			return unlink( $backup_path );
		}



		/*
		 *  __      __     _  _      _         _    _
		 *  \ \    / /    | |(_)    | |       | |  (_)
		 *   \ \  / /__ _ | | _   __| |  __ _ | |_  _   ___   _ __
		 *    \ \/ // _` || || | / _` | / _` || __|| | / _ \ | '_ \
		 *     \  /| (_| || || || (_| || (_| || |_ | || (_) || | | |
		 *      \/  \__,_||_||_| \__,_| \__,_| \__||_| \___/ |_| |_|
		 *
		 */


		/**
		 * Validate that the given id is for a valid image.
		 *
		 * @param $image_id
		 * @throws Exception
		 */
		private static function validate_image_id( $image_id ) {
			if ( ! wp_attachment_is_image( $image_id ) ) {
				throw new \Exception( $image_id . ' is not a valid image id' );
			}
		}


		/**
		 * Validate that the current user has the required capabilities.
		 *
		 * @throws Exception
		 */
		private static function validate_current_user_capabilities() {
			if ( ! current_user_can( 'manage_options' ) ) {
				throw new \Exception( 'Your user account doesn\'t have permission to manage images' );
			}
		}


		/**
		 * Validate that the given file path exists.
		 *
		 * @param $file_path
		 * @throws Exception
		 */
		private static function validate_file_path( $file_path ) {
			if ( false === $file_path || ! file_exists( $file_path ) ) {
				throw new \Exception( 'The file requested could not be found.' );
			}
		}



		/*
		 *                 _          __   __
		 *      /\        | |   /\    \ \ / /
		 *     /  \       | |  /  \    \ V /
		 *    / /\ \  _   | | / /\ \    > <
		 *   / ____ \| |__| |/ ____ \  / . \
		 *  /_/    \_\\____//_/    \_\/_/ \_\
		 *
		 */


		/**
		 * AJAX call to optimise an image.
		 *
		 * Removes Zara 4 images and restores originals.
		 */
		function ajax_optimise() {
			@error_reporting( 0 );

			header( 'Content-type: application/json' );
			$id = (int) $_REQUEST['id'];

			$response = self::optimise_image_from_id( $id );

			die( json_encode( $response ) );
		}



		/**
		 * AJAX call to restore an image back to it's original (Processes a single image ID).
		 *
		 * Removes Zara 4 images and restores originals.
		 */
		function ajax_restore_original() {
			@error_reporting( 0 );

			header( 'Content-type: application/json' );
			$id = (int) $_REQUEST['id'];

			try {
				$this->restore_original_image_from_id( $id );
			} catch( \Exception $e ) {
				die( json_encode( array(
					'status'  => 'error',
					'message' => $e->getMessage(),
				) ) );
			}

			die( json_encode( array(
				'status' => 'success',
			) ) );
		}


		/**
		 * AJAX call to restore an image back to it's original (Processes a single image ID).
		 *
		 * Removes Zara 4 images and restores originals.
		 */
		function ajax_delete_original() {
			@error_reporting( 0 );

			header( 'Content-type: application/json' );
			$id = (int) $_REQUEST['id'];

			try {
				$this->delete_original_image_from_id( $id );
			} catch( \Exception $e ) {
				die( json_encode( array(
					'status'  => 'error',
					'message' => $e->getMessage(),
				) ) );
			}

			die( json_encode( array(
				'status' => 'success',
			) ) );
		}



		function ajax_uncompressed_images() {

			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => - 1,
			);

			$query_images = new WP_Query( $query_images_args );
			$image_ids = array();
			foreach ( $query_images->posts as $image ) {

				$id = $image->ID;

				$file_path = get_attached_file( $id );

				if(!file_exists($file_path)) {
					continue;
				}

				$optimised = self::is_optimised( $id );

				if(!$optimised) {
					$image_ids[] = $id;
				}
			}

			$image_ids = array_unique( $image_ids );
			sort( $image_ids );

			die( json_encode( $image_ids ) );
		}



		function ajax_backed_up_images() {

			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => - 1,
			);

			$query_images = new WP_Query( $query_images_args );
			$image_ids = array();
			foreach ( $query_images->posts as $image ) {
				try {

					$id = $image->ID;

					$file_path = get_attached_file( $id );

					if(!file_exists($file_path)) {
						continue;
					}

					$meta = get_post_meta( $id, '_zara4_optimisation', true );
					$optimised = isset( $meta['bytes_compressed'] );

					$compressed_size   = isset( $meta['bytes_compressed'] ) ? $meta['bytes_compressed'] : null;
					$percentage_saving = isset( $meta['percentage_saving'] ) ? $meta['percentage_saving'] : null;

					// Ensure display data is available.
					$optimised = ( $compressed_size != null ) && ( $percentage_saving != null )
						? $optimised : false;

					if( $optimised && self::has_backup_image( $file_path ) ) {
						$image_ids[] = $id;
					}

				} catch (\Exception $e) {
					// Keep going
				}
			}

			$image_ids = array_unique( $image_ids );
			sort( $image_ids );

			die( json_encode( $image_ids ) );
		}




		function ajax_delete_all_original() {

			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => - 1,
			);

			$query_images = new WP_Query( $query_images_args );
			$image_ids = array();
			foreach ( $query_images->posts as $image ) {
				try {

					$id = $image->ID;

					$file_path = get_attached_file( $id );

					if( !file_exists( $file_path ) ) {
						continue;
					}

					$meta = get_post_meta( $id, '_zara4_optimisation', true );
					$optimised = isset( $meta['bytes_compressed'] );

					$compressed_size   = isset( $meta['bytes_compressed'] ) ? $meta['bytes_compressed'] : null;
					$percentage_saving = isset( $meta['percentage_saving'] ) ? $meta['percentage_saving'] : null;

					// Ensure display data is available.
					$optimised = ( $compressed_size != null ) && ( $percentage_saving != null )
						? $optimised : false;

					if( $optimised && self::has_backup_image( $file_path ) ) {
						$this->delete_original_image_from_id( $id );
					}

				} catch( \Exception $e ) {
					// Keep going
				}
			}


			die( json_encode( $image_ids ) );
		}



		/**
		 * @param $request_id
		 * @param $bytes_compressed
		 * @param $bytes_original
		 * @param $bytes_saving
		 * @param $percentage_saving
		 * @return array
		 */
		private static function generate_ajax_upload_response(
			$request_id, $bytes_compressed, $bytes_original, $bytes_saving, $percentage_saving
		) {
			return array(
				"status" => "already-optimised",
				"request-id" => $request_id,
				"compression" => array(
					"bytes-compressed" => $bytes_compressed,
					"bytes-original" => $bytes_original,
					"bytes-saving" => $bytes_saving,
					"percentage-saving" => $percentage_saving,
				),
			);
		}




		/*
		 *
		 *   _    _  _    _  _
		 *  | |  | || |  (_)| |
		 *  | |  | || |_  _ | |
		 *  | |  | || __|| || |
		 *  | |__| || |_ | || |
		 *   \____/  \__||_||_|
		 *
		 */


		/**
		 * Format a given number of bytes.
		 *
		 * @param $bytes
		 * @return string
		 */
		private static function format_bytes( $bytes ) {
			$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

			$bytes = max( $bytes, 0 );
			$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
			$pow = min( $pow, count( $units ) - 1 );

			$bytes /= pow( 1024, $pow );

			return round( $bytes, 1 ) . ' ' . $units[$pow];
		}

	}
}


// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---


//
// Boot Zara 4 Plugin
//
new Zara4_WordPressPlugin();