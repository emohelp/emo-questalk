<?php
/**
 * The template for displaying answer submit form
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>

<div class="emqa-answer-form">
	<?php do_action( 'emqa_before_answer_submit_form' ); ?>
	<div class="emqa-answer-form-title"><?php _e( 'Your Answer', 'em-question-answer' ) ?></div>
	<form name="emqa-answer-form" id="emqa-answer-form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'emqa_add_answer_nonce', 'emqa_add_answer_nonce' ); ?>
		<?php emqa_print_notices(); ?>
		<?php $content = isset( $_POST['answer-content'] ) ? sanitize_text_field( $_POST['answer-content'] ) : ''; ?>
		<?php emqa_init_tinymce_editor( array( 'content' => $content, 'textarea_name' => 'answer-content', 'id' => 'emqa-answer-content' ) ) ?>
		<?php emqa_load_template( 'captcha', 'form' ); ?>

		<?php if ( emqa_current_user_can( 'post_answer' ) && !is_user_logged_in() ) : ?>
		<p>
			<label for="user-email"><?php _e( 'Your Email', 'em-question-answer' ) ?></label>
			<?php $email = isset( $_POST['user-email'] ) ? sanitize_email( $_POST['user-email'] ) : ''; ?>
			<input type="email" class="" name="user-email" value="<?php echo $email ?>" >
		</p>
		<p>
			<label for="user-name"><?php _e( 'Your Name', 'em-question-answer' ) ?></label>
			<?php $name = isset( $_POST['user-name'] ) ? esc_html( $_POST['user-name'] ) : ''; ?>
			<input type="text" class="" name="user-name" value="<?php echo $name ?>" >
		</p>
		<?php endif; ?>

		<select class="emqa-select" name="emqa-status">
			<optgroup label="<?php _e( 'Who can see this?', 'em-question-answer' ) ?>">
				<option value="publish"><?php _e( 'Public', 'em-question-answer' ) ?></option>
				<option value="private"><?php _e( 'Only Me &amp; Admin', 'em-question-answer' ) ?></option>
			</optgroup>
		</select>
		<?php do_action('emqa_before_answer_submit_button'); ?>
		<input type="submit" name="submit-answer" class="emqa-btn emqa-btn-primary" value="<?php _e( 'Submit', 'em-question-answer' ) ?>">
		<input type="hidden" name="question_id" value="<?php the_ID(); ?>">
		<input type="hidden" name="emqa-action" value="add-answer">
		<?php wp_nonce_field( '_emqa_add_new_answer' ) ?>
	</form>
	<?php do_action( 'emqa_after_answer_submit_form' ); ?>
</div>
