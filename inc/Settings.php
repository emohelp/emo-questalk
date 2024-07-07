<?php  

// Callback for emqa-general-settings Option
function emqa_question_registration_setting_display() {
	global  $emqa_general_settings;
	?>
	<p><input type="checkbox" name="emqa_options[answer-registration]" value="true" <?php checked( true, isset( $emqa_general_settings['answer-registration'] ) ? (bool ) $emqa_general_settings['answer-registration'] : false ); ?> id="emqa_option_answer_registation">
	<label for="emqa_option_answer_registation"><span class="description"><?php esc_html_e( 'Login required. No anonymous post allowed','emqa' ); ?></span></label></p>
	<?php
}

function emqa_pages_settings_display() {
	global  $emqa_general_settings;
	$archive_question_page = isset( $emqa_general_settings['pages']['archive-question'] ) ? $emqa_general_settings['pages']['archive-question'] : 0; 
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'emqa_options[pages][archive-question]',
				'show_option_none'  => esc_html(__( 'Select Archive Question Page','emqa' )),
				'option_none_value' => 0,
				'selected'          => esc_html($archive_question_page),
			) );
		?><br><span class="description"><?php esc_html_e( 'A page where displays all questions. The <code>[emqa-list-questions]</code> short code must be on this page.','emqa' ) ?></span>
	</p>
	<?php
}

function emqa_question_new_time_frame_display() { 
	global  $emqa_general_settings;
	echo '<p><input type="text" name="emqa_options[question-new-time-frame]" id="emqa_options_question_new_time_frame" value="'.esc_attr( isset( $emqa_general_settings['question-new-time-frame'] ) ? $emqa_general_settings['question-new-time-frame'] : 4 ).'" class="small-text" /><span class="description"> '.esc_html(__( 'hours','emqa' )).'<span title="'.esc_html(__( 'A period of time in which new questions are highlighted and marked as New','emqa' ) ).'">( ? )</span></span></p>';
}

function emqa_question_overdue_time_frame_display() { 
	global  $emqa_general_settings;
	echo '<p><input type="text" name="emqa_options[question-overdue-time-frame]" id="emqa_options_question_new_time_frame" value="'.esc_attr( isset( $emqa_general_settings['question-overdue-time-frame'] ) ? $emqa_general_settings['question-overdue-time-frame'] : 2 ).'" class="small-text" /><span class="description"> '.esc_html(__( 'days','emqa' )).'<span title="'.esc_html(__( 'A Question will be marked as overdue if it passes this period of time, starting from the time the question was submitted','emqa' ) ).'">( ? )</span></span></p>';
}

function emqa_submit_question_page_display(){
	global  $emqa_general_settings;
	$submit_question_page = isset( $emqa_general_settings['pages']['submit-question'] ) ? $emqa_general_settings['pages']['submit-question'] : 0; 
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'emqa_options[pages][submit-question]',
				'show_option_none'  => esc_html(__( 'Select Submit Question Page','emqa' )),
				'option_none_value' => 0,
				'selected'          => esc_html($submit_question_page),
			) );
		?><br>
		<span class="description"><?php esc_html_e( 'A page where users can submit questions. The <code>-submit-question-form]</code> short code must be on this page.','emqa' ) ?></span>
	</p>
	<?php
}

function emqa_404_page_display(){
	global  $emqa_general_settings;
	$submit_question_page = isset( $emqa_general_settings['pages']['404'] ) ? $emqa_general_settings['pages']['404'] : 0; 
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'emqa_options[pages][404]',
				'show_option_none'  => esc_html(__( 'Select 404 emQA Page','emqa' )),
				'option_none_value' => 0,
				'selected'          => esc_html($submit_question_page),
			) );
		?>
		<span class="description"><?php esc_html_e( 'This page will be redirected when users without authority click on a private question. You can customize the message of this page in.If not, a default 404 page will be used.','emqa' ) ?></span>
	</p>
	<?php
}
function emqa_email_template_settings_display(){
	global $emqa_options;
	$editor_content = isset( $emqa_options['subscribe']['email-template'] ) ? $emqa_options['subscribe']['email-template'] : '';
	wp_editor( $editor_content, 'emqa_email_template_editor', array(
		'textarea_name' => 'emqa_options[subscribe][email-template]'
	) );
}


function emqa_subscrible_email_logo_display(){
	wp_enqueue_media();
	?>
	<div class="uploader">
		<p><input type="text" name="emqa_subscrible_email_logo" id="emqa_subscrible_email_logo" class="regular-text" value="<?php echo esc_attr(get_option( 'emqa_subscrible_email_logo' )); ?>" />&nbsp;<input type="button" class="button" name="emqa_subscrible_email_logo_button" id="emqa_subscrible_email_logo_button" value="<?php esc_html_e( 'Upload','emqa' ) ?>" /></br><span class="description">&nbsp;<?php esc_html_e( 'Upload or choose a logo to be displayed at the top of the email.','emqa' ) ?></span></p>
	</div>
	<script type="text/javascript">
	jQuery( document ).ready(function($ ){
	  var _custom_media = true,
		  _orig_send_attachment = wp.media.editor.send.attachment;

	  $( '#emqa_subscrible_email_logo_button' ).click(function(e ) {
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $( this );
		var id = button.attr( 'id' ).replace('_button', '' );
		_custom_media = true;
		wp.media.editor.send.attachment = function( props, attachment ){
		  if ( _custom_media ) {
			$( "#"+id ).val(attachment.url );

			if ( $( "#"+id ).closest( '.uploader' ).find('.logo-preview' ).length > 0 ) {
				$( "#"+id ).closest( '.uploader' ).find('.logo-preview img' ).attr( 'src', attachment.url );
			}else {
				$( "#"+id ).closest( '.uploader' ).append('<p class="logo-preview"><img src="'+attachment.url+'"></p>' )
			}
		  } else {
			return _orig_send_attachment.apply( this, [props, attachment] );
		  };
		}

		wp.media.editor.open( button );
		return false;
	  } );

	  $( '.add_media' ).on('click', function(){
		_custom_media = false;
	  } );
	} );
	</script>
	<?php
}

function emqa_subscrible_enable_new_question_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'emqa_subscrible_enable_new_question_notification', 1 ), false ).' name="emqa_subscrible_enable_new_question_notification" id="emqa_subscrible_enable_new_question_notification" ><span class="description">'.esc_html(__( 'Enable notification for new question.', 'emqa' )).'</span></td>';
}
// New Question - Enable Notification

function emqa_subscrible_new_question_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_question_email_subject" name="emqa_subscrible_new_question_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_question_email_subject' )).'" class="regular-text" /></span></td>';
}
// New Question - Email subject

function emqa_subscrible_new_question_email_display(){
	echo '<th for="emqa_subscrible_new_question_email">'.esc_html(__( 'Email Content','emqa' )).'</th>';
	echo '<td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_question_email', 'new-question' );
	wp_editor( $content, 'emqa_subscrible_new_question_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-question.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new question on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{user_avatar}</strong> - Question Author Avatar. <br />
		<strong>{username}</strong> - Question Author Name. <br />
		<strong>{user_link}</strong> - Question Author Posts Link.<br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{question_content}</strong> - Question Content. <br />
	</div>';
	echo '</td>';
}
// New Question - Email Content


