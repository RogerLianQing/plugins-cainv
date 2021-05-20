<?php
/**
 * Class for adding the Email Reminder Available Tags
 */

if ( class_exists( 'PMS_Meta_Box' ) ) {

    class PMS_ER_Available_Tags_Meta_Box extends PMS_Meta_Box {

        /*
         * Method to hook the output methods
         *
         * */
        public function init() {

            // Hook the output method to the parent's class action for output instead of overwriting the output_content method
            add_action( 'pms_output_content_meta_box_' . $this->post_type . '_' . $this->id, array( $this, 'output' ) );

        }

        /*
         * Method to output the Email Reminder details metabox
         *
         * */
        public function output( $post ){

            $email_reminder = new PMS_Email_Reminder( $post );

            if ( class_exists( 'PMS_Merge_Tags' ) ){

                $available_merge_tags = PMS_Merge_Tags::get_merge_tags();

                foreach( $available_merge_tags as $available_merge_tag ){

                    echo ' <input readonly="" type="text"  value="{{'. $available_merge_tag .'}}"> ';
                }

            }

        }

    } // end Class

    $pms_meta_box_available_tags = new PMS_ER_Available_Tags_Meta_Box( 'pms_er_available_tags', __( 'Available Tags', 'paid-member-subscriptions' ), 'pms-email-reminders', 'side' );
    $pms_meta_box_available_tags->init();

}
