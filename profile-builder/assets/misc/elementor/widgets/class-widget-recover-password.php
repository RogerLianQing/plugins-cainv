<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elementor widget for our wppb-recover-password shortcode
 */
class PB_Elementor_Recover_Password_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 */
	public function get_name() {
		return 'wppb-recover-password';
	}

	/**
	 * Get widget title.
	 *
	 */
	public function get_title() {
		return __( 'Recover Password', 'profile-builder' );
	}

	/**
	 * Get widget icon.
	 *
	 */
	public function get_icon() {
		return 'eicon-shortcode';
	}

	/**
	 * Get widget categories.
	 *
	 */
	public function get_categories() {
		return array( 'profile-builder' );
	}

	/**
	 * Register widget controls
	 *
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'pb_content_section',
			array(
				'label' => __( 'Form Settings', 'profile-builder' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'pb_recovery_no_controls_text',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __( 'There are no available controls for the Password Recovery form', 'profile-builder' ),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output in the front-end
	 *
	 */
	protected function render() {

        include_once( WPPB_PLUGIN_DIR.'/front-end/recover.php' );

        echo wppb_front_end_password_recovery();
	}

}
