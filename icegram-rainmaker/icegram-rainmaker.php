<?php
/*
 * Plugin Name: Rainmaker - The Best Readymade WP Forms Plugin
 * Plugin URI: http://www.icegram.com/addons/icegram-rainmaker/
 * Description: The complete solution to create simple forms and collect leads
 * Version: 1.3.0
 * Tested up to: 5.8
 * Author: icegram
 * Author URI: http://www.icegram.com/
 *
 * Copyright (c) 2016-21 Icegram
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: icegram-rainmaker
 * Domain Path: lite/lang/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'IG_RM_FEEDBACK_TRACKER_VERSION' ) ) {
	define( 'IG_RM_FEEDBACK_TRACKER_VERSION', '1.2.4' );
}


/* ***************************** Initial Compatibility Work (Start) ******************* */

/* =========== Do not edit this code unless you know what you are doing ========= */

/*
 * Note: We are not using IG_RM_PLUGIN_DIR constant at this moment because there are chances
 * It might be defined from older version of RM
 */
require plugin_dir_path( __FILE__ ) . 'lite/classes/feedback/class-ig-tracker.php';

global $ig_rm_tracker;

$ig_rm_tracker = 'IG_Tracker_V_' . str_replace( '.', '_', IG_RM_FEEDBACK_TRACKER_VERSION );

