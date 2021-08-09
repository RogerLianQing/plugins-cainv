<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

    /*
     * HTML output for new subscription form
     *
     * @param $atts     - is available from parent file, in the register_form method of the PMS_Shortcodes class
     */
    $form_name = 'new_subscription';
?>

<form id="pms_<?php echo esc_attr( $form_name ); ?>-form" class="pms-form" method="POST">

    <?php do_action( 'pms_' . $form_name . '_form_top', $atts ); ?>

    <?php

        wp_nonce_field( 'pms_' . $form_name . '_form_nonce', 'pmstkn' );
        pms_display_success_messages( pms_success()->get_messages('subscription_plans') );

    ?>

    <ul class="pms-form-fields-wrapper">

        <?php

		$user_roles = wp_get_current_user() -> roles;
		um_fetch_user(wp_get_current_user() -> ID );
		$chosen_role = um_user('testtt'); 
		$app_status = um_user('app_status'); 
			// Roger Custom
			if((in_array('subscriber', $user_roles) or in_array('pms_subscription_plan_890', $user_roles) or in_array('unpaid_business_member', $user_roles) and $app_status != 'Approved')){
				$include = array(0=>890, 1=> 924, 2=> 806);
				$exclude = array();
			}else{
				if($chosen_role == 'Premium Membership $149.99 / year + HSTT'){
					$include = array(0=>561 ,1=>694, 2=>806, 3=>563, 4=>890,  5=>416);
				}
				elseif($chosen_role == 'Regular Membership - $48.99 / year + HST'){
					$include = array(0=>563, 1=>694, 2=>806, 3=>890, 4=>561 , 5=>416);
				}
				elseif($chosen_role == 'ACI VIP Membership $1799 / year + HST'){
					$include = array(0=>416, 1=>694, 2=>806, 3=>563, 4=>890, 5=>561 );
				}
				elseif($chosen_role == 'Business Membership $149.99 / year + HST'){
					$include = array(03=>890, 1=>694, 2=>806, 3=>563, 4=>561 , 5=>416);
				}else{
					$include = array(0=>694, 1=>806, 2=>563, 3=>890, 4=>561 , 5=>416);
				}
				
				$exclude = array(0=> 924);
			}
			$plans =  pms_output_subscription_plans($include, $exclude, false, (isset($atts['selected']) ? trim($atts['selected']) : '' ), 'new_subscription' );

            $field_errors = pms_errors()->get_error_messages( 'subscription_plans' );
            echo '<li class="pms-field pms-field-subscriptions ' . ( !empty( $field_errors ) ? 'pms-field-error' : '' ) . '">';
//                echo pms_output_subscription_plans( $atts['subscription_plans'], $atts['exclude'], false, (isset($atts['selected']) ? trim($atts['selected']) : '' ), 'new_subscription' ); //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped
               echo $plans;
            echo '</li>';

        ?>

    </ul>

    <?php do_action( 'pms_' . $form_name . '_form_bottom', $atts ); ?>

    <input name="pms_<?php echo esc_attr( $form_name ); ?>" type="submit" value="<?php echo esc_attr( apply_filters( 'pms_' . $form_name . '_form_submit_text', __( 'Subscribe', 'paid-member-subscriptions' ) ) ); ?>" />

</form>