function emqa_subscrible_enable_new_answer_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'emqa_subscrible_enable_new_answer_notification', 1 ), false ).' name="emqa_subscrible_enable_new_answer_notification" id="emqa_subscrible_enable_new_answer_notification" ><span class="description">'.esc_html(__( 'Enable notification for new answer.', 'emqa' )).'</span></td>';
}
// New Answer - Enable Notification

function emqa_subscrible_new_answer_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_answer_email_subject" name="emqa_subscrible_new_answer_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_answer_email_subject' )).'" class="regular-text" /></span></td>';
}
// New Answer - Email Subject

function emqa_subscrible_new_answer_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th>';
	echo '<td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_answer_email', 'new-answer' );
	wp_editor( $content, 'emqa_subscrible_new_answer_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-answer.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_avatar}</strong> - Answer Author Avatar. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{answer_author_link}</strong> - Answer Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{answer_content}</strong> - Answer Content. <br />

	</div>';
	echo '</td>';
}
// New Answer - Email Content

function emqa_subscrible_enable_new_answer_followers_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'emqa_subscrible_enable_new_answer_followers_notification', 1 ), false ).' name="emqa_subscrible_enable_new_answer_followers_notification" id="emqa_subscrible_enable_new_answer_followers_notification" ><span class="description">'.esc_html(__( 'Enable notification for new answer ( to Followers ).', 'emqa' )).'</span></td>';
}
// New Answer - Follow - Enable Notification

function emqa_subscrible_new_answer_followers_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_answer_followers_email_subject" name="emqa_subscrible_new_answer_followers_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_answer_followers_email_subject' )).'" class="regular-text" /></span></td>';
}
// New Answer - Follow - Email Subject

function emqa_subscrible_new_answer_followers_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th>';
	echo '<td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_answer_followers_email', 'new-answer-followers' );
	wp_editor( $content, 'emqa_subscrible_new_answer_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-answer-followers.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_avatar}</strong> - Answer Author Avatar. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{answer_author_link}</strong> - Answer Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{answer_content}</strong> - Answer Content. <br />

	</div>';
	echo '</td>';
}
// New Answer - Follow - Email Content

function emqa_subscrible_enable_new_comment_question_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" '.checked( 1, get_option( 'emqa_subscrible_enable_new_comment_question_notification', 1 ), false ).' value="1" name="emqa_subscrible_enable_new_comment_question_notification" id="emqa_subscrible_enable_new_comment_question_notification" ><span class="description">'.esc_html(__( 'Enable notification for new comment of question.', 'emqa' )).'</span></td>';
}
// New Comment - Question - Enable Notification

function emqa_subscrible_new_comment_question_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_comment_question_email_subject" name="emqa_subscrible_new_comment_question_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_comment_question_email_subject' )).'" class="regular-text" /></td>';
}
// New Comment - Question - Email subject

function emqa_subscrible_new_comment_question_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th><td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_comment_question_email', 'new-comment-question' );
	wp_editor( $content, 'emqa_subscrible_new_comment_question_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-editor="emqa_subscrible_new_comment_question_email" data-template="new-comment-question.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{question_author}</strong> - Question Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Question - Email Content

function emqa_subscrible_enable_new_comment_question_followers_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" '.checked( 1, get_option( 'emqa_subscrible_enable_new_comment_question_followers_notify', 1 ), false ).' value="1" name="emqa_subscrible_enable_new_comment_question_followers_notify" id="emqa_subscrible_enable_new_comment_question_followers_notify" ><span class="description">'.esc_html(__( 'Enable notification for new comment of question.', 'emqa' )).'</span></td>';
}
// New Comment - Question - Follow - Enable Notification

function emqa_subscrible_new_comment_question_followers_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_comment_question_followers_email_subject" name="emqa_subscrible_new_comment_question_followers_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_comment_question_followers_email_subject' )).'" class="widefat" /></td>';
}
// New Comment - Question - Follow - Email subject

function emqa_subscrible_new_comment_question_followers_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th><td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_comment_question_followers_email', 'new-comment-question-followers' );
	wp_editor( $content, 'emqa_subscrible_new_comment_question_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-question-followers.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{question_author}</strong> - Question Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Question - Follow - Email Content

function emqa_subscrible_enable_new_comment_answer_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" '.checked( 1, get_option( 'emqa_subscrible_enable_new_comment_answer_notification', 1 ), false ).' value="1" name="emqa_subscrible_enable_new_comment_answer_notification" id="emqa_subscrible_enable_new_comment_answer_notification" ><span class="description">'.esc_html(__( 'Enable notification for new comment of answer.', 'emqa' )).'</span></td>';
}
// New Comment - Answer - Enable Notification

function emqa_subscrible_new_comment_answer_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_comment_answer_email_subject" name="emqa_subscrible_new_comment_answer_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_comment_answer_email_subject' )).'" class="regular-text" /></td>';
}
// New Comment - Answer - Email Subject

