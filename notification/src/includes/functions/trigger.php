<?php
/**
 * Trigger functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Defaults\Store\Trigger as TriggerStore;

/**
 * Adds Trigger to Store
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @param  Interfaces\Triggerable $trigger trigger object.
 * @return \WP_Error | true
 */
function notification_register_trigger( Interfaces\Triggerable $trigger ) {

	$store = new TriggerStore();

	try {
		$store[] = $trigger;
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_register_trigger_error', $e->getMessage() );
	}

	do_action( 'notification/trigger/registered', $trigger );

	return true;

}

/**
 * Gets all registered triggers
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @return array triggers
 */
function notification_get_triggers() {
	$store = new TriggerStore();
	return $store->get_items();
}

/**
 * Gets single registered trigger
 *
 * @since  6.0.0
 * @param  string $trigger_slug trigger slug.
 * @return mixed                trigger object or false
 */
function notification_get_trigger( $trigger_slug ) {
	$triggers = notification_get_triggers();
	return isset( $triggers[ $trigger_slug ] ) ? $triggers[ $trigger_slug ] : false;
}

/**
 * Gets all registered triggers in a grouped array
 *
 * @since  5.0.0
 * @return array grouped triggers
 */
function notification_get_triggers_grouped() {

	$return = array();

	foreach ( notification_get_triggers() as $trigger ) {

		if ( ! isset( $return[ $trigger->get_group() ] ) ) {
			$return[ $trigger->get_group() ] = array();
		}

		$return[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;

	}

	return $return;

}

/**
 * Adds global Merge Tags for all Triggers
 *
 * @since  5.1.3
 * @param  Interfaces\Taggable $merge_tag Merge Tag object.
 * @return void
 */
function notification_add_global_merge_tag( Interfaces\Taggable $merge_tag ) {

	// Add to collection so we could use it later in the Screen Help.
	add_filter( 'notification/global_merge_tags', function( $merge_tags ) use ( $merge_tag ) {
		$merge_tags[] = $merge_tag;
		return $merge_tags;
	} );

	do_action( 'notification/global_merge_tag/registered', $merge_tag );

	// Register the Merge Tag.
	add_action( 'notification/trigger/merge_tags', function( $trigger ) use ( $merge_tag ) {
		$trigger->add_merge_tag( clone $merge_tag );
	} );

}

/**
 * Gets all global Merge Tags
 *
 * @since  5.1.3
 * @return array Merge Tags
 */
function notification_get_global_merge_tags() {
	return apply_filters( 'notification/global_merge_tags', array() );
}

class ReportBug extends \BracketSpace\Notification\Abstracts\Trigger  {

    public function __construct() {

        // Add slug and the title.
        parent::__construct(
            'reportabug',
            __( 'Bug report sent', 'reportabug' )
        );

        // Hook to the action.
        $this->add_action( 'report_a_bug', 10, 2 );

    }

    public function action( $post_ID, $message ) {

    // If the message is empty, don't send any notifications.
    if ( empty( $message ) ) {
        return false;
    }

    // Set the trigger properties.
    $this->post    = get_post( $post_ID );
    $this->message = $message;

}
	public function merge_tags() {

    $this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\UrlTag( array(
        'slug'        => 'post_url',
        'name'        => __( 'Post URL', 'reportabug' ),
        'resolver'    => function( $trigger ) {
            return get_permalink( $trigger->post->ID );
        },
    ) ) );

    $this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
        'slug'        => 'post_title',
        'name'        => __( 'Post title', 'reportabug' ),
        'resolver'    => function( $trigger ) {
            return $trigger->post->post_title;
        },
    ) ) );

    $this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\HtmlTag( array(
        'slug'        => 'message',
        'name'        => __( 'Message', 'reportabug' ),
        'resolver'    => function( $trigger ) {
            return nl2br( $trigger->message );
        },
    ) ) );

    $this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\EmailTag( array(
        'slug'        => 'post_author_email',
        'name'        => __( 'Post author email', 'reportabug' ),
        'resolver'    => function( $trigger ) {
            $author = get_userdata( $trigger->post->post_author );
            return $author->user_email;
        },
    ) ) );

}

}

add_action( 'notification/elements', function() {
    register_trigger( new ReportBug() );
} );


