<?php  
function emqa_user_can( $user_id, $perm, $post_id = false ) {
	global $emqa;
	$can = false;
	if ( $user_id &&  is_numeric($user_id) ) {
		if ( $post_id ) {
			// perm with post id
			$is_comment = array( 'post_comment', 'read_comment', 'delete_comment', 'edit_comment', 'manage_comment' );
			$post_author = 0;
			// is comment
			if ( in_array( $perm, $is_comment ) ) {
				$comment = get_comment( $post_id );
				if ( isset( $comment->user_id ) ) {
					$post_author = $comment->user_id;
				}
			} else {
				$post_author = get_post_field( 'post_author', $post_id );
			}

			if ( (int) $user_id === (int) $post_author || user_can( $user_id, 'emqa_can_' . $perm ) ) {
				$can = true;
			}
		} else {
			// normal perms
			if ( user_can( $user_id, 'emqa_can_' . $perm ) ) {
				$can = true;
			}
		}
	} else {
		$anonymous = isset($emqa->permission->perms['anonymous'])?$emqa->permission->perms['anonymous']:array();
		$type = explode( '_', $perm );
		if ( isset( $anonymous[$type[1]][$type[0]] ) && $anonymous[$type[1]][$type[0]] ) {
			$can = true;
		} else {
			$can = false;
		}
	}
	return apply_filters( 'emqa_user_can', $can, $perm, $user_id, $post_id );
}

function emqa_current_user_can( $perm, $post_id = false ) {
	$current_user_id = get_current_user_id();
	$can = emqa_user_can( $current_user_id, $perm, $post_id );
	return apply_filters( 'emqa_current_user_can', $can, $current_user_id, $perm, $post_id );
}

function emqa_get_warning_page() {
	global $emqa_options;
	$warning_page_id = isset( $emqa_options['pages']['404'] ) ? $emqa_options['pages']['404'] : false;
	if ( $warning_page_id ) {
		$warning_page = wp_cache_get( 'emqa-warning-page' );
		if ( false === $warning_page ) {
			$warning_page = get_post( $warning_page_id );
			wp_cache_set( 'emqa-warning-page', $warning_page );
		}
		return $warning_page;
	}
}

class EMQA_Permission {
	public $defaults;
	public $perms;
	public $default_cap;
	public $objects;
	