function emqa_subscrible_new_comment_answer_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th><td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_comment_answer_email', 'new-comment-answer' );
	wp_editor( $content, 'emqa_subscrible_new_comment_answer_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-answer.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Answer - Email Content

function emqa_subscrible_enable_new_comment_answer_followers_notification(){
	echo '<th>'.esc_html(__( 'Enable?','emqa' )).'</th><td><input type="checkbox" '.checked( 1, get_option( 'emqa_subscrible_enable_new_comment_answer_followers_notification', 1 ), false ).' value="1" name="emqa_subscrible_enable_new_comment_answer_followers_notification" id="emqa_subscrible_enable_new_comment_answer_followers_notification" ><span class="description">'.esc_html(__( 'Enable notification for new comment of answer.', 'emqa' )).'</span></td>';
}
// New Comment - Answer - Follow - Enable Notification

function emqa_subscrible_new_comment_answer_followers_email_subject_display(){ 
	echo '<th>'.esc_html(__( 'Email subject','emqa' )).'</th><td><input type="text" id="emqa_subscrible_new_comment_answer_followers_email_subject" name="emqa_subscrible_new_comment_answer_followers_email_subject" value="'.esc_attr(get_option( 'emqa_subscrible_new_comment_answer_followers_email_subject' )).'" class="regular-text" /></td>';
}
// New Comment - Answer - Follow - Email Subject

function emqa_subscrible_new_comment_answer_followers_email_display(){
	echo '<th>'.esc_html(__( 'Email Content','emqa' )).'</th><td>';
	$content = emqa_get_mail_template( 'emqa_subscrible_new_comment_answer_followers_email', 'new-comment-answer-followers' );
	wp_editor( $content, 'emqa_subscrible_new_comment_answer_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => EMQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-answer-followers.html" type="button" class="button emqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Answer - Follow - Email Content

// End email setting html 

function emqa_question_rewrite_display(){
	global  $emqa_general_settings;
	echo '<p><input type="text" name="emqa_options[question-rewrite]" id="emqa_options_question_rewrite" value="'.esc_attr( isset( $emqa_general_settings['question-rewrite'] ) ? $emqa_general_settings['question-rewrite'] : 'question' ).'" class="regular-text" /></p>';
}

function emqa_question_category_rewrite_display(){
	global  $emqa_general_settings;
	echo '<p><input type="text" name="emqa_options[question-category-rewrite]" id="emqa_options_question_category_rewrite" value="'.esc_attr( isset( $emqa_general_settings['question-category-rewrite'] ) ? $emqa_general_settings['question-category-rewrite'] : 'question-category' ).'" class="regular-text" /></p>';
}

function emqa_question_tag_rewrite_display(){
	global  $emqa_general_settings;
	echo '<p><input type="text" name="emqa_options[question-tag-rewrite]" id="emqa_options_question_tag_rewrite" value="'.esc_attr( isset( $emqa_general_settings['question-tag-rewrite'] ) ? $emqa_general_settings['question-tag-rewrite'] : 'question-tag' ).'" class="regular-text" /></p>';
}

function EMQA_Permission_display(){
	global $emqa;
	$perms = $emqa->permission->perms;
	$roles = get_editable_roles();
	?>
	<input type="hidden" id="reset-permission-nonce" name="reset-permission-nonce" value="<?php echo esc_attr(wp_create_nonce( '_emqa_reset_permission' )); ?>">
	<h3><?php esc_html_e( 'Questions','emqa' ) ?></h3>
	<table class="table widefat emqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th><?php esc_html_e( 'Read','emqa' ) ?></th>
				<th><?php esc_html_e( 'Post','emqa' ) ?></th>
				<th><?php esc_html_e( 'Edit','emqa' ) ?></th>
				<th><?php esc_html_e( 'Delete','emqa' ) ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo esc_html($roles[$key]['name']) ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['read'] ) ? $perms[$key]['question']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][question][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['post'] ) ? $perms[$key]['question']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][question][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['edit'] ) ? $perms[$key]['question']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][question][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['delete'] ) ? $perms[$key]['question']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][question][delete]" value="1"></td>
			   
			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php esc_html_e( 'Anonymous','emqa' ) ?></td>

				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['read'] ) ? $perms['anonymous']['question']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][question][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['post'] ) ? $perms['anonymous']['question']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][question][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['edit'] ) ? $perms['anonymous']['question']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][question][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['delete'] ) ? $perms['anonymous']['question']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][question][delete]" value="1" disabled="disabled"></td>
			</tr>
		</tbody>
	</table>
	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="question" class="button reset-permission" name="emqa-permission-reset" value="question"><?php esc_html_e( 'Reset Default', 'emqa' ); ?></button>
	</p>
	<h3><?php esc_html_e( 'Answers', 'emqa' ); ?></h3>
	<table class="table widefat emqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th>Read</th>
				<th>Post</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo esc_html($roles[$key]['name']) ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['read'] ) ? $perms[$key]['answer']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][answer][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['post'] ) ? $perms[$key]['answer']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][answer][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['edit'] ) ? $perms[$key]['answer']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][answer][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['delete'] ) ? $perms[$key]['answer']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][answer][delete]" value="1"></td>

			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php esc_html_e( 'Anonymous','emqa' ) ?></td>

				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['read'] ) ? $perms['anonymous']['answer']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][answer][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['post'] ) ? $perms['anonymous']['answer']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][answer][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['edit'] ) ? $perms['anonymous']['answer']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][answer][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['delete'] ) ? $perms['anonymous']['answer']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][answer][delete]" value="1" disabled="disabled"></td>
			</tr>
		</tbody>
	</table>
	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="answer" class="button reset-permission" name="emqa-permission-reset" value="answer"><?php esc_html_e( 'Reset Default', 'emqa' ); ?></button>
	</p>
	<h3><?php esc_html_e( 'Comments','emqa' ) ?></h3>
	<table class="table widefat emqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th>Read</th>
				<th>Post</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo esc_html($roles[$key]['name']) ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['read'] ) ? $perms[$key]['comment']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][comment][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['post'] ) ? $perms[$key]['comment']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][comment][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['edit'] ) ? $perms[$key]['comment']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][comment][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['delete'] ) ? $perms[$key]['comment']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo esc_attr($key) ?>][comment][delete]" value="1"></td>
			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php esc_html_e( 'Anonymous','emqa' ) ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['read'] ) ? $perms['anonymous']['comment']['read'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][comment][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['post'] ) ? $perms['anonymous']['comment']['post'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][comment][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['edit'] ) ? $perms['anonymous']['comment']['edit'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][comment][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['delete'] ) ? $perms['anonymous']['comment']['delete'] : false ) ); ?> name="EMQA_Permission[<?php echo 'anonymous' ?>][comment][delete]" value="1" disabled="disabled"  ></td>
			</tr>
		</tbody>
	</table>

	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="comment" class="button reset-permission" name="emqa-permission-reset" value="comment"><?php esc_html_e( 'Reset Default', 'emqa' ); ?></button>
	</p>
	<?php
}

