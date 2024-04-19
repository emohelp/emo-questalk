<?php  
/**
 * Generate html for metabox of question status meta data
 * @param  object $post Post Object
 * @return void       
 */
function emqa_question_status_box_html( $post ){
		$meta = get_post_meta( $post->ID, '_emqa_status', true );
		$meta = $meta ? $meta : 'open';
	?>
	<p>
		<label for="emqa-question-status">
			<?php _e( 'Status','em-question-answer' ) ?><br>&nbsp;
			<select name="emqa-question-status" id="emqa-question-status" class="widefat">
				<option <?php selected( $meta, 'open' ); ?> value="open"><?php _e( 'Open','em-question-answer' ) ?></option>
				<option <?php selected( $meta, 'pending' ); ?> value="pending"><?php _e( 'Pending','em-question-answer' ) ?></option>
				<option <?php selected( $meta, 'resolved' ); ?> value="resolved"><?php _e( 'Resolved','em-question-answer' ) ?></option>
				<option <?php selected( $meta, 're-open' ); ?> value="re-open"><?php _e( 'Re-Open','em-question-answer' ) ?></option>
				<option <?php selected( $meta, 'closed' ); ?> value="closed"><?php _e( 'Closed','em-question-answer' ) ?></option>
			</select>
		</label>
	</p>    
	<p>
		<label for="emqa-question-sticky">
			<?php _e( 'Sticky','em-question-answer' ); ?><br><br>&nbsp;
			<?php
				$sticky_questions = get_option( 'emqa_sticky_questions', array() );
			?>
			<input <?php checked( true, in_array( $post->ID, $sticky_questions ), true ); ?> type="checkbox" name="emqa-question-sticky" id="emqa-question-sticky" value="1" ><span class="description"><?php _e( 'Pin question to top of archive page.','em-question-answer' ); ?></span>
		</label>
	</p>
	<?php
}

class EMQA_Metaboxes {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'answers_metabox' ) );
		add_filter( 'postbox_classes_emqa-question_emqa-answers', array( $this, 'add_css_class_metabox' ) );
		add_action( 'admin_init', array( $this, 'add_status_metabox' ) );
		add_action( 'save_post', array( $this, 'question_status_save' ) );
	}

	//Add a metabox that was used for display list of answers of a questions
	public function answers_metabox(){
		add_meta_box( 'emqa-answers', __( 'Answers','em-question-answer' ), array( $this, 'metabox_answers_list' ), 'emqa-question' );
	}

	/**
	 * generate html for metabox that was used for display list of answers of a questions
	 */
	public function metabox_answers_list(){
		$answer_list_table = new EMQA_Answer_List_Table();
		$answer_list_table->display();
	}

	public function add_css_class_metabox( $classes ){
		$classes[] = 'emqa-answer-list';
		return $classes;
	}
	/**
	 * Add metabox for question status meta data
	 * @return void
	 */
	public function add_status_metabox(){
		add_meta_box( 'emqa-post-status', __( 'Question Meta Data','em-question-answer' ), 'emqa_question_status_box_html', 'emqa-question', 'side', 'high' );
	}

	public function question_status_save( $post_id ){
		if ( ! wp_is_post_revision( $post_id ) ) {
			if ( isset( $_POST['emqa-question-status'] ) ) {
				update_post_meta( $post_id, '_emqa_status', esc_html( $_POST['emqa-question-status'] ) );
			}
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

				$sticky_questions = get_option( 'emqa_sticky_questions', array() );
				if ( isset( $_POST['emqa-question-sticky'] ) && sanitize_text_field( $_POST['emqa-question-sticky'] ) ) {
					if ( ! in_array( $post_id, $sticky_questions ) ) {
						$sticky_questions[] = $post_id;
						update_option( 'emqa_sticky_questions', $sticky_questions );
					}
				} else {
					if ( in_array( $post_id, $sticky_questions ) ) {
						if ( ($key = array_search( $post_id, $sticky_questions ) ) !== false ) {
							unset( $sticky_questions[$key] );
						}
						update_option( 'emqa_sticky_questions', $sticky_questions );
					}
				}
			}
		}
	}
}

?>