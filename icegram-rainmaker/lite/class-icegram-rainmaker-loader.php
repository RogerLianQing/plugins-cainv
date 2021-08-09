<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Rainmaker_Loader' ) ) {

	class Rainmaker_Loader {
		/**
		 * RM instance
		 *
		 *
		 * @var Rainmaker_Loader The one true Rainmaker_Loader
		 *
		 */
		private static $instance;

		/**
		 * Return a true instance of a class
		 *
		 * @return Rainmaker_Loader
		 *
		 * @since 1.3.0
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Rainmaker_Loader ) ) {
				self::$instance = new Rainmaker_Loader();
			}

			return self::$instance;
		}

		/**
		 * Load required files
		 *
		 * @param string $plugin_path Plugin path from which files to load.
		 * 
		 * @since 1.3.0
		 */
		public function load_dependencies( $plugin_path = '' ) {

			if ( ! empty( $plugin_path ) ) {
				$files_to_load = array(
					$plugin_path . 'plus/icegram-rainmaker-premium.php',
				);
	
				foreach ( $files_to_load as $file ) {
					if ( is_file( $file ) ) {
						require_once $file;
					}
				}
			}
		}

	}
}