//Captcha
function emqa_captcha_in_question_display() {
	global $emqa_general_settings;

	echo '<p><input type="checkbox" name="emqa_options[captcha-in-question]"  id="emqa_options_captcha_in_question" value="1" '.checked( 1, (isset($emqa_general_settings['captcha-in-question'] ) ? $emqa_general_settings['captcha-in-question'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Enable captcha on submit question page.','emqa' )).'</span></p>';
}

function emqa_captcha_in_single_question_display() {
	global $emqa_general_settings;
	
	echo '<p><input type="checkbox" name="emqa_options[captcha-in-single-question]"  id="emqa_options_captcha_in_question" value="1" '.checked( 1, (isset($emqa_general_settings['captcha-in-single-question'] ) ? $emqa_general_settings['captcha-in-single-question'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Enable captcha on single question page.','emqa' )).'</span></p>';
}

function emqa_captcha_google_pubic_key_display() {
	global $emqa_general_settings;
	$public_key = isset( $emqa_general_settings['captcha-google-public-key'] ) ?  $emqa_general_settings['captcha-google-public-key'] : '';
	echo '<p><input type="text" name="emqa_options[captcha-google-public-key]" value="'.esc_attr($public_key).'" class="regular-text"></p>';
}

function emqa_captcha_google_private_key_display() {
	global $emqa_general_settings;
	$private_key = isset( $emqa_general_settings['captcha-google-private-key'] ) ?  $emqa_general_settings['captcha-google-private-key'] : '';
	echo '<p><input type="text" name="emqa_options[captcha-google-private-key]" value="'.esc_attr($private_key).'" class="regular-text"></p>';
}

function emqa_captcha_select_type_display() {
	global $emqa_general_settings;

	$types = apply_filters( 'emqa_captcha_type', array( 'default' => __( 'Default', 'emqa' ) ) );
	$total = count( $types );
	$type_selected = isset( $emqa_general_settings['captcha-type'] ) ? $emqa_general_settings['captcha-type'] : 'default';
	echo '<select name="emqa_options[captcha-type]">';
	foreach( $types as $key => $name ) {
		echo '<option '.selected( $key, $type_selected, false ).' value="'.esc_attr($key).'">'.esc_html($name).'</option>';
	}
	echo '</select>';
}

function emqa_posts_per_page_display(){
	global $emqa_general_settings;
	$posts_per_page = isset( $emqa_general_settings['posts-per-page'] ) ?  $emqa_general_settings['posts-per-page'] : 5;
	echo '<p><input type="text" name="emqa_options[posts-per-page]" class="small-text" value="'.esc_attr($posts_per_page).'" > <span class="description">'.esc_html(__( 'questions.','emqa' )).'</span></p>';
}

function emqa_answer_per_page_display() {
	global $emqa_general_settings;
	$posts_per_page = isset( $emqa_general_settings['answer-per-page'] ) ?  $emqa_general_settings['answer-per-page'] : 5;
	echo '<p><input id="emqa_setting_answers_per_page" type="text" name="emqa_options[answer-per-page]" class="small-text" value="'.esc_attr($posts_per_page).'" > <span class="description">'.esc_html(__( 'answers.','emqa' )).'</span></p>';
}

function emqa_allow_anonymous_vote() {
	global $emqa_general_settings;
	
	echo '<p><label for="emqa_options_allow_anonymous_vote"><input type="checkbox" name="emqa_options[allow-anonymous-vote]"  id="emqa_options_allow_anonymous_vote" value="1" '.checked( 1, (isset($emqa_general_settings['allow-anonymous-vote'] ) ? $emqa_general_settings['allow-anonymous-vote'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Allow anonymous vote.', 'emqa' )).'</span></label></p>';
}

function emqa_use_akismet_antispam() {
	global $emqa_general_settings;
	
	echo '<p><label for="emqa_options_use_akismet_antispam"><input type="checkbox" name="emqa_options[use-akismet-antispam]"  id="emqa_options_use_akismet_antispam" value="1" '.checked( 1, (isset($emqa_general_settings['use-akismet-antispam'] ) ? $emqa_general_settings['use-akismet-antispam'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Enable Akismet', 'emqa' )).'</span></label></p>';
}

function emqa_akismet_api_key() {
	global $emqa_general_settings;

	$akismet_api_key = isset( $emqa_general_settings['akismet-api-key'] ) ?  $emqa_general_settings['akismet-api-key'] : '';
	echo '<p><input id="emqa_setting_akismet_api_key" type="text" name="emqa_options[akismet-api-key]" class="medium-text" value="'.esc_attr($akismet_api_key).'" ><br><span class="description">'.esc_html(__( 'Get in', 'emqa' )).' <a href="https://akismet.com">akismet.com</a>'.'</span></p>';
}

function emqa_akismet_connection_status() {
	global $emqa_general_settings;
	
	$status = esc_html(__( 'Not Connected', 'emqa' ));
	
	if(isset($emqa_general_settings['use-akismet-antispam']) && $emqa_general_settings['use-akismet-antispam']){
		//enable akismet
		if ( class_exists( 'emQA_Akismet' ) ){
			if(emQA_Akismet::akismet_verify_key($emqa_general_settings['akismet-api-key'])){
				$status = __( 'Connected', 'emqa' );
			}
		}
	}

	echo '<p>'.esc_html($status).'</p>';
}

function emqa_use_auto_closure() {
	global $emqa_general_settings;
	
	echo '<p><label for="emqa_options_use_auto_closure"><input type="checkbox" name="emqa_options[use-auto-closure]"  id="emqa_options_use_auto_closure" value="1" '.checked( 1, (isset($emqa_general_settings['use-auto-closure'] ) ? $emqa_general_settings['use-auto-closure'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Enable Auto Closure', 'emqa' )).'</span></label></p>';
}
function emqa_number_day_auto_closure() {
	global $emqa_general_settings;
	$number_day_auto_closure = isset( $emqa_general_settings['number-day-auto-closure'] ) ?  $emqa_general_settings['number-day-auto-closure'] : '';
	echo '<p><input id="emqa_setting_number_day_auto_closure" type="text" name="emqa_options[number-day-auto-closure]" class="medium-text" value="'.esc_attr($number_day_auto_closure).'" > <span class="description">'.esc_html(__( 'Days.(greater 0)','emqa' )).'</span></p>';
}



function emqa_enable_private_question_display() {
	global $emqa_general_settings;
	
	echo '<p><label for="emqa_options_enable_private_question"><input type="checkbox" name="emqa_options[enable-private-question]"  id="emqa_options_enable_private_question" value="1" '.checked( 1, (isset($emqa_general_settings['enable-private-question'] ) ? $emqa_general_settings['enable-private-question'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Allow members to post private question.','emqa' )).'</span></label></p>';
}

function emqa_enable_review_question_mode() {
	global $emqa_general_settings;
	
	echo '<p><label for="emqa_options_enable_review_question"><input type="checkbox" name="emqa_options[enable-review-question]"  id="emqa_options_enable_review_question" value="1" '.checked( 1, (isset($emqa_general_settings['enable-review-question'] ) ? $emqa_general_settings['enable-review-question'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Question must be manually approved.','emqa' )).'</span></label></p>';
}

function emqa_show_status_icon() {
	global $emqa_general_settings;

	echo '<p><label for="emqa_options_enable_show_status_icon"><input type="checkbox" name="emqa_options[show-status-icon]"  id="emqa_options_enable_show_status_icon" value="1" '.checked( 1, (isset($emqa_general_settings['show-status-icon'] ) ? $emqa_general_settings['show-status-icon'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Display status icon on the left side.', 'emqa' )).'</span></label></p>';
}

function emqa_disable_question_status() {
	global $emqa_general_settings;

	echo '<p><label for="emqa_options_emqa_disable_question_status"><input type="checkbox" name="emqa_options[disable-question-status]"  id="emqa_options_emqa_disable_question_status" value="1" '.checked( 1, (isset($emqa_general_settings['disable-question-status'] ) ? $emqa_general_settings['disable-question-status'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Disable question status feature.', 'emqa' )).'</span></label></p>';
}

function emqa_show_all_answers() {
	global $emqa_general_settings;

	echo '<p><label for="emqa_options_emqa_show_all_answers"><input type="checkbox" name="emqa_options[show-all-answers-on-single-question-page]"  id="emqa_options_emqa_show_all_answers" value="1" '.checked( 1, (isset($emqa_general_settings['show-all-answers-on-single-question-page'] ) ? $emqa_general_settings['show-all-answers-on-single-question-page'] : false ) , false ) .'><span class="description">'.esc_html(__( 'Show all answers on single question page.', 'emqa' )).'</span></label></p>';
}

function emqa_single_template_options() {
	global $emqa_general_settings;
	$selected = isset( $emqa_general_settings['single-template'] ) ? $emqa_general_settings['single-template'] : -1;
	$theme_path = trailingslashit( get_template_directory() );
	$files = scandir( $theme_path );
	?>
		<p><label for="emqa_single_question_template">
				<select name="emqa_options[single-template]" id="emqa_single_question_template">
					<option <?php selected( $selected, -1 ); ?> value="-1"><?php esc_html_e( 'Select template for Single Quesiton page','emqa' ) ?></option>
					<?php foreach ( $files as $file ) : ?>
						<?php $ext = pathinfo( $file, PATHINFO_EXTENSION ); ?>
						<?php if ( is_dir( $file ) || strpos( $file, '.' === 0 ) || $ext != 'php' ) continue; ?>
					<option <?php selected( $selected, $file ); ?> value="<?php echo esc_attr($file); ?>"><?php echo esc_html($file) ?></option>
					<?php endforeach; ?>
				</select> <span class="description"><?php esc_html_e( 'By default, your single.php template file will be used if you do not choose any template', 'emqa' ) ?></span>
			</label>
		</p>
	<?php

}

function emqa_permalink_section_layout() {
	printf(
    // translators: %s is replaced with an example of a custom question URL structure
    esc_html(__( 'If you like, you may enter custom structure for your single question, question category, and question tag URLs here. For example, using <code>topic</code> as your question base would make your question links like <code>%s</code>. If you leave these blank, the default will be used.', 'emqa' )),
    esc_url(home_url( 'topic/question-name/' ))
	);

}

function emqa_get_rewrite_slugs() {
	global  $emqa_general_settings;
	$emqa_general_settings = get_option( 'emqa_options' );
	
	$rewrite_slugs = array();

	$question_rewrite = get_option( 'emqa-question-rewrite', 'question' );
	$question_rewrite = $question_rewrite ? $question_rewrite : 'question';
	if ( isset( $emqa_general_settings['question-rewrite'] ) && $emqa_general_settings['question-rewrite'] && $emqa_general_settings['question-rewrite'] != $question_rewrite ) {
		$question_rewrite = $emqa_general_settings['question-rewrite'];
		update_option( 'emqa-question-rewrite', $question_rewrite );
	}

	$rewrite_slugs['question_rewrite'] = $question_rewrite;

	$question_category_rewrite = $emqa_general_settings['question-category-rewrite'];
	$question_category_rewrite = $question_category_rewrite ? $question_category_rewrite : 'question-category';
	if ( isset( $emqa_general_settings['question-category-rewrite'] ) && $emqa_general_settings['question-category-rewrite'] && $emqa_general_settings['question-category-rewrite'] != $question_category_rewrite ) {
		$question_category_rewrite = $emqa_general_settings['question-category-rewrite'];
		update_option( 'emqa-question-category-rewrite', $question_category_rewrite );
	}

	$rewrite_slugs['question_category_rewrite'] = $question_category_rewrite;

	$question_tag_rewrite = $emqa_general_settings['question-tag-rewrite'];
	$question_tag_rewrite = $question_tag_rewrite ? $question_tag_rewrite : 'question-tag';
	if ( isset( $emqa_general_settings['question-tag-rewrite'] ) && $emqa_general_settings['question-tag-rewrite'] && $emqa_general_settings['question-tag-rewrite'] != $question_tag_rewrite ) {
		$question_tag_rewrite = $emqa_general_settings['question-tag-rewrite'];
		update_option( 'emqa-question-tag-rewrite', $question_tag_rewrite );
	}
	$rewrite_slugs['question_tag_rewrite'] = $question_tag_rewrite;

	return $rewrite_slugs;
}


function emqa_is_captcha_enable() {
	global $emqa_general_settings;
	$public_key = isset( $emqa_general_settings['captcha-google-public-key'] ) ?  $emqa_general_settings['captcha-google-public-key'] : '';
	$private_key = isset( $emqa_general_settings['captcha-google-private-key'] ) ?  $emqa_general_settings['captcha-google-private-key'] : '';

	if ( ! $public_key || ! $private_key ) {
		return false;
	}
	return true;
}

function emqa_is_captcha_enable_in_submit_question() {
	global $emqa_general_settings;
	$captcha_in_question = isset( $emqa_general_settings['captcha-in-question'] ) ? $emqa_general_settings['captcha-in-question'] : false;
	
	if ( $captcha_in_question ) {
		return true;
	}
	return false;
}

function emqa_is_captcha_enable_in_single_question() {
	global $emqa_general_settings;
	$captcha_in_single_question = isset( $emqa_general_settings['captcha-in-single-question'] ) ? $emqa_general_settings['captcha-in-single-question'] : false;
	if ( $captcha_in_single_question ) {
		return true;
	} 
	return false;
}

function emqa_is_enable_status() {
	global $emqa_general_settings;

	if ( !isset( $emqa_general_settings['disable-question-status'] ) || !$emqa_general_settings['disable-question-status'] ) {
		return true;
	}

	return false;
}

class EMQA_Settings {
	public function __construct(){
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'init_options' ), 9 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'updated_option', array( $this, 'update_options' ), 10, 3 );
		add_action( 'wp_loaded', array( $this, 'flush_rules' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	public function enqueue_script() {
		wp_enqueue_script( 'emqa-admin-settings-page', EMQA_URI . 'assets/js/admin-settings-page.js', array( 'jquery' ), true );
	}

	public function update_options( $option, $old_value, $value ) {
		if ( $option == 'emqa_options' ) {
			if ( $old_value['pages']['archive-question'] != $value['pages']['archive-question']  ) {
				$questions_page_content = get_post_field( 'post_content', $value['pages']['archive-question'] );
				if ( strpos( $questions_page_content, '[emqa-list-questions]' ) === false ) {
					$questions_page_content = str_replace( '[emqa-submit-question-form]', '', $questions_page_content );
					wp_update_post( array(
						'ID'			=> $value['pages']['archive-question'],
						'post_content'	=> $questions_page_content . '[emqa-list-questions]',
					) );
				}
			}

			if ( $old_value['pages']['submit-question'] != $value['pages']['submit-question'] ) {
				$submit_question_content = get_post_field( 'post_content', $value['pages']['submit-question'] );
				if ( strpos( $submit_question_content, '[emqa-submit-question-form]' ) === false ) {
					$submit_question_content = str_replace( '[emqa-list-questions]', '', $submit_question_content );
					wp_update_post( array(
						'ID'			=> $value['pages']['submit-question'],
						'post_content'	=> $submit_question_content . '[emqa-submit-question-form]',
					) );
				}
			}
			
			// Flush rewrite when rewrite rule settings change
			flush_rewrite_rules();
		}
	}

	// Create admin menus for backend
	public function admin_menu(){
		global $emqa_setting_page;
		$emqa_setting_page = add_submenu_page( 'edit.php?post_type=emqa-question', __( 'Plugin Settings','emqa' ), __( 'Settings','emqa' ), 'manage_options', 'emqa-settings', array( $this, 'settings_display' )  );
	}   

	public function init_options(){
		global $emqa_options, $emqa_general_settings;
		$emqa_general_settings = $emqa_options = wp_parse_args( get_option( 'emqa_options' ), array( 
			'pages'     => array(
					'submit-question'   => 0,
					'archive-question'  => 0,
				),
			'question-category-rewrite' => '',
			'question-tag-rewrite' => '',
			'captcha-in-single-question' => false,
			'question-new-time-frame' => 4,
		) );
	}

	public function flush_rules() {
		if ( isset( $_GET['page'] ) && 'emqa-settings' == esc_html( $_GET['page'] ) ) {
			flush_rewrite_rules();
		}
	}

	public function current_email_tab() {
		if ( isset( $_GET['tab'] ) && 'email' == esc_html( $_GET['tab'] ) ) {
			return isset( $_GET['section'] ) ? esc_html( $_GET['section'] ) : 'general';
		}

		return false;
	}

	public function email_tabs() {
		$section = $this->current_email_tab();
		ob_start();
		?>
		<ul class="subsubsub">
			<li class="<?php echo $section == 'general' ? 'active' : '' ?>"><a href="<?php echo esc_url(add_query_arg( 'section', 'general', admin_url( 'edit.php?post_type=emqa-question&page=emqa-settings&tab=email' ) ) ) ?>"><?php esc_html_e( 'Email Settings', 'emqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-question' ? 'active' : '' ?>"><a href="<?php echo esc_url(add_query_arg( 'section', 'new-question', admin_url( 'edit.php?post_type=emqa-question&page=emqa-settings&tab=email' ) )) ?>"><?php esc_html_e( 'New Question Notifications', 'emqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-answer' ? 'active' : '' ?>"><a href="<?php echo esc_attr(add_query_arg( 'section', 'new-answer', admin_url( 'edit.php?post_type=emqa-question&page=emqa-settings&tab=email' ) )) ?>"><?php esc_html_e( 'New Answer Notifications', 'emqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-comment' ? 'active' : '' ?>"><a href="<?php echo esc_url(add_query_arg( 'section', 'new-comment', admin_url( 'edit.php?post_type=emqa-question&page=emqa-settings&tab=email' )) ) ?>"><?php esc_html_e( 'New Comment Notifications', 'emqa' ) ?></a></li>
		</ul>
		<div class="clear"></div>
		<?php
		return ob_get_clean();
	}

	public function register_settings(){
		global  $emqa_general_settings;

		//Register Setting Sections
		add_settings_section( 
			'emqa-general-settings', 
			__( 'Page Settings', 'emqa' ),
			null, 
			'emqa-settings' 
		);

		add_settings_field( 
			'emqa_options[pages][archive-question]', 
			__( 'Question List Page', 'emqa' ), 
			'emqa_pages_settings_display', 
			'emqa-settings', 
			'emqa-general-settings'
		);

		add_settings_field( 
			'emqa_options[pages][submit-question]', 
			__( 'Ask Question Page', 'emqa' ), 
			'emqa_submit_question_page_display', 
			'emqa-settings', 
			'emqa-general-settings'
		);

		// add_settings_field( 
		// 	'emqa_options[single-template]', 
		// 	__( 'Single Question Template', 'emqa' ), 
		// 	'emqa_single_template_options', 
		// 	'emqa-settings', 
		// 	'emqa-general-settings' 
		// );

		do_action( 'emqa_register_setting_section' );

		//Time setting
//		add_settings_section( 
//			'emqa-time-settings', 
//			__( 'Time settings','emqa' ), 
//			null, 
//			'emqa-settings' 
//		);
//
//		add_settings_field( 
//			'emqa_options[question-new-time-frame]', 
//			__( 'New Question Time Frame', 'emqa' ), 
//			'emqa_question_new_time_frame_display', 
//			'emqa-settings', 
//			'emqa-time-settings'
//		);
//
//		add_settings_field( 
//			'emqa_options[question-overdue-time-frame]', 
//			__( 'Question Overdue - Time Frame', 'emqa' ), 
//			'emqa_question_overdue_time_frame_display', 
//			'emqa-settings', 
//			'emqa-time-settings'
//		);

		// Question Settings
		add_settings_section(
			'emqa-misc-settings',
			__( 'Question Settings', 'emqa' ),
			false,
			'emqa-settings'
		);

		add_settings_field( 
			'emqa_options[posts-per-page]', 
			__( 'Archive Page Show At Most','emqa' ), 
			'emqa_posts_per_page_display', 
			'emqa-settings', 
			'emqa-misc-settings' 
		);

		add_settings_field( 
			'emqa_options[enable-review-question]', 
			__( 'Before A Question Appears', 'emqa' ), 
			'emqa_enable_review_question_mode', 
			'emqa-settings', 
			'emqa-misc-settings'
		);

		add_settings_field( 
			'emqa_options[enable-private-question]', 
			__( 'Other Question Settings', 'emqa' ), 
			'emqa_enable_private_question_display', 
			'emqa-settings', 
			'emqa-misc-settings'
		);

		add_settings_field(
			'emqa_options[disable-question-status]',
			'',
			'emqa_disable_question_status',
			'emqa-settings',
			'emqa-misc-settings'
		);

		add_settings_field(
			'emqa_options[show-status-icon]',
			'',
			'emqa_show_status_icon',
			'emqa-settings',
			'emqa-misc-settings'
		);

		// Answer Settings
		add_settings_section(
			'emqa-answer-settings',
			__( 'Answer Settings', 'emqa' ),
			false,
			'emqa-settings'
		);

		add_settings_field(
			'emqa_options[show-all-answers-on-single-question-page]',
			__( 'Answer Listing', 'emqa' ),
			'emqa_show_all_answers',
			'emqa-settings',
			'emqa-answer-settings'
		);

		add_settings_field( 
			'emqa_options[answer-per-page]', 
			false, 
			'emqa_answer_per_page_display', 
			'emqa-settings', 
			'emqa-answer-settings' 
		);
		
		// Vote Settings
		add_settings_section(
			'emqa-vote-settings',
			__( 'Vote Settings', 'emqa' ),
			false,
			'emqa-settings'
		);

		add_settings_field(
			'emqa_options[allow-anonymous-vote]',
			__( 'Allow Anonymous Vote', 'emqa' ),
			'emqa_allow_anonymous_vote',
			'emqa-settings',
			'emqa-vote-settings'
		);

		// Akismet Settings
		add_settings_section(
			'emqa-akismet-settings',
			__( 'Akismet Settings', 'emqa' ),
			false,
			'emqa-settings'
		);

		add_settings_field(
			'emqa_options[use-akismet-antispam]',
			__( 'Use Akismet anti-spam', 'emqa' ),
			'emqa_use_akismet_antispam',
			'emqa-settings',
			'emqa-akismet-settings'
		);
		add_settings_field(
			'emqa_options[akismet-api-key]',
			__( 'Akismet API key', 'emqa' ),
			'emqa_akismet_api_key',
			'emqa-settings',
			'emqa-akismet-settings'
		);
		add_settings_field(
			'emqa_options[akismet-connection-status]',
			__( 'Akismet connection status', 'emqa' ),
			'emqa_akismet_connection_status',
			'emqa-settings',
			'emqa-akismet-settings'
		);
		
		//Auto closure Settings
		add_settings_section(
			'emqa-auto-closure-settings',
			__( 'Auto Closure Settings', 'emqa' ),
			false,
			'emqa-settings'
		);

		add_settings_field(
			'emqa_options[use-auto-closure]',
			__( 'Use Auto Closure', 'emqa' ),
			'emqa_use_auto_closure',
			'emqa-settings',
			'emqa-auto-closure-settings'
		);
		add_settings_field(
			'emqa_options[number-day-auto-closure]',
			__( 'Closure after', 'emqa' ),
			'emqa_number_day_auto_closure',
			'emqa-settings',
			'emqa-auto-closure-settings'
		);
		
		//Captcha Setting

		add_settings_section( 
			'emqa-captcha-settings', 
			__( 'Captcha Settings','emqa' ), 
			null, 
			'emqa-settings' 
		);

		add_settings_field( 
			'emqa_options[captcha-type]', 
			__( 'Type', 'emqa' ), 
			'emqa_captcha_select_type_display',
			'emqa-settings', 
			'emqa-captcha-settings'
		);

		add_settings_field( 
			'emqa_options[captcha-in-question]', 
			__( 'Ask Question Page', 'emqa' ), 
			'emqa_captcha_in_question_display', 
			'emqa-settings',
			'emqa-captcha-settings'
		);

		add_settings_field( 
			'emqa_options[captcha-in-single-question]', 
			__( 'Single Question Page', 'emqa' ), 
			'emqa_captcha_in_single_question_display', 
			'emqa-settings', 
			'emqa-captcha-settings'
		);

		do_action( 'emqa_captcha_setting_field' );


		//Permalink
		add_settings_section( 
			'emqa-permalink-settings', 
			__( 'Permalink Settings','emqa' ), 
			'emqa_permalink_section_layout',
			'emqa-settings' 
		);

		add_settings_field( 
			'emqa_options[question-rewrite]', 
			__( 'Question Base', 'emqa' ), 
			'emqa_question_rewrite_display', 
			'emqa-settings', 
			'emqa-permalink-settings'
		);

		add_settings_field( 
			'emqa_options[question-category-rewrite]', 
			__( 'Question Category Base', 'emqa' ), 
			'emqa_question_category_rewrite_display', 
			'emqa-settings', 
			'emqa-permalink-settings'
		);

		add_settings_field( 
			'emqa_options[question-tag-rewrite]', 
			__( 'Question Tag Base', 'emqa' ), 
			'emqa_question_tag_rewrite_display', 
			'emqa-settings', 
			'emqa-permalink-settings'
		);

		register_setting( 'emqa-settings', 'emqa_options' );
		
		add_settings_section( 
			'emqa-subscribe-settings', 
			false,
			false,
			'emqa-email' 
		);

		add_settings_section(
			'emqa-subscribe-settings-new-question',
			false,
			false,
			'emqa-email'
		);

		add_settings_section(
			'emqa-subscribe-settings-new-answer',
			false,
			false,
			'emqa-email'
		);

		add_settings_section(
			'emqa-subscribe-settings-new-comment',
			false,
			false,
			'emqa-email'
		);

		// Send to address setting
		// add_settings_field( 
		//     'emqa_subscrible_sendto_address', 
		//     __( 'Admin Email', 'emqa' ), 
		//     array( $this, 'email_sendto_address_display' ), 
		//     'emqa-email', 
		//     'emqa-subscribe-settings'
		// );
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_sendto_address' );

		// Cc address setting
		// add_settings_field( 
		//     'emqa_subscrible_cc_address', 
		//     __( 'Cc', 'emqa' ), 
		//     array( $this, 'email_cc_address_display' ), 
		//     'emqa-email', 
		//     'emqa-subscribe-settings'
		// );
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_cc_address' );

		// Bcc address setting
		// add_settings_field( 
		//     'emqa_subscrible_bcc_address', 
		//     __( 'Bcc', 'emqa' ), 
		//     array( $this, 'email_bcc_address_display' ), 
		//     'emqa-email', 
		//     'emqa-subscribe-settings'
		// );
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_bcc_address' );

		// Bcc address setting
		add_settings_field( 
			'emqa_subscrible_from_address', 
			__( 'From Email', 'emqa' ), 
			array( $this, 'email_from_address_display' ), 
			'emqa-email', 
			'emqa-subscribe-settings'
		);
		register_setting( 'emqa-subscribe-settings', 'emqa_subscrible_from_address' );

		//add delay email(need to speed up )
		add_settings_field( 
			'emqa_enable_email_delay', 
			false, 
			array( $this, 'enable_email_delay' ), 
			'emqa-email', 
			'emqa-subscribe-settings'
		);
		register_setting( 'emqa-subscribe-settings', 'emqa_enable_email_delay' );

		// Send copy
		add_settings_field( 
			'emqa_subscrible_send_copy_to_admin', 
			false, 
			array( $this, 'email_send_copy_to_admin' ), 
			'emqa-email', 
			'emqa-subscribe-settings'
		);
		register_setting( 'emqa-subscribe-settings', 'emqa_subscrible_send_copy_to_admin' );

		// Logo setting in for email template
		// add_settings_field( 
		//     'emqa_subscrible_email_logo', 
		//     __( 'Email Logo', 'emqa' ), 
		//     'emqa_subscrible_email_logo_display', 
		//     'emqa-email', 
		//     'emqa-subscribe-settings'
		// );
		register_setting( 'emqa-subscribe-settings', 'emqa_subscrible_email_logo' );

		//New Question Email Notify
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_new_question_email' );
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_new_question_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-question', 'emqa_subscrible_enable_new_question_notification' );

		// New Answer Email Notify
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_new_answer_email' );
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_new_answer_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_enable_new_answer_notification' );
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_new_answer_forward' );
		// New Answer to Followers Email Notify
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_new_answer_followers_email' );
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_new_answer_followers_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-answer', 'emqa_subscrible_enable_new_answer_followers_notification' );

		// New Comment for Question Notify
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_question_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_question_email' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_enable_new_comment_question_notification' );

		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_question_forward' );

		// New Comment for Question to Followers Email Notify
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_question_followers_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_question_followers_email' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_enable_new_comment_question_followers_notify' );

		// New Comment for Answer Email Notify
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_answer_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_answer_email' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_enable_new_comment_answer_notification' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_answer_forward' );

		// New Comment for Answer to Followers Email Notify
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_answer_followers_email_subject' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_new_comment_answer_followers_email' );
		register_setting( 'emqa-subscribe-settings-new-comment', 'emqa_subscrible_enable_new_comment_answer_followers_notification' );


		add_settings_section( 
			'emqa-permission-settings', 
			__( 'Group Permission','emqa' ),
			false,
			'emqa-permission' 
		);

		add_settings_field( 
			'EMQA_Permission', 
			__( 'Group Permission','emqa' ), 
			'EMQA_Permission_display', 
			'emqa-permission', 
			'emqa-permission-settings' 
		);

		register_setting( 'emqa-permission-settings', 'EMQA_Permission' );    
	}

	public function settings_display(){
		global $emqa_general_settings;
		$email_section = $this->current_email_tab();
		?>
		<style type="text/css">
			ul.subsubsub {
			    float: left;
			}

			ul.subsubsub > li {
			    display: inline-block;
			}

			ul.subsubsub > li.active > a {
			    color: #000;
			    font-weight: bold;
			}
			.wrap{
				position: relative;
			}
			.wrap #blog-designwall{
			    position: absolute;
			    top: 200px;
			    right: 0px;
			    width: 300px;
			    height: 300px;
			}
		</style>
		<div class="wrap">
			<h2><?php esc_html_e( 'EMQA Settings', 'emqa' ) ?></h2>
			<?php settings_errors(); ?>  
			<?php $active_tab = isset( $_GET[ 'tab' ] ) ? esc_html( $_GET['tab'] ) : 'general'; ?>  
			<h2 class="nav-tab-wrapper">  
				<a href="?post_type=emqa-question&amp;page=emqa-settings&amp;tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General','emqa' ); ?></a> 
				<a href="?post_type=emqa-question&amp;page=emqa-settings&amp;tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Emails','emqa' ); ?></a> 
				<a href="?post_type=emqa-question&amp;page=emqa-settings&amp;tab=permission" class="nav-tab <?php echo $active_tab == 'permission' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Permissions','emqa' ); ?></a>
				<a href="?post_type=emqa-question&amp;page=emqa-settings&amp;tab=licenses" class="nav-tab <?php echo $active_tab == 'licenses' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Licenses','emqa' ); ?></a> 
			</h2>  
			  
			<form method="post" action="options.php">  
			<?php 

			switch ($active_tab) {
				case 'email':
					
					echo '<div class="emqa-notification-settings">';
					echo wp_kses_post($this->email_tabs());
					// email setup section
					if ( $email_section === 'general' ) :
						settings_fields( 'emqa-subscribe-settings' );
						echo '<h3>'.esc_html(__( 'Email settings','emqa' )).'</h3>';
						echo '<table class="form-table"><tr>';
						echo '<th scope="row">'.esc_html(__( 'Email Logo','emqa' )).'</th><td>';
						emqa_subscrible_email_logo_display();
						echo '</td></tr></table>';
						do_settings_sections( 'emqa-email' );
					endif;

					echo '<div class="emqa-mail-templates">';
					echo '<div class="progress-bar"><div class="progress-bar-inner"></div></div>';

					echo '<div class="tab-content">'; 

					if ( $email_section == 'new-question' ) :
						settings_fields( 'emqa-subscribe-settings-new-question' );
						echo '<div id="new-question" class="tab-pane active">';
						echo '<h3>'.esc_html(__( 'New Question Notifications (to Admin)','emqa' )) . '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_question_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_question_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_question_email_display();
						echo '</tr>';
						echo '<tr>';
						$this->email_sendto_address_display();
						echo '</tr>';
						echo '</table>';
						echo '</div>'; //End tab for New Question Notification
					endif;

					// new answer section
					if ( $email_section == 'new-answer' ) :

						settings_fields( 'emqa-subscribe-settings-new-answer' );
						// new answer to follower section
						echo '<div id="new-answer-followers" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Answer Notifications (to Followers)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_answer_followers_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_answer_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_answer_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>';//End tab for New Answer Notification To Followers

						echo '<div id="new-answer" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Answer Notifications (to Author)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_answer_notification();
						echo '<tr>';
						emqa_subscrible_new_answer_email_subject_display();
						echo '<tr>';
						emqa_subscrible_new_answer_email_display();
						echo '</tr>';
						echo '<tr>';
						$this->new_answer_forward();
						echo '</tr>';
						echo '</table>';
						echo '</div>';//End tab for New Answer Notification

					endif;

					if ( $email_section == 'new-comment' ) :
						settings_fields( 'emqa-subscribe-settings-new-comment' );
						echo '<div id="new-comment-question-followers" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Comment to Question Notifications (to Followers)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_comment_question_followers_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_question_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_question_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Question Notification


						echo '<div id="new-comment-question" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Comment to Question Notifications (to Admin)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_comment_question_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_question_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_question_email_display();
						echo '</tr>';
						echo '<tr>';
						$this->new_comment_question_forward();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Question Notification

						
						echo '<div id="new-comment-answer-followers" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Comment to Answer Notifications (to Followers)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_comment_answer_followers_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_answer_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_answer_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Answer Notification

						
						echo '<div id="new-comment-answer" class="tab-pane">';
						echo '<h3>'.esc_html(__( 'New Comment to Answer Notifications (to Admin)','emqa' )). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						emqa_subscrible_enable_new_comment_answer_notification();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_answer_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						emqa_subscrible_new_comment_answer_email_display();
						echo '</tr>';
						echo '<tr>';
						$this->new_comment_answer_forward();
						echo '</tr>';
						echo '</table>';
						echo '</div>'; //End tab for New Comment to Answer Notification
					endif;

					submit_button( __( 'Save all changes','emqa' ) );
					echo '</div>'; //End wrap mail template settings

					echo '</div>'; //End wrap tab content

					echo '</div>'; //The End
					break;
				case 'permission':
					settings_fields( 'emqa-permission-settings' );
					EMQA_Permission_display();
					submit_button();
					break;
				case 'licenses':
					settings_fields( 'emqa-addons' );
					echo '<p class="description">' . sprintf(
						// translators: %s is replaced with the link to EMQA Extensions
						esc_html(__( 'Manage <a href="%s">EMQA Extensions</a> license keys', 'emqa' )),
						esc_url( add_query_arg(
								array( 'post_type' => 'emqa-question', 'page' => 'emqa-extensions' ),
								admin_url( 'edit.php' )
						) )
					) . '</p>';
				
					do_settings_sections( 'emqa-addons-settings' );
					submit_button();
					break;
				default:
					settings_fields( 'emqa-settings' );
					do_settings_sections( 'emqa-settings' );
					submit_button();
					break;
			}

			?>
			</form>
			<?php 
				$EmqaTab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general'; 
				if ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $EmqaTab == 'general' ) ):
			?>

			<!-- Get blog from emohelp.com -->
			<div id="blog-emohelp">
				<?php  
					// $this->get_blog_emohelp();
				?>
			</div>
			<?php endif;?>
			
		</div>
		<?php
	}

	public function new_answer_forward() {
		echo '<th>'.esc_html(__( 'Forward to', 'emqa' ) ).'</th>';
		$this->textarea_field( 'emqa_subscrible_new_answer_forward' );
	}

	public function new_comment_question_forward() {
		echo '<th>'.esc_html(__( 'Forward to', 'emqa' ) ).'</th>';
		$this->textarea_field( 'emqa_subscrible_new_comment_question_forward' );
	}

	public function new_comment_answer_forward() {
		echo '<th>'.esc_html(__( 'Forward to', 'emqa' ) ).'</th>';
		$this->textarea_field( 'emqa_subscrible_new_comment_answer_forward' );
	}

	public function email_sendto_address_display(){
		echo '<th>'.esc_html(__( 'Forward to', 'emqa' ) ).'</th>';
		$this->textarea_field( 'emqa_subscrible_sendto_address' );
	}

	public function email_cc_address_display(){
		echo '<p>'.esc_html(__( 'Cc', 'emqa' ) ).'</p>';
		$this->input_text_field( 'emqa_subscrible_cc_address' );
	}

	public function email_bcc_address_display(){
		echo '<p>'.esc_html(__( 'Bcc', 'emqa' ) ).'</p>';
		$this->input_text_field( 'emqa_subscrible_bcc_address' );
	}

	public function email_from_address_display(){
		$this->input_text_field( 'emqa_subscrible_from_address', false, esc_html(__( 'This address will be used as the sender of the outgoing emails.','emqa' ) ) );
	}

	public function email_send_copy_to_admin(){
		$this->input_checkbox_field( 
			'emqa_subscrible_send_copy_to_admin',
			esc_html(__( 'Send a copy of every email to admin.','emqa' ) )
		);
	}

	public function enable_email_delay(){
		$this->input_checkbox_field( 
			'emqa_enable_email_delay',
			__( 'Email Delay*','emqa' )
		);
	}

	public function input_text_field( $option, $label = false, $description = false, $class = false ){
		echo '<p><label for="'.esc_attr($option).'"><input type="text" id="'.esc_attr($option).'" name="'.esc_attr($option).'" value="'.esc_attr(get_option( $option )).'" class="regular-text" />';
		if ( $description ) {
			echo '<br><span class="description">'.esc_html($description).'</span>';
		}
		echo '</label></p>';
	}

	public function textarea_field( $option, $lable = false, $description = false, $class = false ) {
		echo '<td><textarea type="text" id="'.esc_attr($option).'" name="'.esc_attr($option).'" rows="5" class="widefat" >'.esc_html(get_option( $option )).'</textarea>';
		if ( $description ) {
			echo '<br><span class="description">'.esc_html($description).'</span>';
		}
		echo '<td>';
	}

	public function input_checkbox_field( $option, $description = false ){
		echo '</p><label for="'.esc_attr($option).'"><input id="'.esc_attr($option).'" name="'.esc_attr($option).'" type="checkbox" '.checked( true, (bool ) get_option( $option ), false ).' value="true"/>';
		if ( $description ) {
			echo '<span class="description">'.esc_html($description).'</span>';
		}
		echo '</label></p>';
	}

	// public function get_blog_emohelp(){
	// 	$url = 'httpS://emohelp.com';
	// 	$response = wp_remote_post( $url, array(
	// 		'method' => 'POST',
	// 		'timeout' => 45,
	// 		'redirection' => 5,
	// 		'httpversion' => '1.0',
	// 		'blocking' => true,
	// 		'headers' => array(),
	// 		'body' => array('plugin_show_blog'=>1),
	// 		'cookies' => array()
	// 	    )
	// 	);
		
	// 	if ( !is_wp_error( $response ) && isset($response['body']) ) {
	// 		$body = json_decode($response['body'], true);
	// 	  	if($body['success'] && isset($body['data']['html'])){
	// 	  		echo $body['data']['html'];
	// 	  	}
	// 	}
	// }
}

?>
