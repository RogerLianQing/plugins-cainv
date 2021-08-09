<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.3.0
 */
if ( ! class_exists( 'Rainmaker_Activator' ) ) {
	/**
	 * Fired during plugin activation.
	 *
	 * This class defines all code necessary to run during the plugin's activation.
	 *
	 * @since      1.3.0
	 */
	class Rainmaker_Activator {

		/**
		 * Handles tasks to do on plugin activation
		 *
		 * @since    1.3.0
		 */
		public static function activate() {
			
			// Redirect to welcome screen
			delete_option( '_icegram_rm_activation_redirect' );
			add_option( '_icegram_rm_activation_redirect', 'pending' );

			do_action( 'ig_rm_activated' );
		}
	}
}