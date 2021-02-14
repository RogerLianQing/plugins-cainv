<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

    /**
     * Function that returns an array with the user roles slugs and names with the exception
     * of the ones created by the subscription plans
     *
     * @return array
     *
     */
    function pms_get_user_role_names() {

        global $wp_roles;

        // This will be returned at the end
        $role_names = array();
        $wp_roles_names = array_reverse( $wp_roles->role_names );

        foreach( $wp_roles_names as $role_slug => $role_name ) {

            // Evade administrators
            if( $role_slug == 'administrator' )
                continue;

            // Escape user roles created from subscription plans
            if( strpos( $role_slug, 'pms_subscription_plan_' ) !== false )
                continue;

            $role_names[ $role_slug ] = $role_name;

        }

        return $role_names;
    }


    /**
     * Return a user role name by its slug
     *
     * @param string $role_slug
     *
     */
    function pms_get_user_role_name( $role_slug = '' ) {

        global $wp_roles;

        return ( isset( $wp_roles->role_names[ $role_slug ] ) ? $wp_roles->role_names[ $role_slug ] : '' );
    }


    /**
     * Function that checks to see if a user role exists
     *
     * @param string $role_slug
     *
     * @return bool
     *
     */
    function pms_user_role_exists( $role_slug = '' ) {

        global $wp_roles;

        if( isset( $wp_roles->role_names[$role_slug] ) )
            return true;
        else
            return false;

    }


    /**
     * Returns the user role assign to a subscription plan,
     *
     * @param mixed int|array $subscription_plan_id_or_ids
     *
     * @return mixed int|array
     *
     */
    function pms_get_user_roles_by_plan_ids( $subscription_plan_id_or_ids ) {

        if( is_array( $subscription_plan_id_or_ids ) ) {

            $return = array();

            foreach( $subscription_plan_id_or_ids as $id )
                $return[$id] = get_post_meta( $id, 'pms_subscription_plan_user_role', true );

        } else {

            $return = pms_get_subscription_plan_user_role( $subscription_plan_id_or_ids );

        }

        return $return;

    }


    /**
     * Add a new user role to an existing user
     *
     * @param int    $user_id
     * @param string $user_role
     *
     */
    function pms_add_user_role( $user_id = 0, $user_role = '' ) {

        if( empty( $user_id ) )
            return;

        if( empty( $user_role ) )
            return;

        global $wp_roles;

        if( ! isset( $wp_roles->role_names[$user_role] ) )
            return;

		// Roger Custom
		$user = new WP_User( $user_id );
        $user->add_role( $user_role );
		
		if('administrator' == $user_role){
		$toupdate = array('testtt' => 'Admin');
		}
		elseif('vip_member' == $user_role ){
			$toupdate = array('testtt' => 'ACI VIP Membership $1099 / year + HST');
		}
		elseif('regular_member' == $user_role){
			$toupdate = array('testtt' => 'Regular Membership $48.99 / year + HST');
		}
		elseif('premium_member'== $user_role){
			$toupdate = array('testtt' => 'Premium Membership $149.99 / year + HST');
		}
		elseif('pms_subscription_plan_694'== $user_role){
			$toupdate = array('testtt' => 'Approved - pending for payment');
		}
		elseif('pms_subscription_plan_890'== $user_role){
			$toupdate = array('testtt' => 'Business Membership $149.99 / year + HST');
		}
		elseif(('unpaid_business_member' == $user_role)  or ('subscriber'== $user_role)){
			$toupdate = array('testtt' => 'Not a Member');
		}
	
		um_fetch_user($user -> ID );
		$refer = um_user('referral'); 
		if(strlen($refer) >= 8 ){
			$users = get_users( array( 'fields' => array( 'ID' ) ) );
			foreach($users as $userr){
				$nick =  get_field('nickname', 'user_' . $userr -> ID);
				if($refer == $nick and $user_role != 'unpaid_business_member' and $user_role != 'subscriber'){
					$disc = get_field('discount', 'user_' . $userr -> ID);
					if ($disc >= 0){
						if($user_role == 'regular_member'){
							$add = 2.5;
						}
						else{
							$add = 5;
						}
						$final = $disc + $add;
						if($final >= 100 ){
							$final = 100;
						}
						update_field('discount', $final ,  'user_' . $userr -> ID);
						$toupdate = array('referral' => ' ');
						UM()->user()->update_profile($toupdate);
					}
					else{
						if($user_role == 'regular_member'){
							$add = 2.5;
						}else{
							$add = 5;
						}
						update_field('discount', $add ,  'user_' . $userr -> ID);
						$toupdate = array('referral' => ' ');
						UM()->user()->update_profile($toupdate);
					}
				}
			}
		}

		
		UM()->user()->update_profile($toupdate);
	}


    /**
     * Remove a new user role from an existing user. If the user remains without a role,
     * "subscriber" is added by default
     *
     * @param int    $user_id
     * @param string $user_role
     *
     */
    function pms_remove_user_role( $user_id = 0, $user_role = '' ) {

        if( empty( $user_id ) )
            return;

        if( empty( $user_role ) )
            return;

        $user = new WP_User( $user_id );
        $user->remove_role( $user_role );
		

        if( empty( $user->roles ) )
            $user->add_role( 'unpaid_business_member' );
			$toupdate = array('testtt' => 'Not a Member');
			um_fetch_user($user -> ID );
			UM()->user()->update_profile($toupdate);

    }


    /**
     * When a member subscription is being inserted into the database we want the role attached to
     * the subscription plan to be added to the user
     *
     * @param int   $subscription_id
     * @param array $new_data
     *
     */
    function pms_member_add_user_role_subscription_inserted( $subscription_id = 0, $new_data = array() ) {

        if( empty( $subscription_id ) || empty( $new_data ) )
            return;

        if( empty( $new_data['subscription_plan_id'] ) )
            return;

        if( empty( $new_data['status'] ) || $new_data['status'] != 'active' )
            return;

        $member_subscription = pms_get_member_subscription( $subscription_id );

        // Add new subscription plan role
        pms_add_user_role( $member_subscription->user_id, pms_get_subscription_plan_user_role( (int)$new_data['subscription_plan_id'] ) );

    }
    add_action( 'pms_member_subscription_inserted', 'pms_member_add_user_role_subscription_inserted', 10, 2 );

    /**
     * When a member subscription is being updated and the subscription plan id is changed we also want
     * this to be reflected in the user role
     *
     * @param int   $subscription_id
     * @param array $new_data
     * @param array $old_data
     *
     */
    function pms_member_add_user_role_subscription_updated( $subscription_id = 0, $new_data = array(), $old_data = array() ) {

        if( empty( $subscription_id ) || empty( $new_data ) || empty( $old_data ) )
            return;

        /**
         * Handle activation of the member subscription
         *
         */
        if( ! empty( $new_data['status'] ) && $new_data['status'] == 'active' ) {

            if( ! empty( $old_data['subscription_plan_id'] ) ) {

                $member_subscription = pms_get_member_subscription( $subscription_id );

                // Add new subscription plan role
                pms_add_user_role( $member_subscription->user_id, pms_get_subscription_plan_user_role( (int)$old_data['subscription_plan_id'] ) );

            }

        }


        /**
         * Handle the change of subscription plan ids of the
         *
         */
        if( ! empty( $new_data['subscription_plan_id'] ) && ! empty( $old_data['subscription_plan_id'] ) ) {

            if( $new_data['subscription_plan_id'] != $old_data['subscription_plan_id'] ) {

                $member_subscription = pms_get_member_subscription( $subscription_id );

                // Add new subscription plan role
                pms_add_user_role( $member_subscription->user_id, pms_get_subscription_plan_user_role( (int)$new_data['subscription_plan_id'] ) );

                // Remove old subscription plan role
                pms_remove_user_role( $member_subscription->user_id, pms_get_subscription_plan_user_role( (int)$old_data['subscription_plan_id'] ) );

            }

        }

    }
    add_action( 'pms_member_subscription_updated', 'pms_member_add_user_role_subscription_updated', 10, 3 );


    /**
     * Removes the user role, attached to the subscription plan, from the member when their subscription expires
     *
     * @param int   $subscription_id
     * @param array $new_data
     * @param array $old_data
     *
     */
    function pms_member_remove_user_role_subscription_expire( $subscription_id = 0, $new_data = array(), $old_data = array() ) {

        if( empty( $subscription_id ) || empty( $new_data ) || empty( $old_data ) )
            return;

        if( empty( $new_data['status'] ) )
            return;

        if( $new_data['status'] != 'expired' )
            return;

        $member_subscription         = pms_get_member_subscription( $subscription_id );
        $subscription_plan_user_role = pms_get_subscription_plan_user_role( $member_subscription->subscription_plan_id );

        pms_remove_user_role( $member_subscription->user_id, $subscription_plan_user_role );

    }
    add_action( 'pms_member_subscription_updated', 'pms_member_remove_user_role_subscription_expire', 10, 3 );


    /**
     * Removes the user role, attached to the subscription plan, from the member when their subscription is deleted
     * from the database
     *
     * @param int   $subscription_id
     * @param array $old_data
     *
     */
    function pms_member_remove_user_role_subscription_deleted( $subscription_id = 0, $old_data = array() ) {

        if( empty( $subscription_id ) || empty( $old_data ) )
            return;

        if( empty( $old_data['subscription_plan_id'] ) )
            return;

        if( empty( $old_data['user_id'] ) )
            return;

        $subscription_plan_user_role = pms_get_subscription_plan_user_role( $old_data['subscription_plan_id'] );

        pms_remove_user_role( $old_data['user_id'], $subscription_plan_user_role );

    }
    add_action( 'pms_member_subscription_deleted', 'pms_member_remove_user_role_subscription_deleted', 10, 2 );

    /**
     * Removes the user role, attached to the subscription plan, from the member when the user is abandoning
     * the subscription plan
     *
     * @param int   $subscription_id
     * @param array $old_data
     *
     */
    function pms_member_remove_user_role_subscription_abandoned( $member_data, $member_subscription ) {

        if( empty( $member_data['user_id'] ) || empty( $member_subscription->subscription_plan_id ) )
            return;

        pms_remove_user_role( $member_data['user_id'], pms_get_subscription_plan_user_role( $member_subscription->subscription_plan_id ) );

    }
    add_action( 'pms_abandon_member_subscription_successful', 'pms_member_remove_user_role_subscription_abandoned', 10, 2 );
