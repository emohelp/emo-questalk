<?php

class EMQA_Handle {
	public function __construct() {
		// question
		add_action( 'wp_loaded', array( $this, 'submit_question' ), 11 );
		add_action( 'wp_loaded', array( $this, 'update_question' ) );

		// answer
		add_action( 'wp_loaded', array( $this, 'insert_answer') );
		add_action( 'wp_loaded', array( $this, 'update_answer' ) );

		// comment
		add_action( 'wp_loaded', array( $this, 'insert_comment' ) );
		add_action( 'wp_loaded', array( $this, 'update_comment' ) );
	}

	public function insert_answer() {
		global $emqa_options;
		if ( ! isset( $_POST['emqa-action'] ) || ! isset( $_POST['submit-answer'] ) ) {
			return false;
		}
		// do_action( 'emqa_add_answer', $answer_id, $question_id );
		// die();

		if ( isset( $_POST['emqa_add_answer_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['emqa_add_answer_nonce'] ), 'emqa_add_answer_nonce' ) ) {
			// Nonce is valid, proceed with processing the form data
			if ( 'add-answer' !== sanitize_text_field( $_POST['emqa-action'] ) ) {
				return false;
			}
		} else {
			// Nonce is invalid, handle the error (e.g., display an error message, log the attempt, etc.)
			echo 'Nonce verification failed. Please try again.';
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( esc_html(  $_POST['_wpnonce'] ), '_emqa_add_new_answer' ) ) {
			// emqa_add_notice( __( '&quot;Helllo&quot;, Are you cheating huh?.', 'emqa' ), 'error' );
			wp_die( esc_html(__( '&quot;Hello&quot;, Are you cheating huh?', 'emqa' ) ) );
		}

		if ( $_POST['submit-answer'] == __( 'Delete draft', 'emqa' ) ) {
			$draft = isset( $_POST['answer-id'] ) ? intval( $_POST['answer-id'] ) : 0;
			if ( $draft )
				wp_delete_post( $draft );
		}

		if ( empty( $_POST['answer-content'] ) ) {
			emqa_add_notice( __( 'Answer content is empty', 'emqa' ), 'error' );
		}
		if ( empty( $_POST['question_id'] ) ) {
			emqa_add_notice( __( 'Question is empty', 'emqa' ), 'error' );
		}

		if ( !emqa_current_user_can( 'post_answer' ) ) {
			emqa_add_notice( __( 'You do not have permission to submit question.', 'emqa' ), 'error' );
		}

		if ( !is_user_logged_in() && apply_filters( 'emqa_require_user_email_fields', true ) && ( empty( $_POST['user-email'] ) || !is_email( sanitize_email( $_POST['user-email'] ) ) ) ) {
			emqa_add_notice( __( 'Missing email information', 'emqa' ), 'error' );
		}

		if ( !is_user_logged_in() && apply_filters( 'emqa_require_user_name_fields', true ) && ( empty( $_POST['user-name'] ) ) ) {
			emqa_add_notice( __( 'Missing name information', 'emqa' ), 'error' );
		}

		if ( !emqa_valid_captcha( 'single-question' ) ) {
			emqa_add_notice( __( 'Captcha is not correct', 'emqa' ), 'error' );
		}

		$user_id = 0;
		$is_anonymous = false;
		$post_author_email = '';
		$post_author_name = '';
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$is_anonymous = true;
			if ( isset( $_POST['user-email'] ) && is_email( $_POST['user-email'] ) ) {
				$post_author_email = sanitize_email( $_POST['user-email'] );
			}
			if ( isset( $_POST['user-name'] ) && !empty( $_POST['user-name'] ) ) {
				$post_author_name = sanitize_text_field( $_POST['user-name'] );
			}
		}

		$question_id = intval( $_POST['question_id'] );

		$answer_title = __( 'Answer for ', 'emqa' ) . get_post_field( 'post_title', $question_id );

		$answ_content = isset( $_POST['answer-content']) ? sanitize_text_field($_POST['answer-content']) : '';
		$answ_content = apply_filters( 'emqa_prepare_answer_content', $answ_content );

		$answers = array(
			'comment_status' => 'open',
			'post_author'    => $user_id,
			'post_content'   => $answ_content,
			'post_title'     => $answer_title,
			'post_type'      => 'emqa-answer',
			'post_parent'	 => $question_id,
		);

		$answers['post_status'] = isset( $_POST['save-draft'] )
									? 'draft'
										: ( isset( $_POST['emqa-status'] ) && $_POST['emqa-status'] ? sanitize_text_field( $_POST['emqa-status'] ) : 'publish' );

		do_action( 'emqa_prepare_add_answer' );

		if ( emqa_count_notices( 'error' ) > 0 ) {
			return false;
		}

		$answers = apply_filters( 'emqa_insert_answer_args', $answers );
		
		$answer_id = wp_insert_post( $answers );

		if ( !is_wp_error( $answer_id ) ) {
			if ( $answers['post_status'] != 'draft' ) {
				update_post_meta( $question_id, '_emqa_status', 'answered' );
				update_post_meta( $question_id, '_emqa_answered_time', time() );
				update_post_meta( $answer_id, '_emqa_votes', 0 );
				$answer_count = get_post_meta( $question_id, '_emqa_answers_count', true );
				update_post_meta( $question_id, '_emqa_answers_count', (int) $answer_count + 1 );
			}

			if ( $is_anonymous ) {
				update_post_meta( $answer_id, '_emqa_is_anonymous', true );

				if ( isset( $post_author_email ) && is_email( $post_author_email ) ) {
					update_post_meta( $answer_id, '_emqa_anonymous_email', $post_author_email );
				}

				if ( isset( $post_author_name ) && !empty( $post_author_name ) ) {
					update_post_meta( $answer_id, '_emqa_anonymous_name', $post_author_name );
				}
			} else {
				if ( !emqa_is_followed( $question_id, get_current_user_id() ) ) {
					add_post_meta( $question_id, '_emqa_followers', get_current_user_id() );
				}
			}

			do_action( 'emqa_add_answer', $answer_id, $question_id );
			$this->update_modified_date( $question_id , current_time( 'timestamp', 0 ), current_time( 'timestamp', 1 ) );

			wp_redirect( esc_url(get_permalink( $question_id ) ) );
			exit;

		} else {
			emqa_add_wp_error_message( $answer_id );
		}
	}

	public function update_answer() {
		if ( isset( $_POST['emqa-edit-answer-submit'] ) ) {
			if ( !emqa_current_user_can( 'edit_answer' ) ) {
				emqa_add_notice( __( "You do not have permission to edit answer.", 'emqa' ), 'error' );
			}

			if ( !isset( $_POST['_wpnonce'] ) && !wp_verify_nonce( esc_html( $_POST['_wpnonce'] ), '_emqa_edit_answer' ) ) {
				// emqa_add_notice( __( 'Hello, Are you cheating huh?', 'emqa' ), 'error' );
				wp_die( esc_html(__( 'Hello, Are you cheating huh?', 'emqa' ) ) );			
			}

			$answer_content =  isset( $_POST['answer_content']) ? sanitize_text_field($_POST['answer_content']) : '';
			$answer_content = apply_filters( 'emqa_prepare_edit_answer_content', $answer_content );

			if ( empty( $answer_content ) ) {
				emqa_add_notice( __( 'You must enter a valid answer content.', 'emqa' ), 'error' );
			}

			$answer_id = isset( $_POST['answer_id'] ) ? intval( $_POST['answer_id'] ) : false;

			if ( !$answer_id ) {
				emqa_add_notice( __( 'Answer is missing.', 'emqa' ), 'error' );
			}

			if ( 'emqa-answer' !== get_post_type( $answer_id ) ) {
				emqa_add_notice( __( 'This post is not answer.', 'emqa' ), 'error' );
			}

			do_action( 'emqa_prepare_insert_question', $answer_id );

			if ( emqa_count_notices( 'error' ) > 0 ) {
				return false;
			}

			$args = array(
				'ID' => $answer_id,
				'post_content' => $answer_content
			);

			$new_answer_id = wp_update_post( $args );

			if ( !is_wp_error( $new_answer_id ) ) {
				$old_post = get_post( $answer_id  );
				$new_post = get_post( $new_answer_id );
				do_action( 'emqa_update_answer', $new_answer_id, $old_post, $new_post );
				$question_id = emqa_get_post_parent_id( $new_answer_id );
				$this->update_modified_date( $question_id , current_time( 'sql', 0 ), current_time( 'sql', 1 ) );

				wp_safe_redirect( get_permalink( $question_id ) . '#answer-' . $new_answer_id );
			} else {
				emqa_add_wp_error_message( $new_answer_id );
				return false;
			}
			exit();
		}
	}
	
	public function insert_comment() {
		global $current_user;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
		if ( isset( $_POST['comment-submit'] ) ) {
			
			if ( !emqa_valid_captcha( 'comment' ) ) {
				emqa_add_notice( __( 'Captcha is not correct', 'emqa' ), 'error' , true );
			}
			if ( ! emqa_current_user_can( 'post_comment' ) ) {
				emqa_add_notice( __( 'You can\'t post comment', 'emqa' ), 'error', true );
			}
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			if ( ! isset( $_POST['comment_post_ID'] ) ) {
				emqa_add_notice( __( 'Missing post id.', 'emqa' ), 'error', true );
			}
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$comment_content = isset( $_POST['comment'] ) ? sanitize_text_field($_POST['comment']) : '';
			$comment_content = apply_filters( 'emqa_pre_comment_content', $comment_content );

			if ( empty( $comment_content ) ) {
				emqa_add_notice( __( 'Please enter your comment content', 'emqa' ), 'error', true );
			}

			$args = array(
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				'comment_post_ID'   => intval( $_POST['comment_post_ID'] ),
				'comment_content'   => $comment_content,
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				'comment_parent'    => isset( $_POST['comment_parent']) ? intval( $_POST['comment_parent'] ) : 0,
				'comment_type'		=> 'emqa-comment'
			);

			if ( is_user_logged_in() ) {
				$args['user_id'] = $current_user->ID;
				$args['comment_author'] = $current_user->display_name;
			} else {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				if ( ( ! isset( $_POST['email'] ) || ! is_email( $_POST['email'] ) ) && apply_filters( 'emqa_require_user_email_fields', true ) ) {
					emqa_add_notice( __( 'Missing email information', 'emqa' ), 'error', true );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				if ( ( ! isset( $_POST['name'] ) || empty( $_POST['name'] ) ) && apply_filters( 'emqa_require_user_name_fields', true ) ) {
					emqa_add_notice( __( 'Missing name information', 'emqa' ), 'error', true );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				$_POST['name'] = sanitize_text_field( wp_filter_kses( _wp_specialchars( $_POST['name'] ) ) );
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				$args['comment_author'] = isset( $_POST['name'] ) ? sanitize_text_field($_POST['name']) : 'Anonymous';
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				$args['comment_author_email'] = sanitize_email(  $_POST['email'] );
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				$args['comment_author_url'] = isset( $_POST['url'] ) ? esc_url( $_POST['url'] ) : '';
				$args['user_id']    = -1;
			}

			if ( emqa_count_notices( 'error', true ) > 0 ) {
				//redirect to clear content if refresh
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
				$question_id = absint( $_POST['comment_post_ID'] );
				if ( 'emqa-answer' == get_post_type( $question_id ) ) {
					$question_id = emqa_get_question_from_answer_id( $question_id );
				}
				$redirect_to = get_permalink( $question_id );
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhe
				if ( isset( $_GET['ans-page'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhe
					$redirect_to = add_query_arg( 'ans-page', absint( $_GET['ans-page'] ), $redirect_to );
				}

				$redirect_to = apply_filters( 'emqa_submit_comment_error_redirect', $redirect_to, $question_id);
    		wp_safe_redirect( $redirect_to );
    		exit; // No need for parentheses around wp_safe_redirect
				// return false;
			}

			$args = apply_filters( 'emqa_insert_comment_args', $args );

			$comment_id = wp_insert_comment( $args );
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$question_id = absint( $_POST['comment_post_ID'] );
			if ( 'emqa-answer' == get_post_type( $question_id ) ) {
				$question_id = emqa_get_question_from_answer_id( $question_id );
			}

			global $comment;
			$comment = get_comment( $comment_id );
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$client_id = isset( $_POST['clientId'] ) ? absint( $_POST['clientId'] ) : false;

			$latest_activity_args = array(
				'text' => 'commented',
				'date' => $comment->comment_date,
				'user_id' => $comment->user_id,
				'act_id' => $comment->comment_ID
			);

			wp_update_post( array(
				'ID' => absint( $question_id ),
				'post_modified' => time(),
				'post_modified_gmt' => time()
			) );

			update_post_meta( $question_id, '_latest_activity', $latest_activity_args );

			if ( is_user_logged_in() ) {
				if ( !emqa_is_followed( $question_id, $comment->user_id ) ) {
					add_post_meta( $question_id, '_emqa_followers', $comment->user_id );
				}
			} else {
				if ( !emqa_is_followed( $question_id, $comment->comment_author_email ) ) {
					add_post_meta( $question_id, '_emqa_followers', $comment->comment_author_email );
				}
			}

			do_action( 'emqa_add_comment', $comment_id, $client_id );

			$redirect_to = get_permalink( $question_id );
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhe
			if ( isset( $_GET['ans-page'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhe
				$redirect_to = add_query_arg( 'ans-page', absint( $_GET['ans-page'] ), $redirect_to );
			}

			$redirect_to = apply_filters( 'emqa_submit_comment_redirect', $redirect_to, $question_id, $comment );
			wp_safe_redirect( esc_url($redirect_to ));
			exit;
		}
	}

	public function update_comment() {
		global $post_submit_filter;
		
		if ( isset( $_POST['emqa-edit-comment-submit'] ) ) {
			if ( ! isset( $_POST['comment_id']) ) {
				emqa_add_notice( __( 'Comment is missing', 'emqa' ), 'error' );
			}
			$comment_id = intval( $_POST['comment_id'] );
			
			// Verify nonce first
			if ( !isset( $_POST['emqa_comment_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['emqa_comment_nonce'] ), 'emqa_comment_nonce_action' ) ) {
				emqa_add_notice( __( 'Invalid nonce. Please try again.', 'emqa' ), 'error' );
				return; // Invalid nonce
			}
			$comment_content = isset( $_POST['comment_content'] ) ? esc_html( $_POST['comment_content'] ) : '';
			$comment_content = apply_filters( 'emqa_pre_update_comment_content', $comment_content );

			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), '_emqa_edit_comment' ) ) {
				// emqa_add_notice( __( 'Are you cheating huh?', 'emqa' ), 'error' );
				wp_die( esc_html(__( 'Are you cheating huh?', 'emqa' ) ) );
			}

			if ( !emqa_current_user_can( 'edit_comment', $comment_id ) && !emqa_current_user_can( 'manage_comment' ) ) {
				emqa_add_notice( __( 'You do not have permission to edit comment.', 'emqa' ), 'error' );
			}

			if ( emqa_count_notices( 'error' ) > 0 ) {
				return false;
			}
			
			if ( strlen( $comment_content ) <= 0 || ! isset( $comment_id ) || ( int )$comment_id <= 0 ) {
				emqa_add_notice( __( 'Comment content must not be empty.', 'emqa' ), 'error' );
				return false;
			} else {
				
				$commentarr = array(
					'comment_ID'        => $comment_id,
					'comment_content'   => $comment_content
				);
				
				// check only author and admin can edit comment
				$emqa_comment_author = get_comment_author( $comment_id ); 
				if  ( $emqa_comment_author != get_current_user_id() && !emqa_current_user_can( 'edit_comment', $comment_id ) && !emqa_current_user_can( 'manage_comment' ) ) {
					return false;
				}

				$intval = wp_update_comment( $commentarr );
				if ( !is_wp_error( $intval ) ) {
					$comment = get_comment( $comment_id );
					wp_safe_redirect( esc_url(emqa_get_question_link( $comment->comment_post_ID ) ) ) ;
					exit;
				}else {
					emqa_add_wp_error_message( $intval );
				}
			}
		}
	}

	public function submit_question() {
		global $emqa_options;

		if ( isset( $_POST['emqa-question-submit'] ) ) {
			global $emqa_current_error;
			$valid_captcha = emqa_valid_captcha( 'question' );

			$emqa_submit_question_errors = new WP_Error();

			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( esc_html( $_POST['_wpnonce'] ), '_emqa_submit_question' ) ) {
				if ( $valid_captcha ) {
					if ( empty( $_POST['question-title'] ) ) {
						emqa_add_notice( __( 'You must enter a valid question title.', 'emqa' ), 'error' );
						return false;
					}

					if ( !is_user_logged_in() ) {
						if ( empty( $_POST['_emqa_anonymous_email'] ) || !is_email( sanitize_email( $_POST['_emqa_anonymous_email'] ) ) ) {
							emqa_add_notice( __( 'Missing email information', 'emqa' ), 'error' );
							return false;
						}

						if ( empty( $_POST['_emqa_anonymous_name'] ) ) {
							emqa_add_notice( __( 'Missing name information', 'emqa' ), 'error' );
							return false;
						}
					}

					$title = esc_html( $_POST['question-title'] );

					$category = isset( $_POST['question-category'] ) ?
								intval( $_POST['question-category'] ) : 0;
					if ( ! term_exists( $category, 'emqa-question_category' ) ) {
						$category = 0;
					}

					$tags = isset( $_POST['question-tag'] ) ?
								esc_html( $_POST['question-tag'] ): '';

					$content = isset( $_POST['question-content'] ) ?  sanitize_text_field( $_POST['question-content'] )  : '';
					$content = apply_filters( 'emqa_prepare_question_content', $content );

					$user_id = 0;
					$is_anonymous = false;
					if ( is_user_logged_in() ) {
						$user_id = get_current_user_id();
					} else {
						//$post_author_email = $_POST['user-email'];
						if ( isset( $_POST['login-type'] ) && sanitize_text_field( $_POST['login-type'] ) == 'sign-in' ) {
							$user = wp_signon( array(
								'user_login'    => isset( $_POST['user-name'] ) ? esc_html( $_POST['user-name'] ) : '',
								'user_password' => isset( $_POST['user-password'] ) ? esc_html( $_POST['user-password'] ) : '',
							), false );

							if ( ! is_wp_error( $user ) ) {
								global $current_user;
								$current_user = $user;
								wp_get_current_user();
								$user_id = $user->data->ID;
							} else {
								$emqa_current_error = $user;
								return false;
							}
						} elseif ( isset( $_POST['login-type'] ) && sanitize_text_field( $_POST['login-type'] ) == 'sign-up' ) {
							//Create new user
							$users_can_register = get_option( 'users_can_register' );
							if ( isset( $_POST['user-email'] ) && isset( $_POST['user-name-signup'] )
									&& $users_can_register && ! email_exists( $_POST['user-email'] )
										&& ! username_exists( $_POST['user-name-signup'] ) ) {

								if ( isset( $_POST['password-signup'] ) ) {
									$password = esc_html( $_POST['password-signup'] );
								} else {
									$password = wp_generate_password( 12, false );
								}

								$user_id = wp_create_user(
									esc_html( $_POST['user-name-signup'] ),
									$password,
									sanitize_email( $_POST['user-email'] )
								);
								if ( is_wp_error( $user_id ) ) {
									$emqa_current_error = $user_id;
									return false;
								}
								wp_new_user_notification( $user_id );
								$user = wp_signon( array(
									'user_login'    => esc_html( $_POST['user-name-signup'] ),
									'user_password' => $password,
								), false );
								if ( ! is_wp_error( $user ) ) {
									global $current_user;
									$current_user = $user;
									wp_get_current_user();
									$user_id = $user->data->ID;
								} else {
									$emqa_current_error = $user;
									return false;
								}
							} else {
								$message = '';
								if ( ! $users_can_register ) {
									$message .= __( 'User Registration was disabled.','emqa' ).'<br>';
								}
								if ( isset( $_POST['user-name'] ) && email_exists( sanitize_email( $_POST['user-email'] ) ) ) {
									$message .= __( 'This email is already registered, please choose another one.','emqa' ).'<br>';
								}
								if ( isset( $_POST['user-name'] ) && username_exists( esc_html( $_POST['user-name'] ) ) ) {
									$message .= __( 'This username is already registered. Please use another one.','emqa' ).'<br>';
								}
								// $emqa_current_error = new WP_Error( 'submit_question', $message );
								emqa_add_notice( $message, 'error' );
								return false;
							}
						} else {
							$is_anonymous = true;
							$question_author_email = isset( $_POST['_emqa_anonymous_email'] ) && is_email( $_POST['_emqa_anonymous_email'] ) ? sanitize_email( $_POST['_emqa_anonymous_email'] ) : false;
							$question_author_name = isset( $_POST['_emqa_anonymous_name'] ) && !empty( $_POST['_emqa_anonymous_name'] ) ? sanitize_text_field( $_POST['_emqa_anonymous_name'] ) : false;
							$user_id = 0;
						}
					}

					$post_status = ( isset( $_POST['question-status'] ) && esc_html( $_POST['question-status'] ) ) ? sanitize_text_field($_POST['question-status']) : 'publish';

					//Enable review mode
					global $emqa_general_settings;
					if ( isset( $emqa_general_settings['enable-review-question'] )
						&& $emqa_general_settings['enable-review-question']
						&& $post_status != 'private' && ! current_user_can( 'manage_options' ) ) {
						 $post_status = 'pending';
					}

					$postarr = array(
						'comment_status' => 'open',
						'post_author'    => $user_id,
						'post_content'   => $content,
						'post_status'    => $post_status,
						'post_title'     => $title,
						'post_type'      => 'emqa-question',
						'tax_input'      => array(
							'emqa-question_category'    => array( $category ),
							'emqa-question_tag'         => explode( ',', $tags )
						)
					);

					if ( apply_filters( 'emqa-current-user-can-add-question', emqa_current_user_can( 'post_question' ), $postarr ) ) {
						$new_question = $this->insert_question( $postarr );
						do_action('emqa_after_insert_question',$new_question);
					} else {
						//$emqa_submit_question_errors->add( 'submit_question',  __( 'You do not have permission to submit question.', 'emqa' ) );
						emqa_add_notice( __( 'You do not have permission to submit question.', 'emqa' ), 'error' );
						$new_question = $emqa_submit_question_errors;
					}

					if ( emqa_count_notices( 'error' ) == 0 ) {
						if ( $is_anonymous ) {
							update_post_meta( $new_question, '_emqa_anonymous_email', $question_author_email );
							update_post_meta( $new_question, '_emqa_anonymous_name', $question_author_name );
							update_post_meta( $new_question, '_emqa_is_anonymous', true );
						}

						if ( isset( $emqa_options['enable-review-question'] ) && $emqa_options['enable-review-question'] && !current_user_can( 'manage_options' ) && $post_status != 'private' ) {
							emqa_add_notice( __( 'Your question is waiting moderator.', 'emqa' ), 'success' );
						} else {
							wp_safe_redirect( esc_url(get_permalink( $new_question ) ) );
							exit;
						}
					}
				} else {
					// $emqa_submit_question_errors->add( 'submit_question', __( 'Captcha is not correct','emqa' ) );
					emqa_add_notice( __( 'Captcha is not correct', 'emqa' ), 'error' );
				}
			} else {
				// $emqa_submit_question_errors->add( 'submit_question', __( 'Are you cheating huh?','emqa' ) );
				emqa_add_notice( __( 'Are you cheating huh?', 'emqa' ), 'error' );
			}
			//$emqa_current_error = $emqa_submit_question_errors;
		}
	}

	public function update_question() {
		if ( isset( $_POST['emqa-edit-question-submit'] ) ) {
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( esc_html( $_POST['_wpnonce'] ), '_emqa_edit_question' ) ) {

				if ( !emqa_current_user_can( 'edit_question' ) ) {
					emqa_add_notice( __( "You do not have permission to edit question", 'emqa' ), 'error' );
				}

				$question_title = apply_filters( 'emqa_prepare_edit_question_title', sanitize_text_field( $_POST['question_title'] ) );
				if ( empty( $question_title ) ) {
					emqa_add_notice( __( 'You must enter a valid question title.', 'emqa' ), 'error' );
				}

				$question_id = isset( $_POST['question_id'] ) ? sanitize_text_field( $_POST['question_id'] ) : false;

				if ( !$question_id ) {
					emqa_add_notice( __( 'Question is missing.', 'emqa' ), 'error' );
				}

				if ( 'emqa-question' !== get_post_type( $question_id ) ) {
					emqa_add_notice( __( 'This post is not question.', 'emqa' ), 'error' );
				}

				$question_content = isset( $_POST['question_content'] ) ? sanitize_textarea_field( $_POST['question_content'] ) : '';
				$question_content = apply_filters( 'emqa_prepare_edit_question_content', $question_content );
				
				// $question_content = apply_filters( 'emqa_prepare_edit_question_content', $_POST['question_content'] );

				$tags = isset( $_POST['question-tag'] ) ? esc_html( $_POST['question-tag'] ): '';
				$category = isset( $_POST['question-category'] ) ? intval( $_POST['question-category'] ) : 0;
				if ( ! term_exists( $category, 'emqa-question_category' ) ) {
					$category = 0;
				}

				do_action( 'emqa_prepare_update_question', $question_id );

				if ( emqa_count_notices( 'error' ) > 0 ) {
					return false;
				}

				$args = array(
					'ID' => $question_id,
					'post_content' => $question_content,
					'post_title' => $question_title,
					'tax_input' => array(
						'emqa-question_category' => array( $category ),
						'emqa-question_tag'		=> explode( ',', $tags )
					),
				);

				$new_question_id = wp_update_post( $args );

				if ( !is_wp_error( $new_question_id ) ) {
					$old_post = get_post( $question_id );
					$new_post = get_post( $new_question_id );
					do_action( 'emqa_update_question', $new_question_id, $old_post, $new_post );
					wp_safe_redirect( get_permalink( $new_question_id ) );
				} else {
					emqa_add_wp_error_message( $new_question_id );
					return false;
				}
			} else {
				emqa_add_notice( __( 'Hello, Are you cheating huh?', 'emqa' ), 'error' );
				return false;
			}
			exit(0);
		}
	}

	public function insert_question( $args ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} elseif ( emqa_current_user_can( 'post_question' ) ) {
			$user_id = 0;
		} else {
			return false;
		}

		$args = wp_parse_args( $args, array(
			'comment_status' => 'open',
			'post_author'    => $user_id,
			'post_content'   => '',
			'post_status'    => 'pending',
			'post_title'     => '',
			'post_type'      => 'emqa-question',
		) );
		
		$args = apply_filters( 'emqa_insert_question_args', $args );

		$new_question = wp_insert_post( $args, true );

		if ( ! is_wp_error( $new_question ) ) {

			if ( isset( $args['tax_input'] ) ) {
				foreach ( $args['tax_input'] as $taxonomy => $tags ) {
					wp_set_post_terms( $new_question, $tags, $taxonomy );
				}
			}
			update_post_meta( $new_question, '_emqa_status', 'open' );
			update_post_meta( $new_question, '_emqa_views', 0 );
			update_post_meta( $new_question, '_emqa_votes', 0 );
			update_post_meta( $new_question, '_emqa_answers_count', 0 );
			add_post_meta( $new_question, '_emqa_followers', $user_id );
			$date = get_post_field( 'post_date', $new_question );
			// emqa_log_last_activity_on_question( $new_question, 'Create question', $date );
			//Call action when add question successfull
			do_action( 'emqa_add_question', $new_question, $user_id );
		}
		return $new_question;
	}

	function update_modified_date( $question_id, $modified_date, $modified_date_gmt ) {
		$data = array(
			'ID' => $question_id,
			'post_modified' => $this->timeformat_convert( $modified_date ),
			'post_modified_gmt' => $this->timeformat_convert( $modified_date_gmt ),
		);
		wp_update_post( $data );
	}

	function timeformat_convert( $timestamp ) {
		return gmdate("Y-m-d H:i:s", $timestamp );
	}
}
