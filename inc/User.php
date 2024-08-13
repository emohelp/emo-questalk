<?php  

function emqa_get_following_user( $question_id = false ) {
	if ( ! $question_id ) {
		$question_id = get_the_ID();
	}
	$followers = get_post_meta( $question_id, '_emqa_followers' );
	
	if ( empty( $followers ) ) {
		return false;
	}
	
	return $followers;
}
/** 
 * Did user flag this post ?
 */
function emqa_is_user_flag( $post_id, $user_id = null ) {
	if ( ! $user_id ) {
		global $current_user;
		if ( $current_user->ID > 0 ) {
			$user_id = $current_user->ID;
		} else {
			return false;
		}
	}
	$flag = get_post_meta( $post_id, '_flag', true );
	if ( ! $flag ) {
		return false;
	}
	$flag = unserialize( $flag );
	if ( ! is_array( $flag ) ) {
		return false;
	}
	if ( ! array_key_exists( $user_id, $flag ) ) {
		return false;
	}
	if ( $flag[$user_id] == 1 ) {
		return true;
	}
	return false;
}


function emqa_user_post_count( $user_id, $post_type = 'post' ) {
	$posts = new WP_Query( array(
		'author' => $user_id,
		'post_status'		=> array( 'publish', 'private' ),
		'post_type'			=> $post_type,
		'fields' => 'ids',
	) );
	return $posts->found_posts;
}

function emqa_user_question_count( $user_id ) {
	return emqa_user_post_count( $user_id, 'emqa-question' );
}

function emqa_user_answer_count( $user_id ) {
	return emqa_user_post_count( $user_id, 'emqa-answer' );
}


function emqa_user_most_answer( $number = 10, $from = false, $to = false ) {
	global $wpdb;
	
	$query = "SELECT post_author, count( * ) as `answer_count` 
				FROM `{$wpdb->prefix}posts` 
				WHERE post_type = 'emqa-answer' 
					AND post_status = 'publish'
					AND post_author <> 0";
	if ( $from ) {
		$from = gmdate( 'Y-m-d h:i:s', $from );
		$query .= " AND `{$wpdb->prefix}posts`.post_date > '{$from}'";
	}
	if ( $to ) {
		$to = gmdate( 'Y-m-d h:i:s', $to );
		$query .= " AND `{$wpdb->prefix}posts`.post_date < '{$to}'";
	}

	$prefix = '-all';
	if ( $from && $to ) {
		$prefix = '-' . ( $from - $to );
	}
	
	// Append the group by and order by clauses to the query
	$query .= " GROUP BY post_author ORDER BY `answer_count` DESC LIMIT 0, %d";
	
	// Generate a unique cache key based on the prefix
	$cache_key = 'emqa-most-answered' . $prefix;
	$users = wp_cache_get( $cache_key );
	
	if ( false === $users ) {
		global $wpdb;
	
		// Prepare the query to safely insert the limit value
		$query = $wpdb->prepare( $query, $number );
	
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$users = $wpdb->get_results( $query, ARRAY_A );
	
		// Store the result in cache
		wp_cache_set( $cache_key, $users );
	}
	
	return $users;
			
}

function emqa_user_most_answer_this_month( $number = 10 ) {
	$from = strtotime( 'first day of this month' );
	$to = strtotime( 'last day of this month' );
	return emqa_user_most_answer( $number, $from, $to );
}

function emqa_user_most_answer_last_month( $number = 10 ) {
	$from = strtotime( 'first day of last month' );
	$to = strtotime( 'last day of last month' );
	return emqa_user_most_answer( $number, $from, $to );
}

function emqa_is_followed( $post_id = false, $user_id = false ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $user_id ) {
		$user = wp_get_current_user();
		$user_id = $user->ID;
	}

	if ( in_array( $user_id, get_post_meta( $post_id, '_emqa_followers', false ) ) ) {
		return true;
	}
	return false;
}

/**
* Get username
*
* @param string $display_name
* @return string
* @since 1.4.0
*/
function emqa_the_author( $display_name ) {
	global $post;

	if ( 'emqa-answer' == $post->post_type || 'emqa-question' == $post->post_type) {
		if ( emqa_is_anonymous( $post->ID ) ) {
			$anonymous_name = get_post_meta( $post->ID, '_emqa_anonymous_name', true );
			$display_name = $anonymous_name ? $anonymous_name : __( 'Anonymous', 'emqa' );
		}
	}

	return $display_name;
}
add_filter( 'the_author', 'emqa_the_author' );

