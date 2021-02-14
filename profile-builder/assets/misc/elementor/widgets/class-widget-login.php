<?php

// Exit if accessed directly
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elementor widget for our wppb-login shortcode
 */
class PB_Elementor_Login_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 */
	public function get_name() {
		return 'wppb-login';
	}

	/**
	 * Get widget title.
	 *
	 */
	public function get_title() {
		return __( 'Login', 'profile-builder' );
	}

	/**
	 * Get widget icon.
	 *
	 */
	public function get_icon() {
		return 'eicon-lock-user';
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
			'pb_login_links',
			array(
				'label' => __( 'Form Settings', 'profile-builder' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

        $this->add_control(
            'pb_register_url',
            array(
                'label'       => __( 'Registration', 'profile-builder' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'Enter URL', 'profile-builder' ),
            )
        );

        $this->add_control(
            'pb_lostpassword_url',
            array(
                'label'       => __( 'Recover Password', 'profile-builder' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'Enter URL', 'profile-builder' ),
            )
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'pb_login_redirects',
            array(
                'label' => __( 'Redirects', 'profile-builder' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'pb_after_login_redirect_url',
            array(
                'label'       => __( 'After Login', 'profile-builder' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'Enter URL', 'profile-builder' ),
            )
        );

        $this->add_control(
            'pb_after_logout_redirect_url',
            array(
                'label'       => __( 'After Logout', 'profile-builder' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'Enter URL', 'profile-builder' ),
            )
        );

        $this->end_controls_section();
	}

	/**
	 * Render widget output in the front-end
	 *
	 */
	protected function render() {

        include_once( WPPB_PLUGIN_DIR.'/front-end/login.php' );

        $settings = $this->get_settings_for_display();

		$atts = [
            'redirect_url'        => $settings['pb_after_login_redirect_url'],
            'logout_redirect_url' => $settings['pb_after_logout_redirect_url'],
            'register_url'        => $settings['pb_register_url'],
            'lostpassword_url'    => $settings['pb_lostpassword_url'],
        ];

        echo wppb_front_end_login( $atts );
	}

}
