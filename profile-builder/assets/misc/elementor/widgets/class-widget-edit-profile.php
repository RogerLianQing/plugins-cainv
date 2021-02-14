<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elementor widget for our wppb-edit-profile shortcode
 */
class PB_Elementor_Edit_Profile_Widget extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        //Select2
        wp_register_script('wppb_sl2_lib_js', WPPB_PLUGIN_URL . 'assets/js/select2/select2.min.js', array('jquery'));

        wp_register_style('wppb_sl2_lib_css', WPPB_PLUGIN_URL . 'assets/css/select2/select2.min.css');
        wp_register_style( 'wppb_sl2_css', WPPB_PLUGIN_URL.'front-end/extra-fields/select2/select2.css', false, PROFILE_BUILDER_VERSION );

        //SelectCPT
        wp_register_script( 'wppb_select2_js', WPPB_PLUGIN_URL .'assets/js/select2/select2.min.js', array( 'jquery' ), PROFILE_BUILDER_VERSION );
        wp_register_style( 'wppb_select2_css', WPPB_PLUGIN_URL .'assets/css/select2/select2.min.css', array(), PROFILE_BUILDER_VERSION );
        wp_register_style( 'wppb-select-cpt-style', WPPB_PLUGIN_URL.'front-end/extra-fields/select-cpt/style-front-end.css', array(), PROFILE_BUILDER_VERSION );

        //Upload
        wp_register_style( 'profile-builder-upload-css', WPPB_PLUGIN_URL.'front-end/extra-fields/upload/upload.css', false, PROFILE_BUILDER_VERSION );

        //Multi-Step Forms compatibility
        wp_register_style( 'wppb-msf-style-frontend', WP_PLUGIN_URL.'/pb-add-on-multi-step-forms/assets/css/frontend-multi-step-forms.css', array(), PROFILE_BUILDER_VERSION );
    }

    public function get_script_depends() {
        return [
            'wppb_sl2_lib_js',
            'wppb_select2_js',
        ];
    }

    public function get_style_depends() {
        $styles = [
            'wppb_sl2_lib_css',
            'wppb_sl2_css',
            'profile-builder-upload-css',
            'wppb_select2_css',
            'wppb-select-cpt-style',
        ];

        if ( is_plugin_active( 'pb-add-on-multi-step-forms/index.php' ) ) {
            $styles[] = 'wppb-msf-style-frontend';
        }

        return $styles;
    }

	/**
	 * Get widget name.
	 *
	 */
	public function get_name() {
		return 'wppb-edit-profile';
	}

	/**
	 * Get widget title.
	 *
	 */
	public function get_title() {
		return __( 'Edit Profile', 'profile-builder' );
	}

	/**
	 * Get widget icon.
	 *
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
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
			'pb_edit_profile_form_settings_section',
			array(
				'label' => __( 'Form Settings', 'profile-builder' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

        $args = array(
            'post_type' => 'wppb-epf-cpt',
            'posts_per_page'=> -1
        );
        $the_query = new WP_Query( $args );
        $edit_form_links = array(
            'default' => ''
        );
        $form_titles = array(
            'default' => __( 'Default', 'profile-builder' )
        );


        if ( $the_query->have_posts() ) {
            foreach ( $the_query->posts as $post ) {
                $form_titles     [ Wordpress_Creation_Kit_PB::wck_generate_slug( $post->post_title )] = $post->post_title ;
                $edit_form_links [ Wordpress_Creation_Kit_PB::wck_generate_slug( $post->post_title )] = get_edit_post_link($post->ID);
            }
            wp_reset_postdata();
        }

        $this->add_control(
            'pb_form_name',
            array(
                'label' => __('Form', 'profile-builder' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $form_titles,
                'default' => 'default',
            )
        );

        foreach ($edit_form_links as $form_slug => $edit_form_link ) {
            if( $form_slug === 'default' ){
                continue;
            }

            $this->add_control(
                'pb_form_'.$form_slug.'_edit_link' ,
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => __( 'Edit the Settings for this form <a href="'.esc_url( $edit_form_link ).'" target="_blank">here</a>' , 'profile-builder' ),
                    'condition'=>[
                        'pb_form_name' => [ $form_titles[$form_slug], $form_slug ],
                    ],
                )
            );
        }

        $this->end_controls_section();

        $this->start_controls_section(
            'pb_edit_profile_redirects_section',
            array(
                'label' => __( 'Redirects', 'profile-builder' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition'=>[
                    'pb_form_name'=>'default',
                ],
            )
        );

		$this->add_control(
			'pb_redirect_url',
			array(
				'label'      => __( 'Redirect after Edit Profile', 'profile-builder' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter URL', 'profile-builder' ),
                'condition'=>[
                    'pb_form_name'=>'default',
                ],
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output in the front-end
	 *
	 */
	protected function render() {

        include_once( WPPB_PLUGIN_DIR.'/front-end/edit-profile.php' );
        include_once( WPPB_PLUGIN_DIR.'/front-end/class-formbuilder.php' );

		$settings = $this->get_settings_for_display();

        $form_name = 'unspecified';
		if ( array_key_exists( 'pb_form_name', $settings ) && $settings['pb_form_name'] !== 'default') {
            $form_name = $settings['pb_form_name'];
		}

        $atts = [
            'form_name'    => $form_name,
            'redirect_url' => $settings['pb_redirect_url'],
        ];

        $output = wppb_front_end_profile_info( $atts );

        echo $output;

        // check if the form is being displayed in the Elementor editor
        $is_elementor_edit_mode = false;
        if( class_exists ( '\Elementor\Plugin' ) ){
            $is_elementor_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
            $message= "";
        }

        if ( $is_elementor_edit_mode && $output->args !== null ) {

            //add the scripts for various fields
            foreach ( $output->args['form_fields'] as $form_field ){
                switch ( $form_field['field'] ){
                    case 'Select2':
                        echo '<script src="'.WPPB_PLUGIN_URL.'front-end/extra-fields/select2/select2.js?ver='.PROFILE_BUILDER_VERSION.'" id="wppb_sl2_js"></script>';
                        break;
                    case 'WYSIWYG':
                        echo '<script>jQuery(document.body).off( "click.add-media-button", ".insert-media" );</script>';
                        break;
                    case 'Select (CPT)':
                        echo '<script src="'.WPPB_PLUGIN_URL.'front-end/extra-fields/select-cpt/select-cpt.js?ver='.PROFILE_BUILDER_VERSION.'" id="wppb-select-cpt-script"></script>';
                        break;
                    case 'Phone':
                        echo '<script src="'.WPPB_PLUGIN_URL.'front-end/extra-fields/phone/jquery.inputmask.bundle.min.js?ver='.PROFILE_BUILDER_VERSION.'" id="wppb-jquery-inputmask"></script>';
                        echo '<script src="'.WPPB_PLUGIN_URL.'front-end/extra-fields/phone/script-phone.js?ver='.PROFILE_BUILDER_VERSION.'" id="wppb-phone-script"></script>';
                        break;
                    default:
                        break;
                }
            }

            //Multi-Step Forms compatibility
            if ( is_plugin_active( 'pb-add-on-multi-step-forms/index.php' ) ) {
                $ajaxUrl = admin_url( 'admin-ajax.php' );
                $ajaxNonce = wp_create_nonce( 'wppb_msf_frontend_nonce' );
                echo '
                    <script id="wppb-msf-script-frontend-extra">
                        var wppb_msf_data_frontend = {"ajaxUrl":"'.$ajaxUrl.'","ajaxNonce":"'.$ajaxNonce.'"};
                    </script>
                ';
                echo '
                    <script src="'.WP_PLUGIN_URL.'/pb-add-on-multi-step-forms/assets/js/frontend-multi-step-forms.js?ver='.PROFILE_BUILDER_VERSION.'" id="wppb-msf-script-frontend">
                    </script>
                ';

            }
        }
	}

}