if ( ! function_exists( 'rm_show_upgrade_pro_notice' ) ) {
	/**
	 * Show RM Premium Upgrade Notice
	 *
	 * @since 1.3.0
	 */
	function rm_show_upgrade_pro_notice() {
		$url = admin_url( 'plugins.php?plugin_status=upgrade' );
		?>
		<div class="notice notice-error">
			<p>
			<?php 
			/* translators: %s: Link to Rainmaker Premium upgrade */
			echo wp_kses_post( sprintf( __( 'You are using older version of <strong>Rainmaker Premium</strong> plugin. It won\'t work because it needs plugin to be updated. Please update %s plugin.', 'icegram-rainmaker' ),
					'<a href="' . esc_url( $url ) . '" target="_blank">' . __( 'Rainmaker Premium', 'icegram-rainmaker' ) . '</a>' ) );
			?>
					</p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'deactivate_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$ig_rm_plan = 'lite';
if ( 'icegram-rainmaker-premium.php' === basename( __FILE__ ) ) {
	$ig_rm_plan = 'premium';
} 
$current_active_plugins = $ig_rm_tracker::get_active_plugins();

if ( 'premium' === $ig_rm_plan ) {
	if ( in_array( 'icegram-rainmaker/icegram-rainmaker.php', $current_active_plugins, true ) ) {
		deactivate_plugins( 'icegram-rainmaker/icegram-rainmaker.php', true );
	}
} else {
	/**
	 * Steps:
	 * - Check Whether Rainmaker Premium Installed
	 * - If It's installed & It's < 1.3.0 => Show Upgrade Notice
	 * - If It's installed & It's >= 1.3.0 => return
	 */

	//- If It's installed & It's < 1.3.0 => Show Upgrade Notice
	$all_plugins = $ig_rm_tracker::get_plugins( 'all', true );

	$rm_prem_plugin         = 'icegram-rainmaker-premium/icegram-rainmaker-premium.php';
	$rm_prem_plugin_version = ! empty( $all_plugins[ $rm_prem_plugin ] ) ? $all_plugins[ $rm_prem_plugin ]['version'] : '';

	if ( ! empty( $rm_prem_plugin_version ) ) {

		// Is Premium active?
		$is_premium_active = $all_plugins[ $rm_prem_plugin ]['is_active'];

		// Free >= 1.3.0 && Premium < 1.3.0
		if ( version_compare( $rm_prem_plugin_version, '1.3.0', '<' ) ) {

			// Show Upgrade Notice if It's Admin Screen.
			if ( is_admin() ) {
				add_action( 'admin_head', 'rm_show_upgrade_pro_notice', PHP_INT_MAX );
			}

		} elseif ( $is_premium_active && version_compare( $rm_prem_plugin_version, '1.3.0', '>=' ) ) {
			return;
		}
	}
}

/* ***************************** Initial Compatibility Work (End) ******************* */

if ( ! defined( 'IG_RM_PLUGIN_DIR' ) ) {
	define( 'IG_RM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'IG_RM_PLUGIN_URL' ) ) {
	define( 'IG_RM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'IG_RM_PLUGIN_FILE' ) ) {
	define( 'IG_RM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'IG_RM_PLUGIN_VERSION' ) ) {
	define( 'IG_RM_PLUGIN_VERSION', '1.3.0' );
}

require plugin_dir_path( __FILE__ ) . 'lite/classes/class-icegram-rainmaker.php';
require plugin_dir_path( __FILE__ ) . 'lite/class-icegram-rainmaker-loader.php';

if ( ! function_exists( 'activate_rainmaker' ) ) {
	/**
	 * The code that runs during plugin activation.
	 * 
	 * @param bool $network_wide Is plugin being activated on a network.
	 */
	function activate_rainmaker( $network_wide ) {

		global $wpdb;
		
		require_once plugin_dir_path( __FILE__ ) . 'lite/classes/class-rainmaker-activator.php';

		if ( is_multisite() && $network_wide ) {
			
			// Get all active blogs in the network and activate plugin on each one
			$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE deleted = %d", 0 ) );
			foreach ( $blog_ids as $blog_id ) {
				ig_rm_activate_on_blog( $blog_id );
			}
		} else {
			Rainmaker_Activator::activate();
		}
	}
}

if ( ! function_exists( 'deactivate_rainmaker' ) ) {
	/**
	 * The code that runs during plugin deactivation.
	 * 
	 * @param bool $network_wide Is plugin being activated on a network.
	 * 
	 */
	function deactivate_rainmaker( $network_wide ) {

		require_once plugin_dir_path( __FILE__ ) . 'lite/classes/class-rainmaker-deactivator.php';

		if ( is_multisite() && $network_wide ) {
			
			global $wpdb;
			
			// Get all active blogs in the network.
			$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE deleted = %d", 0 ) );
			foreach ( $blog_ids as $blog_id ) {
				// Run deactivation code on each one
				ig_rm_trigger_deactivation_in_multisite( $blog_id );
			}
		} else {
			Rainmaker_Deactivator::deactivate();
		}
	}
}

if ( ! function_exists( 'ig_rm_activate_on_blog' ) ) {

	/**
	 * Function to trigger Rainmaker's activation code for individual site/blog in a network.
	 * 
	 * @param  int $blog_id Blog ID of newly created site/blog.
	 * 
	 * @since  1.3.0
	 */
	function ig_rm_activate_on_blog( $blog_id ) {
		switch_to_blog( $blog_id );
		Rainmaker_Activator::activate();
		restore_current_blog();
	}
}

if ( ! function_exists( 'ig_rm_trigger_deactivation_in_multisite' ) ) {

	/**
	 * Function to trigger Rainmaker deactivation code for individual site in a network.
	 * 
	 * @param  int $blog_id Blog ID of newly created site/blog.
	 * 
	 * @since  1.3.0
	 */
	function ig_rm_trigger_deactivation_in_multisite( $blog_id ) {
		switch_to_blog( $blog_id );
		Rainmaker_Deactivator::deactivate();
		restore_current_blog();
	}
}

register_activation_hook( __FILE__, 'activate_rainmaker' );
register_deactivation_hook( __FILE__, 'deactivate_rainmaker' );


if ( ! function_exists( 'init_icegram_rainmaker' ) ) {
	function init_icegram_rainmaker() {
		global $icegram_rainmaker;
		// i18n / l10n - load translations
		load_plugin_textdomain( 'icegram-rainmaker', false, IG_RM_PLUGIN_DIR . 'lite/lang/' );
		$icegram_rainmaker = new Rainmaker();
	}
}
add_action( 'plugins_loaded', 'init_icegram_rainmaker' );


if ( ! function_exists( 'RM' ) ) {
	
	/**
	 * Rainmaker instance
	 *
	 * @param string $plugin_path Plugin path from which files to load.
	 * 
	 * @return Rainmaker
	 *
	 * @since 1.3.0
	 */
	function RM( $plugin_path = '' ) {
		$rainmaker_loader = Rainmaker_Loader::instance();
		// Load files if plugin path given.
		if ( ! empty( $plugin_path ) ) {
			$rainmaker_loader->load_dependencies( $plugin_path );
		}
		return $rainmaker_loader;
	}
}

$current_plugin_path = plugin_dir_path( __FILE__ );

/** 
 * We need to pass the plugin path explicitly using $current_plugin_path variable. 
 * We are not using IG_RM_PLUGIN_DIR constant here, since using IG_RM_PLUGIN_DIR constant causes premium version files not getting loaded when lite version is active and user is activating premium versions.
 * In that case, value of IG_RM_PLUGIN_DIR constant is the path of Rainmaker lite plugin(since it is loaded first before premium version) which does not have premium version's file thus these files are not loaded.
 */
RM( $current_plugin_path );