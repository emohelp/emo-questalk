<?php
/**
 * The template for displaying single answers
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>
<?php if ( emqa_current_user_can( 'post_question' ) ) : ?>
	<?php do_action( 'emqa_before_question_submit_form' ); ?>
	<form method="post" class="emqa-content-edit-form" enctype="multipart/form-data">
		<p class="emqa-search">
			<label for="question_title"><?php _e( 'Title', 'em-question-answer' ) ?></label>
			<?php $title = isset( $_POST['question-title'] ) ? sanitize_title( $_POST['question-title'] ) : ''; ?>
			<input type="text" data-nonce="<?php echo wp_create_nonce( '_emqa_filter_nonce' ) ?>" id="question-title" name="question-title" value="<?php echo $title ?>" tabindex="1">
		</p>
		<?php $content = isset( $_POST['question-content'] ) ? sanitize_text_field( $_POST['question-content'] ) : ''; ?>
		<p><?php emqa_init_tinymce_editor( array( 'content' => $content, 'textarea_name' => 'question-content', 'id' => 'question-content' ) ) ?></p>
		<?php global $emqa_general_settings; ?>
		<?php if ( isset( $emqa_general_settings['enable-private-question'] ) && $emqa_general_settings['enable-private-question'] ) : ?>
		<p>
			<label for="question-status"><?php _e( 'Status', 'em-question-answer' ) ?></label>
			<select class="emqa-select" id="question-status" name="question-status">
				<optgroup label="<?php _e( 'Who can see this?', 'em-question-answer' ) ?>">
					<option value="publish"><?php _e( 'Public', 'em-question-answer' ) ?></option>
					<option value="private"><?php _e( 'Only Me &amp; Admin', 'em-question-answer' ) ?></option>
				</optgroup>
			</select>
		</p>
		<?php endif; ?>
		<p>
			<label for="question-category"><?php _e( 'Category', 'em-question-answer' ) ?></label>
			<?php
				wp_dropdown_categories( array(
					'name'          => 'question-category',
					'id'            => 'question-category',
					'taxonomy'      => 'emqa-question_category',
					'show_option_none' => __( 'Select question category', 'em-question-answer' ),
					'hide_empty'    => 0,
					'quicktags'     => array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close' ),
					'selected'      => isset( $_POST['question-category'] ) ? sanitize_text_field( $_POST['question-category'] ) : false,
				) );
			?>
		</p>
		<p>
			<label for="question-tag"><?php _e( 'Tag', 'em-question-answer' ) ?></label>
			<?php $tags = isset( $_POST['question-tag'] ) ? sanitize_text_field( $_POST['question-tag'] ) : ''; ?>
			<input type="text" class="" name="question-tag" value="<?php echo $tags ?>" >
		</p>
		<?php if ( emqa_current_user_can( 'post_question' ) && !is_user_logged_in() ) : ?>
		<p>
			<label for="_emqa_anonymous_email"><?php _e( 'Your Email', 'em-question-answer' ) ?></label>
			<?php $email = isset( $_POST['_emqa_anonymous_email'] ) ? sanitize_email( $_POST['_emqa_anonymous_email'] ) : ''; ?>
			<input type="email" class="" name="_emqa_anonymous_email" value="<?php echo $email ?>" >
		</p>
		<p>
			<label for="_emqa_anonymous_name"><?php _e( 'Your Name', 'em-question-answer' ) ?></label>
			<?php $name = isset( $_POST['_emqa_anonymous_name'] ) ? sanitize_text_field( $_POST['_emqa_anonymous_name'] ) : ''; ?>
			<input type="text" class="" name="_emqa_anonymous_name" value="<?php echo $name ?>" >
		</p>
		<?php endif; ?>
		<?php wp_nonce_field( '_emqa_submit_question' ) ?>
		<?php emqa_load_template( 'captcha', 'form' ); ?>
		<?php do_action('emqa_before_question_submit_button'); ?>
		<input type="submit" name="emqa-question-submit" value="<?php _e( 'Submit', 'em-question-answer' ) ?>" >
	</form>
	<?php do_action( 'emqa_after_question_submit_form' ); ?>
<?php else : ?>
	<div class="alert"><?php _e( 'You do not have permission to submit a question','em-question-answer' ) ?></div>
<?php endif; ?>