/**
* Get user's profile link
*
* @param int $user_id
* @return string
* @since 1.4.0
*/
function emqa_get_author_link( $user_id = false ) {
	if ( ! $user_id ) {
		return false;
	}

	$user = get_user_by( 'id', $user_id );
	if(!$user){
		return false;
	}

	global $emqa_general_settings;
	
	$question_link = isset( $emqa_general_settings['pages']['archive-question'] ) ? get_permalink( $emqa_general_settings['pages']['archive-question'] ) : false;
	$url = get_the_author_link( $user_id );
	if ( $question_link ) {
		$url = add_query_arg( array( 'user' => urlencode( $user->user_nicename ) ), $question_link );
	}

	return apply_filters( 'emqa_get_author_link', $url, $user_id, $user );
}


/**
* Get question ids user is subscribing
*
* @param int $user_id
* @return array
* @since 1.4.0
*/
function emqa_get_user_question_subscribes( $user_id = false, $posts_per_page = 5, $page = 1 ) {
	if ( !$user_id ) {
		return array();
	}
	$args = array(
		'post_type' 				=> 'emqa-question',
		'posts_per_page'			=> $posts_per_page,
		'paged'						=> $page,
		'fields' 					=> 'ids',
		'update_post_term_cache' 	=> false,
		'update_post_meta_cache' 	=> false,
		'no_found_rows' 			=> true,
		// @phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'meta_query'				=> array(
			'key'					=> '_emqa_followers',
			'value'					=> $user_id,
			'compare'				=> '='
		)
	);

	$question_id = wp_cache_get( '_emqa_user_'. $user_id .'_question_subscribes' );

	if ( ! $question_id ) {
		$question_id = get_posts( $args );
		wp_cache_set( '_emqa_user_'. $user_id .'_question_subscribes', $question_id, false, 450 );
	}

	return $question_id;
}

function emqa_get_user_badge( $user_id = false ) {
	if ( !$user_id ) {
		return;
	}

	$badges = array();
	if ( user_can( $user_id, 'edit_posts' ) ) {
		$badges['staff'] = __( 'Staff', 'emqa' );
	}

	return apply_filters( 'emqa_get_user_badge', $badges, $user_id );
}

function emqa_print_user_badge( $user_id = false, $echo = false ) {
	if ( !$user_id ) {
		return;
	}

	$badges = emqa_get_user_badge( $user_id );
	$result = '';
	if ( $badges && !empty( $badges ) ) {
		foreach( $badges as $k => $badge ) {
			$k = str_replace( ' ', '-', $k );
			$result .= '<span class="emqa-label emqa-'. esc_attr(strtolower( $k )) .'">'.wp_kses_post( $badge ).'</span>';
		}
	}

	if ( $echo ) {
		echo wp_kses_post($result);
	}

	return $result;
}

class EMQA_User { 
	public function __construct() {
		// Do something about user roles, permission login, profile setting
		add_action( 'wp_ajax_emqa-follow-question', array( $this, 'follow_question' ) );
	}

	function follow_question() {
		check_ajax_referer( '_emqa_follow_question', 'nonce' );
		if ( ! isset( $_POST['post'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid Post', 'emqa' ) ) );
		}
		$question = get_post( intval( $_POST['post'] ) );
		if ( is_user_logged_in() ) {
			global $current_user;
			if ( ! emqa_is_followed( $question->ID )  ) {
				do_action( 'emqa_follow_question', $question->ID, $current_user->ID );
				add_post_meta( $question->ID, '_emqa_followers', $current_user->ID );
				wp_send_json_success( array( 'code' => 'followed', 'text' => 'Unsubscribe' ) );
			} else {
				do_action( 'emqa_unfollow_question', $question->ID, $current_user->ID );
				delete_post_meta( $question->ID, '_emqa_followers', $current_user->ID );
				wp_send_json_success( array( 'code' => 'unfollowed', 'text' => 'Subscribe' ) );
			}
		} else {
			wp_send_json_error( array( 'code' => 'not-logged-in' ) );
		}

	}
}
?>