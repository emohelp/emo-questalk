<?php
function custom_filter_notifications_get_registered_components( $component_names = array() ) {

    // Force $component_names to be an array
    if ( ! is_array( $component_names ) ) {
        $component_names = array();
    }

    array_push( $component_names, 'emqa' );

    return $component_names;
}
add_filter( 'bp_notifications_get_registered_components', 'custom_filter_notifications_get_registered_components' );

function bp_emqa_format_buddypress_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string',$component_action_name, $component_name ) {

	if ( 'emqa_new_answer_reply' !== $component_action_name ) {
		return $action;
	}

    // New answer notifications
    if ( 'emqa_new_answer_reply' === $component_action_name ) {
		$answer = get_post( $item_id );
		if(empty($answer)){
			return $action;
		}
		$author = get_user_by( 'id', $answer->post_author );
		
		$emqa_notif_title = get_the_title( $answer->post_parent );
		$emqa_notif_link = wp_nonce_url( add_query_arg( array( 'action' => 'bp_emqa_mark_read', 'question_id' => $answer->post_parent, 'answer_id' => $answer->ID ), get_permalink( $answer->post_parent ) ), 'bp_emqa_mark_answer_' . $answer->ID );
		$emqa_notif_title_attr  = __( 'Question Replies', 'emqa' );
		
		if ( (int) $total_items > 1 ) {
			$text   = sprintf(
				// translators: %d is replaced with the number of new replies
				__('EMQA: ','emqa') .__( 'You have %d new replies', 'emqa' ), (int) $total_items );
			$filter = 'bp_emqa_multiple_new_subscription_notification';
		} else {
			if ( !empty( $secondary_item_id ) ) {
				$text = sprintf( 
					// translators: %1$d is replaced with the number of new replies, %2$s is replaced with the recipient's name, %3$s is replaced with the sender's name
					__('EMQA: ','emqa') .__( 'You have %1$d new reply to %2$s from %3$s', 'emqa' ), (int) $total_items, $emqa_notif_title, bp_core_get_user_displayname( $secondary_item_id ) );
				
			} else {
				$text = sprintf( 
					// translators: %1$d is replaced with the number of new replies, %2$s is replaced with the recipient's name
					__('EMQA: ','emqa') .__( 'You have %1$d new reply to %2$s', 'emqa' ), (int) $total_items, $emqa_notif_title );
				
			}
			$filter = 'bp_emqa_single_new_subscription_notification';
		}

		// WordPress Toolbar
		if ( 'string' === $format ) {
			$return = apply_filters( $filter, '<a href="' . esc_url( $emqa_notif_link ) . '" title="' . esc_attr( $emqa_notif_title_attr ) . '">' . esc_html( $text ) . '</a>', (int) $total_items, $text, $emqa_notif_link );

		// Deprecated BuddyBar
		} else {
			$return = apply_filters( $filter, array(
				'text' => $text,
				'link' => $emqa_notif_link
			), $emqa_notif_link, (int) $total_items, $text, $emqa_notif_title );
		}

		do_action( 'bp_emqa_format_buddypress_notifications', $action, $item_id, $secondary_item_id, $total_items );
        return $return;
    }
}
add_filter( 'bp_notifications_get_notifications_for_user', 'bp_emqa_format_buddypress_notifications', 11, 7 );


function bp_emqa_add_answer_notification( $answer_id, $question_id ) {
    $post = get_post( $question_id );
    $answer = get_post( $answer_id );
    
	if($answer->post_status=='publish' || $answer->post_status=='private'){
		$author_id = $post->post_author;
		bp_notifications_add_notification( array(
			'user_id'           => $author_id,
			'item_id'           => $answer_id,
			'component_name'    => 'emqa',
			'component_action'  => 'emqa_new_answer_reply',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		) );
	}
}
add_action( 'emqa_add_answer', 'bp_emqa_add_answer_notification', 99, 2 );

function bp_emqa_buddypress_mark_notifications() {

   // Check nonce
   if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ), 'bp_emqa_mark_answer_' . ( isset( $_GET['answer_id'] ) ? intval( $_GET['answer_id'] ) : 0 ) ) ) {
	emqa_add_notice( __( "Invalid nonce specified. Please try again.", 'emqa' ), 'error' );
		return;
	}

	// Check if 'answer_id' is set and is a number
	if ( !isset( $_GET['answer_id'] ) || !is_numeric( $_GET['answer_id'] ) ) {
		return;
	}

	// Check if 'action' is set and matches the expected value
	if ( !isset( $_GET['action'] ) || 'bp_emqa_mark_read' !== sanitize_text_field( $_GET['action'] ) ) {
		emqa_add_notice( __( "Invalid action specified.", 'emqa' ), 'error' );
		return;
	}
	

	// Get required data
	$user_id  = bp_loggedin_user_id();
	$answer_id = intval( $_GET['answer_id'] );
	$question_id = intval( $_GET['question_id'] );

	// Check nonce
	$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';

	if ( ! wp_verify_nonce( $nonce, 'bp_emqa_mark_answer_' . $answer_id ) ) {
		emqa_add_notice( __( "Hello, Are you cheating huh?", 'emqa' ), 'error' );
		// Check current user's ability to edit the user
	} elseif ( !current_user_can( 'edit_user', $user_id ) ) {
		emqa_add_notice( __( "You do not have permission to mark notifications for that user.", 'emqa' ), 'error' );
	}

	if ( emqa_count_notices( 'error' ) > 0 ) {
		return;
	}else{
		$success = bp_notifications_mark_notifications_by_item_id( $user_id, $answer_id, 'emqa', 'emqa_new_answer_reply' );
	}
	
	if($success){
		wp_redirect(get_permalink($question_id));
		exit();
	}
}
add_action( 'init', 'bp_emqa_buddypress_mark_notifications', 10 );