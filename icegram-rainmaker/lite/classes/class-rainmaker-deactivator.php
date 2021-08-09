<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.3.0
 */
if ( ! class_exists( 'Rainmaker_Deactivator' ) ) {
	/**
	 * Fired during plugin deactivation.
	 *
	 * This class defines all code necessary to run during the plugin's deactivation.
	 *
	 * @since      1.3.0
	 */
	class Rainmaker_Deactivator {

		/**
		 * Handles tasks to do on plugin deactivation
		 *
		 * @since 1.3.0
		 */
		public static function deactivate() {
			
			do_action( 'ig_rm_deactivated' );
		}

	}
}