	public function __construct() {
		$this->default_cap = array(
			'read'      => 1,
			'post'      => 0,
			'edit'      => 0,
			'delete'    => 0,
		);
		$this->objects = array( 'question', 'answer', 'comment' );
		$this->defaults = array(
			'administrator' => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1,
				),
				'answer'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1,  
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1,
				),
			),
			'editor'        => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1,
				),
				'answer'        => array(
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1,
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 1,
					'delete'    => 1, 
				),
			),
			'author'        => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'answer'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
			),
			'contributor'   => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'answer'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
			),
			'subscriber'    => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'answer'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				)
			),
			'anonymous'    => array(
				'question'      => array( 
					'read'      => 1,
					'post'      => 1,
					'edit'      => 0,
					'delete'    => 0,
				),
				'answer'        => array( 
					'read'      => 1,
					'post'      => 0,
					'edit'      => 0,
					'delete'    => 0,
				),
				'comment'        => array( 
					'read'      => 1,
					'post'      => 0,
					'edit'      => 0,
					'delete'    => 0,
				),
			),
		);
		
		add_action( 'init', array( $this, 'first_update_role_functions' ) );
		add_action( 'init', array( $this, 'prepare_permission' ) );
		
		add_action( 'admin_init', array( $this, 'admin_menu_reset_permission_default' ) );
		
		add_action( 'update_option_EMQA_Permission', array( $this, 'update_permission' ), 10, 2 );

		add_filter( 'user_has_cap', array( $this, 'allow_user_view_their_draft_post' ), 10, 4 );

		add_action( 'wp_ajax_emqa-reset-permission-default', array( $this, 'reset_permission_default' ) );
		add_filter( 'the_posts', array( $this, 'read_permission_apply' ), 10, 2 );
		add_filter( 'comments_array', array( $this, 'read_comment_permission_apply' ), 10, 2 );

		add_filter( 'the_posts', array( $this, 'restrict_single_question' ), 11 );
	}

	public function admin_menu_reset_permission_default(){
    if(is_admin() && current_user_can('manage_options') && isset($_POST['emqa-permission-reset']) && isset($_POST['emqa_reset_permissions_nonce'])) {
        if(wp_verify_nonce($_POST['emqa_reset_permissions_nonce'], 'emqa_reset_permissions_action')) {
            $this->reset_caps($_POST['emqa-permission-reset']);
            wp_redirect(admin_url('edit.php?post_type=emqa-question&page=emqa-settings&tab=permission'));
            exit;
        } else {
            wp_die(__('Security check failed. Please try again.', 'emqa') );
        }
    }
	}

	
	public function prepare_permission() {
		$this->perms = get_option( 'EMQA_Permission' );
	}

	public function add_caps( $value ) {
		foreach ( $value as $role_name  => $role_info ) {
			if ( $role_name == 'anonymous' )
				continue;
			$role = get_role( $role_name );
			if ( ! $role )
				continue;

			foreach ( $this->objects as $post_type ) {
				foreach ( $this->default_cap as $cap => $default ) {
					if ( isset( $role_info[$post_type][$cap] ) && $role_info[$post_type][$cap] ) {
						$role->add_cap( 'emqa_can_' . $cap . '_' . $post_type );
					} else {
						$role->remove_cap( 'emqa_can_' . $cap . '_' . $post_type );
					}
				}
			}
		}
	}
	
	public function update_permission( $old_value, $value ) {
		$this->update_caps($value);
	}
	
	public function update_caps( $value ) {
		update_option( 'EMQA_Permission', $value );
		$this->add_caps( $value );
	}

	public function reset_caps( $post_type = 'question' ) {
		//change cap of post type
		$roles = get_editable_roles();
		$roles['anonymous'] = array();
		foreach($roles as $role => $role_info){
			if(isset($this->defaults[$role])){
				$this->perms[$role][$post_type] = $this->defaults[$role][$post_type];
			}else{
				$this->perms[$role][$post_type] = array();
			}
		}
		$this->update_caps($this->perms);
	}

	public function first_update_role_functions() {
		$emqa_has_roles = get_option( 'emqa_has_roles' );
		$this->perms = get_option( 'EMQA_Permission' );
		if ( ! $emqa_has_roles || ! is_array( $this->perms ) || empty( $this->perms ) ) {
			//perms default
			$this->perms = $this->defaults;

			$this->update_caps($this->perms);
			
			update_option( 'emqa_has_roles', 1 );
		}
	}  
	
	public function prepare_permission_caps() {
		$this->update_caps( $this->defaults );
	}

	public function remove_permision_caps() {
		foreach ( $this->defaults as $role_name => $perm ) {
			if ( $role_name == 'anonymous' ) {
				continue;
			}
			
			$role = get_role( $role_name );

			foreach ( $perm['question'] as $key => $value ) {
				$cap = 'emqa_can_'.$key.'_question';
				if(isset($role->capabilities[$cap])){
					$role->remove_cap( $cap );
				}
			}
			foreach ( $perm['answer'] as $key => $value ) {
				$cap = 'emqa_can_'.$key.'_answer';
				if(isset($role->capabilities[$cap])){
					$role->remove_cap( $cap );
				}
			}
			foreach ( $perm['comment'] as $key => $value ) {
				$cap = 'emqa_can_'.$key.'_comment';
				if(isset($role->capabilities[$cap])){
					$role->remove_cap( $cap );
				}
			}
		}
	}

	public function allow_user_view_their_draft_post( $all_caps, $caps, $name, $user ) {
		if ( is_user_logged_in() ) {
			global $wp_query, $current_user;
			if ( isset( $wp_query->is_single ) && $wp_query->is_single && isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] == 'emqa-question' && $name[0] == 'edit_post' ) {
				if ( isset( $name[2] ) ) {
					$post_id = $name[2];
					$author = get_post_field( 'post_author', $post_id );
					if ( $author == $current_user->ID ) {
						foreach ( $caps as $cap ) {
							$all_caps[$cap] = true;
						}
					}
				}
			}
		}
		return $all_caps;
	}

	public function reset_permission_default() {
		global $emqa;
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), '_emqa_reset_permission' ) ) {
			wp_send_json_error( array( 'message' => __( 'Are you cheating huh?', 'emqa' ) ) );
		}
		if ( isset( $_POST['type'] ) ) {
			$old = $emqa->permission->perms;
			$type = sanitize_text_field( $_POST['type'] );
			foreach ( $emqa->permission->defaults as $role => $perms ) {
				$emqa->permission->perms[$role][$type] = $perms[$type];
			}
			$emqa->permission->reset_caps( $old, $emqa->permission->perms );
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	public function read_permission_apply( $posts, $query ) {

		if ( isset( $query->query['post_type'] ) && $query->query['post_type'] == 'emqa-question' && ! emqa_current_user_can( 'read_question' ) ) {
			return false;
		}

		if ( isset( $query->query['post_type'] ) && $query->query['post_type'] == 'emqa-answer' && ! emqa_current_user_can( 'read_answer' ) ) {
			return false;
		}

		return $posts;
	}

	public function read_comment_permission_apply( $comments, $post_id ) {
		if ( ( 'emqa-question' == get_post_type( $post_id ) || 'emqa-answer' == get_post_type( $post_id ) ) && ! emqa_current_user_can( 'read_comment' ) ) {
			return array();
		}
		return $comments;
	}

	public function restrict_single_question( $posts ) {
		global $wp_query, $wpdb, $emqa_options;
		if ( is_user_logged_in() ) 
			return $posts;
		//user is not logged
		if ( ! is_single() ) 
			return $posts;
		//this is a single post

		if ( ! $wp_query->is_main_query() )
			return $posts;
		//this is the main query

		if ( $wp_query->post_count ) 
			return $posts;

		if ( ! isset( $wp_query->query['post_type'] ) || $wp_query->query['post_type'] != 'emqa-question' ) {
			return $posts;
		}
		if ( isset( $wp_query->query['name'] ) && ! $posts ) {
			$question = get_page_by_path( $wp_query->query['name'], OBJECT, 'emqa-question' );
		} elseif ( isset( $wp_query->query['p'] ) && ! $posts ) {
			$question = get_post( $wp_query->query['p'] );
		} elseif ( ! empty( $posts ) ) {
			$question = $posts[0];	
		} else {
			return emqa_get_warning_page();
		}
		//this is a question which was submitted by anonymous user
		if ( ! emqa_is_anonymous( $question->ID ) ) {
			if ( ! $posts ) {
				return emqa_get_warning_page();
			}
			return $posts;
		} else {
			//This is a pending question
			if ( 'pending' == get_post_status( $question->ID ) || 'private' == get_post_status( $question->ID ) ) {
				$anonymous_author_view = get_post_meta( $question->ID, '_anonymous_author_view', true );
				$anonymous_author_view = $anonymous_author_view  ? $anonymous_author_view  : 0;
				
				
				if ( $anonymous_author_view < 3 ) {
					// Allow to read question right after this was added
					$questions[] = $question;
					$anonymous_author_view++;
					update_post_meta( $question->ID, '_anonymous_author_view', $anonymous_author_view );
					return $questions;
				} else {
					return emqa_get_warning_page();
				}
			}
		}

		return $posts;
	}
}

?>