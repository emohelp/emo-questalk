<?php
/**
 * The template for editing question and answer
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>

<?php
$comment_id = isset( $_GET['comment_edit'] ) && is_numeric( $_GET['comment_edit'] ) ? intval( $_GET['comment_edit'] ) : false;
$edit_id = isset( $_GET['edit'] ) && is_numeric( $_GET['edit'] ) ? intval( $_GET['edit'] ) : ( $comment_id ? $comment_id : false );
if ( !$edit_id ) return;
$type = $comment_id ? 'comment' : ( 'emqa-question' == get_post_type( $edit_id ) ? 'question' : 'answer' );
?>
<?php do_action( 'emqa_before_edit_form' ); ?>
<form method="post" class="emqa-content-edit-form" enctype="multipart/form-data">
	<?php if ( 'emqa-question' == get_post_type( $edit_id ) ) : ?>
	<?php $title = emqa_question_get_edit_title( $edit_id ) ?>
	<p>
		<label for="question_title"><?php _e( 'Title', 'emqa' ) ?></label>
		<input type="text" name="question_title" value="<?php echo $title ?>" tabindex="1">
	</p>
	<?php endif; ?>
	<?php $content = call_user_func( 'emqa_' . $type . '_get_edit_content', $edit_id ); ?>
	<p><?php emqa_init_tinymce_editor( array( 'content' => $content, 'textarea_name' => $type . '_content', 'wpautop' => true ) ) ?></p>
	<?php if ( 'emqa-question' == get_post_type( $edit_id ) ) : ?>
	<p>
		<label for="question-category"><?php _e( 'Category', 'emqa' ) ?></label>
		<?php $category = wp_get_post_terms( $edit_id, 'emqa-question_category' ); ?>
		<?php
			wp_dropdown_categories( array(
				'name'          => 'question-category',
				'id'            => 'question-category',
				'taxonomy'      => 'emqa-question_category',
				'show_option_none' => __( 'Select question category', 'emqa' ),
				'hide_empty'    => 0,
				'quicktags'     => array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close' ),
				'selected'      => isset( $category[0]->term_id ) ? $category[0]->term_id : false,
			) );
		?>
	</p>
	<p>
		<label for="question-tag"><?php _e( 'Tag', 'emqa' ) ?></label>
		<input type="text" class="" name="question-tag" value="<?php emqa_get_tag_list( get_the_ID(), true ); ?>" >
	</p>
	<?php endif; ?>
	<?php do_action('emqa_after_show_content_edit', $edit_id); ?>
	<?php do_action( 'emqa_before_edit_submit_button' ) ?>
	<input type="hidden" name="<?php echo $type ?>_id" value="<?php echo $edit_id ?>">
	<?php wp_nonce_field( '_emqa_edit_' . $type ) ?>
	<input type="submit" name="emqa-edit-<?php echo $type ?>-submit" value="<?php _e( 'Save Changes', 'emqa' ) ?>" >
</form>
<?php do_action( 'emqa_after_edit_form' ); ?>
