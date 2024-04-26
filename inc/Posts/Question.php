<?php

/**
 * Get related questions            [description]
 */
function emqa_related_question( $question_id = false, $number = 5, $echo = true ) {
	if ( ! $question_id ) {
		$question_id = get_the_ID();
	}
	$tag_in = $cat_in = array();
	$tags = wp_get_post_terms( $question_id, 'emqa-question_tag' );
	if ( ! empty($tags) ) {
		foreach ( $tags as $tag ) {
			$tag_in[] = $tag->term_id;
		}
	}

	$category = wp_get_post_terms( $question_id, 'emqa-question_category' );
	if ( ! empty($category) ) {
		foreach ( $category as $cat ) {
			$cat_in[] = $cat->term_id;
		}
	}
	$args = array(
		'orderby'       => 'rand',
		'post__not_in'  => array($question_id),
		'showposts'     => $number,
		'ignore_sticky_posts' => 1,
		'post_type'     => 'emqa-question',
	);

	$args['tax_query']['relation'] = 'OR';
	if ( ! empty( $cat_in ) ) {
		$args['tax_query'][] = array(
			'taxonomy'  => 'emqa-question_category',
			'field'     => 'id',
			'terms'     => $cat_in,
			'operator'  => 'IN',
		);
	}
	if ( ! empty( $tag_in ) ) {
		$args['tax_query'][] = array(
			'taxonomy'  => 'emqa-question_tag',
			'field'     => 'id',
			'terms'     => $tag_in,
			'operator'  => 'IN',
		);
	}

	$related_questions = new WP_Query( $args );

	if ( $related_questions->have_posts() ) {
		if ( $echo ) {
			echo '<ul>';
			while ( $related_questions->have_posts() ) { $related_questions->the_post();
				echo '<li><a href="'.esc_url(get_permalink()).'" class="question-title">'.esc_html(get_the_title()).'</a> '.esc_html(__( 'asked by', 'emqa' )).' ';
				the_author_posts_link();
				echo '</li>';
			}
			echo '</ul>';
		}
	}
	$posts = $related_questions->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Count number of views for a questions
 * @param  int $question_id Question Post ID
 * @return int Number of views
 */
function emqa_question_views_count( $question_id = null ) {
	if ( ! $question_id ) {
		global $post;
		$question_id = $post->ID;
		if ( isset( $post->view_count ) ) {
			return $post->view_count;
		}
	}
	$views = get_post_meta( $question_id, '_emqa_views', true );

	if ( ! $views ) {
		return 0;
	} else {
		return ( int ) $views;
	}
}

class EMQA_Posts_Question extends EMQA_Posts_Base {

	public function __construct() {
		global $emqa_general_settings;

		if ( !$emqa_general_settings ) {
			$emqa_general_settings = get_option( 'emqa_options' );
		}
		$slug = isset( $emqa_general_settings['question-rewrite'] ) ? $emqa_general_settings['question-rewrite'] : 'question';
		parent::__construct( 'emqa-question', array(
			'plural' => __( 'Questions', 'emqa' ),
			'singular' => __( 'Question', 'emqa' ),
			'menu'	 => __( 'Questions', 'emqa' ),
			'rewrite' => array( 'slug' => $slug, 'with_front' => false ),
		) );

		add_action( 'manage_emqa-question_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );

		// Update view count of question, if we change single question template into shortcode, this function will need to be rewrite
		add_action( 'wp_head', array( $this, 'update_view' ) );
		//Ajax Get Questions Archive link

		add_action( 'wp_ajax_emqa-get-questions-permalink', array( $this, 'get_questions_permalink') );
		add_action( 'wp_ajax_nopriv_emqa-get-questions-permalink', array( $this, 'get_questions_permalink') );
		//Ajax stick question
		add_action( 'wp_ajax_emqa-stick-question', array( $this, 'stick_question' ) );
		add_action( 'restrict_manage_posts', array( $this, 'admin_posts_filter_restrict_manage_posts' ) );

		
		// Ajax Update question status
		add_filter( 'parse_query', array( $this, 'posts_filter' ) );

		add_action( 'wp', array( $this, 'schedule_events' ) );
		add_action( 'emqa_hourly_event', array( $this, 'do_this_hourly' ) );
		add_action( 'before_delete_post', array( $this, 'hook_on_remove_question' ) );

		//Prepare question content
		add_filter( 'emqa_prepare_question_content', array( $this, 'pre_content_kses' ), 10 );
		add_filter( 'emqa_prepare_question_content', array( $this, 'pre_content_filter'), 20 );
		add_filter( 'emqa_prepare_update_question', array( $this, 'pre_content_kses'), 10 );
		add_filter( 'emqa_prepare_update_question', array( $this, 'pre_content_filter'), 20 );
	}

	public function init() {
		$this->register_taxonomy();
	}

	public function set_supports() {
		return array( 'title', 'editor', 'comments', 'author', 'page-attributes' );
	}

	public function set_rewrite() {
		global $emqa_general_settings;
		if( isset( $emqa_general_settings['question-rewrite'] ) ) {
			return array(
				'slug' => $emqa_general_settings['question-rewrite'],
				'with_front' => false,
			);
		}
		return array(
			'slug' => 'question',
			'with_front' => false,
		);
	}

	public function get_question_rewrite() {
		global $emqa_general_settings;

		if ( !$emqa_general_settings ) {
			$emqa_general_settings = get_option( 'emqa_options' );
		}

		return isset( $emqa_general_settings['question-rewrite'] ) && !empty( $emqa_general_settings['question-rewrite'] ) ? $emqa_general_settings['question-rewrite'] : 'question';
	}

	public function get_category_rewrite() {
		global $emqa_general_settings;

		if ( !$emqa_general_settings ) {
			$emqa_general_settings = get_option( 'emqa_options' );
		}

		return isset( $emqa_general_settings['question-category-rewrite'] ) && !empty( $emqa_general_settings['question-category-rewrite'] ) ? $emqa_general_settings['question-category-rewrite'] : 'category';
	}

	public function get_tag_rewrite() {
		global $emqa_general_settings;

		if ( !$emqa_general_settings ) {
			$emqa_general_settings = get_option( 'emqa_options' );
		}

		return isset( $emqa_general_settings['question-tag-rewrite'] ) && !empty( $emqa_general_settings['question-tag-rewrite'] ) ? $emqa_general_settings['question-tag-rewrite'] : 'tag';
	}

	public function register_taxonomy() {
		global $emqa_general_settings;

		if ( !$emqa_general_settings ) {
			$emqa_general_settings = get_option( 'emqa_options' );
		}

		$cat_slug = $this->get_question_rewrite() . '/' . $this->get_category_rewrite();
		$tag_slug = $this->get_question_rewrite() . '/' . $this->get_tag_rewrite();

		$labels = array(
			'name'              => _x( 'Question Categories', 'taxonomy general name', 'emqa' ),
			'singular_name'     => _x( 'Question Category', 'taxonomy singular name', 'emqa' ),
			'search_items'      => __( 'Search Question Categories', 'emqa' ),
			'all_items'         => __( 'All Question Categories', 'emqa' ),
			'parent_item'       => __( 'Parent Question Category', 'emqa' ),
			'parent_item_colon' => __( 'Parent Question Category:', 'emqa' ),
			'edit_item'         => __( 'Edit Question Category', 'emqa' ),
			'update_item'       => __( 'Update Question Category', 'emqa' ),
			'add_new_item'      => __( 'Add New Question Category', 'emqa' ),
			'new_item_name'     => __( 'New Question Category Name', 'emqa' ),
			'menu_name'         => __( 'Question Category', 'emqa' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $cat_slug, 'with_front' => false, 'hierarchical' => true ),
			'query_var'         => true,
			'capabilities'      => array(),
		);
		register_taxonomy( $this->get_slug() . '_category', array( $this->get_slug() ), $args );

		$labels = array(
			'name'                       => _x( 'Question Tags', 'taxonomy general name', 'emqa' ),
			'singular_name'              => _x( 'Question Tag', 'taxonomy singular name', 'emqa' ),
			'search_items'               => __( 'Search Question Tags', 'emqa' ),
			'popular_items'              => __( 'Popular Question Tags', 'emqa' ),
			'all_items'                  => __( 'All Question Tags', 'emqa' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Question Tag', 'emqa' ),
			'update_item'                => __( 'Update Question Tag', 'emqa' ),
			'add_new_item'               => __( 'Add New Question Tag', 'emqa' ),
			'new_item_name'              => __( 'New Question Tag Name', 'emqa' ),
			'separate_items_with_commas' => __( 'Separate question tags with commas', 'emqa' ),
			'add_or_remove_items'        => __( 'Add or remove question tags', 'emqa' ),
			'choose_from_most_used'      => __( 'Choose from the most used question tags', 'emqa' ),
			'not_found'                  => __( 'No question tags found.', 'emqa' ),
			'menu_name'                  => __( 'Question Tags', 'emqa' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => false,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'               => array( 'slug' => $tag_slug, 'with_front' => false, 'hierarchical' => true ),
			'query_var'         => true,
			'capabilities'      => array(),
		);
		register_taxonomy( $this->get_slug() . '_tag', array( $this->get_slug() ), $args );

		// Create default category for emqa question type when emqa plugin is actived
		$cats = get_categories( array(
			'type'                     => $this->get_slug(),
			'hide_empty'               => 0,
			'taxonomy'                 => $this->get_slug() . '_category',
		) );

		if ( empty( $cats ) ) {
			wp_insert_term( __( 'Questions', 'emqa' ), $this->get_slug() . '_category' );
		}

		// global $emqa;
		// $emqa->rewrite->update_term_rewrite_rules();
	}

	// ADD NEW COLUMN
	public function columns_head( $defaults ) {
		if ( isset( $_GET['post_type'] ) && esc_html( $_GET['post_type'] ) == $this->get_slug() ) {
			$defaults['info'] = __( 'Info', 'emqa' );
			$defaults = emqa_array_insert( $defaults, array( 'question-category' => 'Category', 'question-tag' => 'Tags' ), 1 );
		}
		return $defaults;
	}

	// SHOW THE FEATURED IMAGE
	public function columns_content( $column_name, $post_ID ) {
		switch ( $column_name ) {
			case 'info':
				$status = get_post_meta( $post_ID, '_emqa_status', true );
				$sanitized_status = sanitize_text_field( $status );
				echo esc_html(ucfirst( $sanitized_status ) ). '<br>';
				echo '<strong>'.esc_html(emqa_question_answers_count( $post_ID ) ). '</strong> '.esc_html(__( 'answered', 'emqa' ) ). '<br>';
				echo '<strong>'.esc_html(emqa_vote_count( $post_ID )).'</strong> '.esc_html(__( 'voted', 'emqa' ) ). '<br>';
				echo '<strong>'.esc_html(emqa_question_views_count( $post_ID )).'</strong> '.esc_html(__( 'views', 'emqa' ) ). '<br>';
				break;
			case 'question-category':
				$terms = wp_get_post_terms( $post_ID, 'emqa-question_category' );
				$i = 0;
				foreach ( $terms as $term ) {
					if ( $i > 0 ) {
						echo ', ';
					}
					echo '<a href="'.esc_url(get_term_link( $term, 'emqa-question_category' )).'">'.esc_html($term->name) . '</a> ';
					$i++;
				}
				break;
			case 'question-tag':
				$terms = wp_get_post_terms( $post_ID, 'emqa-question_tag' );
				$i = 0;
				foreach ( $terms as $term ) {
					if ( $i > 0 ) {
						echo ', ';
					}
					echo '<a href="'.esc_url(esc_url(get_term_link( $term, 'emqa-question_tag' ))).'">' . esc_html($term->name) . '</a> ';
					$i++;
				}
				break;
		}
	}
	
	/**
	 * Init or increase views count for single question
	 * @return void
	 */
	public function update_view() {
		global $post;
		if ( is_singular( 'emqa-question' ) ) {
			$refer = wp_get_referer();
			if ( is_user_logged_in() ) {
				global $current_user;
				//save who see this post
				$viewed = get_post_meta( $post->ID, '_emqa_who_viewed', true );
				$viewed = ! is_array( $viewed ) ? array() : $viewed;
				$viewed[$current_user->ID] = current_time( 'timestamp' );
			}

			if ( ( $refer && $refer != get_permalink( $post->ID ) ) || ! $refer ) {
				if ( is_single() && 'emqa-question' == get_post_type() ) {
					$views = get_post_meta( $post->ID, '_emqa_views', true );

					if ( ! $views ) {
						$views = 1;
					} else {
						$views = ( ( int ) $views ) + 1;
					}
					update_post_meta( $post->ID, '_emqa_views', $views );
				}
			}
		}
	}

	public function get_questions_permalink() {
		if ( isset( $_GET['params'] ) ) {
			global $emqa_options;
			$params = explode( '&', sanitize_text_field( $_GET['params'] ) );
			$args = array();
			if ( ! empty( $params ) ) {
				foreach ( $params as $p ) {
					if ( $p ) {
						$arr = explode( '=', $p );
						$args[$arr[0]] = $arr[1];
					}
				}
			}

			if ( ! empty( $args ) ) {
				$url = get_permalink( $emqa_options['pages']['archive-question'] );
				$url = $url ? $url : get_post_type_archive_link( 'emqa-question' );

				$question_tag_rewrite = $emqa_options['question-tag-rewrite'];
				$question_tag_rewrite = $question_tag_rewrite ? $question_tag_rewrite : 'question-tag';
				if ( isset( $args[$question_tag_rewrite] ) ) {
					if ( isset( $args['emqa-question_tag'] ) ) {
						unset( $args['emqa-question_tag'] );
					}
				}

				$question_category_rewrite = $emqa_options['question-category-rewrite'];
				$question_category_rewrite = $question_category_rewrite ? $question_category_rewrite : 'question-category';

				if ( isset( $args[$question_category_rewrite] ) ) {
					if ( isset( $args['emqa-question_category'] ) ) {
						unset( $args['emqa-question_category'] );
					}
					$term = get_term_by( 'slug', $args[$question_category_rewrite], 'emqa-question_category' );
					unset( $args[$question_category_rewrite] );
					$url = get_term_link( $term, 'emqa-question_category' );
				} else {
					if ( isset( $args[$question_tag_rewrite] ) ) {
						$term = get_term_by( 'slug', $args[$question_tag_rewrite], 'emqa-question_tag' );
						unset( $args[$question_tag_rewrite] );
						$url = get_term_link( $term, 'emqa-question_tag' );
					}
				}


				if ( $url && ! is_wp_error( $url ) ) {
					$url = esc_url( add_query_arg( $args, $url ) );
					wp_send_json_success( array( 'url' => $url ) );
				} else {
					wp_send_json_error( array( 'error' => 'missing_questions_archive_page' ) );
				}
			} else {
				$url = get_permalink( $emqa_options['pages']['archive-question'] );
				$url = $url ? $url : get_post_type_archive_link( 'emqa-question' );
				wp_send_json_success( array( 'url' => $url ) );
			}
		}
		wp_send_json_error();
	}

	public function stick_question() {
		check_ajax_referer( '_emqa_stick_question', 'nonce' );
		if ( ! isset( $_POST['post'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid Post', 'emqa' ) ) );
		}

		$question = get_post( intval( $_POST['post'] ) );
		if ( is_user_logged_in() ) {
			global $current_user;
			$sticky_questions = get_option( 'emqa_sticky_questions', array() );

			if ( ! emqa_is_sticky( $question->ID )  ) {
				$sticky_questions[] = $question->ID;
				update_option( 'emqa_sticky_questions', $sticky_questions );
				wp_send_json_success( array( 'code' => 'stickied' ) );
			} else {
				foreach ( $sticky_questions as $key => $q ) {
					if ( $q == $question->ID ) {
						unset( $sticky_questions[$key] );
					}
				}
				update_option( 'emqa_sticky_questions', $sticky_questions );
				wp_send_json_success( array( 'code' => 'Unstick' ) );
			}
		} else {
			wp_send_json_error( array( 'code' => 'not-logged-in' ) );
		}
	}

	public function admin_posts_filter_restrict_manage_posts() {
		$type = 'post';
		if ( isset( $_GET['post_type'] ) ) {
			$type = sanitize_text_field( $_GET['post_type'] );
		}

		//only add filter to post type you want
		if ( 'emqa-question' == $type ) {
			?>
			<label for="emqa-filter-sticky-questions" style="line-height: 32px"><input type="checkbox" name="emqa-filter-sticky-questions" id="emqa-filter-sticky-questions" value="1" <?php checked( true, ( isset( $_GET['emqa-filter-sticky-questions'] ) && sanitize_text_field( $_GET['post_type'] ) ) ? true : false, true ); ?>> <span class="description"><?php esc_html_e( 'Sticky Questions','emqa' ) ?></span></label>
			<?php
		}
	}

	public function posts_filter( $query ) {
		global $pagenow;
		$type = 'post';
		if ( isset( $_GET['post_type'] ) ) {
			$type = sanitize_text_field( $_GET['post_type'] );
		}
		if ( 'emqa-question' == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['emqa-filter-sticky-questions'] ) && $_GET['emqa-filter-sticky-questions'] ) {

			$sticky_questions = get_option( 'emqa_sticky_questions' );

			if ( $sticky_questions ) {
				$query->query_vars['post__in'] = $sticky_questions;
			}
		}
		return $query;
	}


	public function delete_question() {
		$valid_ajax = check_ajax_referer( '_emqa_delete_question', 'nonce', false );
		$nonce = isset($_POST['nonce']) ? esc_html( $_POST['nonce'] ) : false;
		if ( ! $valid_ajax || ! wp_verify_nonce( $nonce, '_emqa_delete_question' ) || ! is_user_logged_in() ) {
			wp_send_json_error( array(
				'message' => __( 'Hello, Are you cheating huh?', 'emqa' )
			) );
		}

		if ( ! isset( $_POST['question'] ) ) {
			wp_send_json_error( array(
				'message'   => __( 'Question is not valid','emqa' )
			) );
		}

		$question = get_post( sanitize_text_field( $_POST['question'] ) );
		global $current_user;
		if ( emqa_current_user_can( 'delete_question', $question->ID ) ) {
			//Get all answers that is tired with this question
			do_action( 'before_delete_post', $question->ID );

			$delete = wp_delete_post( $question->ID );

			if ( $delete ) {
				global $emqa_options;
				do_action( 'emqa_delete_question', $question->ID );
				wp_send_json_success( array(
					'question_archive_url' => get_permalink( $emqa_options['pages']['archive-question'] )
				) );
			} else {
				wp_send_json_error( array(
					'question'  => $question->ID,
					'message'   => __( 'Delete Action was failed','emqa' )
				) );
			}
		} else {
			wp_send_json_error( array(
				'message'   => __( 'You do not have permission to delete this question','emqa' )
			) );
		}
	}

	public function hook_on_remove_question( $post_id ) {
		if ( 'emqa-question' == get_post_type( $post_id ) ) {
			$answers = wp_cache_get( 'emqa-answers-for-' . $post_id, 'emqa' );

			if ( false == $answers ) {

				$args = array(
					'post_type' => 'emqa-answer',
					'post_parent' => $post_id,
					'post_per_page' => '-1',
					'post_status' => array('publish', 'private', 'pending')
				);

				$answers = get_posts($args);

				wp_cache_set( 'emqa-answers-for'.$post_id, $answers, 'emqa', 21600 );
			}

			if ( ! empty( $answers ) ) {
				foreach ( $answers as $answer ) {
					wp_trash_post( $answer->ID );
				}
			}
		}
	}

	//Auto close question when question was resolved longtime
	public function schedule_events() {
		if ( ! wp_next_scheduled( 'emqa_hourly_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'emqa_hourly_event' );
		}
	}

	public function do_this_hourly() {
		$closed_questions = wp_cache_get( 'emqa-closed-question' );
		if ( false == $closed_questions ) {
			global $wpdb;
			$query = "SELECT `{$wpdb->posts}`.ID FROM `{$wpdb->posts}` JOIN `{$wpdb->postmeta}` ON `{$wpdb->posts}`.ID = `{$wpdb->postmeta}`.post_id WHERE 1=1 AND `{$wpdb->postmeta}`.meta_key = '_emqa_status' AND `{$wpdb->postmeta}`.meta_value = 'closed' AND `{$wpdb->posts}`.post_status = 'publish' AND `{$wpdb->posts}`.post_type = 'emqa-question'";
			$closed_questions = $wpdb->get_results( $query );

			wp_cache_set( 'emqa-closed-question', $closed_questions );
		}

		if ( ! empty( $closed_questions ) ) {
			foreach ( $closed_questions as $q ) {
				$resolved_time = get_post_meta( $q->ID, '_emqa_resolved_time', true );
				if ( emqa_is_resolved( $q->ID ) && ( time() - $resolved_time > (3 * 24 * 60 * 60 ) ) ) {
					update_post_meta( $q->ID, '_emqa_status', 'resolved' );
				}
			}
		}
	}
}

?>
