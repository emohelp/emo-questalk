<?php
/**
 * The template for displaying single answers
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>
<?php if ( emqa_current_user_can( 'post_question' ) ) : ?>
	<?php do_action( 'emqa_before_question_submit_form' ); ?>
	<form method="post" class="emqa-content-edit-form" enctype="multipart/form-data">
		<p class="emqa-search">
			<label for="question_title"><?php esc_html_e( 'Title', 'emqa' ) ?></label>
			<?php 
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$title = isset( $_POST['question-title'] ) ? sanitize_title( $_POST['question-title'] ) : ''; ?>
			<input type="text" data-nonce="<?php echo esc_attr(wp_create_nonce( '_emqa_filter_nonce' )) ?>" id="question-title" name="question-title" value="<?php echo esc_attr($title) ?>" tabindex="1">
		</p>
		<?php 
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
		$content = isset( $_POST['question-content'] ) ? sanitize_text_field( $_POST['question-content'] ) : ''; ?>
		<p><?php emqa_init_tinymce_editor( array( 'content' => $content, 'textarea_name' => 'question-content', 'id' => 'question-content' ) ) ?></p>
		<?php global $emqa_general_settings; ?>
		<?php if ( isset( $emqa_general_settings['enable-private-question'] ) && $emqa_general_settings['enable-private-question'] ) : ?>
		<p>
			<label for="question-status"><?php esc_html_e( 'Status', 'emqa' ) ?></label>
			<select class="emqa-select" id="question-status" name="question-status">
				<optgroup label="<?php esc_html_e( 'Who can see this?', 'emqa' ) ?>">
					<option value="publish"><?php esc_html_e( 'Public', 'emqa' ) ?></option>
					<option value="private"><?php esc_html_e( 'Only Me &amp; Admin', 'emqa' ) ?></option>
				</optgroup>
			</select>
		</p>
		<?php endif; ?>
		<p>
			<label for="question-category"><?php esc_html_e( 'Category', 'emqa' ) ?></label>
			<?php
				wp_dropdown_categories( array(
					'name'          => 'question-category',
					'id'            => 'question-category',
					'taxonomy'      => 'emqa-question_category',
					'show_option_none' => __( 'Select question category', 'emqa' ),
					'hide_empty'    => 0,
					'quicktags'     => array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
					'selected'      => isset( $_POST['question-category'] ) ? sanitize_text_field( $_POST['question-category'] ) : false,
				) );
			?>
		</p>
		<p>
			<label for="question-tag"><?php esc_html_e( 'Tag', 'emqa' ) ?></label>
			<?php
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$tags = isset( $_POST['question-tag'] ) ? sanitize_text_field( $_POST['question-tag'] ) : ''; ?>
			<input type="text" class="" name="question-tag" value="<?php echo esc_attr($tags) ?>" >
		</p>
		<?php if ( emqa_current_user_can( 'post_question' ) && !is_user_logged_in() ) : ?>
		<p>
			<label for="_emqa_anonymous_email"><?php esc_html_e( 'Your Email', 'emqa' ) ?></label>
			<?php 
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$email = isset( $_POST['_emqa_anonymous_email'] ) ? sanitize_email( $_POST['_emqa_anonymous_email'] ) : ''; ?>
			<input type="email" class="" name="_emqa_anonymous_email" value="<?php echo esc_attr($email) ?>" >
		</p>
		<p>
			<label for="_emqa_anonymous_name"><?php esc_html_e( 'Your Name', 'emqa' ) ?></label>
			<?php 
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled elsewhere.
			$name = isset( $_POST['_emqa_anonymous_name'] ) ? sanitize_text_field( $_POST['_emqa_anonymous_name'] ) : ''; ?>
			<input type="text" class="" name="_emqa_anonymous_name" value="<?php echo esc_attr($name) ?>" >
		</p>
		<?php endif; ?>
		<?php wp_nonce_field( '_emqa_submit_question' ) ?>
		<?php emqa_load_template( 'captcha', 'form' ); ?>
		<?php do_action('emqa_before_question_submit_button'); ?>
		<input type="submit" name="emqa-question-submit" value="<?php esc_html_e( 'Submit', 'emqa' ) ?>" >
	</form>
	<?php do_action( 'emqa_after_question_submit_form' ); ?>
<?php else : ?>
	<div class="alert"><?php esc_html_e( 'You do not have permission to submit a question','emqa' ) ?></div>
<?php endif; ?>