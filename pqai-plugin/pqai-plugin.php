<?php
/**
 * @link     		https://projectpq.ai/
 * @since    		0.1.0
 * @package  		PQAIWordpressPlugin
 */
/*
 * Plugin Name: 	PQAI Wordpress Plugin
 * Plugin URI: 		https://projectpq.ai/
 * Description: 	Plugin for adding PQAI search page to Wordpress websites
 * Version: 		0.1.0
 * Author: 			PQAI Team
 * Author URI: 		https://projectpq.ai/
 * License URI: 	http://www.gnu.org/licenses/gpl-2.0.txt
 * License: 		GPLv2 or later
 * Text Domain: 	pqai-wordpress-plugin
 * Domain Path: 	English
*/

defined( 'ABSPATH' ) or die( 'Wordpress not working properly. Please try again or call suppport!' );

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use Inc\Activate;
use Inc\Deactivate;

if ( !class_exists( 'PQAIWordpressPlugin' ) ) {

	class PQAIWordpressPlugin
	{

		public $plugin;

		function __construct() {
			$this->plugin = plugin_basename( __FILE__ );
		}

		function register() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			add_filter( "plugin_action_links_$this->plugin", 
				array( $this, 'search_link' ) );

		}

		public function search_link( $links ) {
			$search_link = 
				// '<a href="/testsite/wp-content/plugins/pqai-plugin/templates/search.php">PQAI Search</a>';
				'<a href="/wp-content/plugins/pqai-plugin/templates/search.php">PQAI Search</a>';
			array_push( $links, $search_link );
			return $links;
		}

		function enqueue() {
			// enqueue all our scripts
		}


		function activate() {
			Activate::activate();
		}
	}

	$PQAIWordpressPlugin = new PQAIWordpressPlugin();
	$PQAIWordpressPlugin->register();

	// activation
	register_activation_hook( __FILE__, array( $PQAIWordpressPlugin, 'activate' ) );

	// deactivation
	register_deactivation_hook( __FILE__, array( 'Deactivate', 'deactivate' ) );

}