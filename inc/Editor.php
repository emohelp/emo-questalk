<?php  

function emqa_init_tinymce_editor( $args = array() ) {
	global $emqa;
	$emqa->editor->display( $args );
}

function emqa_paste_srtip_disable( $mceInit ){
	$mceInit['paste_strip_class_attributes'] = 'none';
	return $mceInit;
}

class EMQA_Editor {

	public function __construct() {

		add_action( 'init', array( $this, 'tinymce_addbuttons' ) );

		add_filter( 'emqa_prepare_edit_answer_content', 'wpautop' );
		add_filter( 'emqa_prepare_edit_question_content', 'wpautop' );
	}
	
	public function tinymce_addbuttons() {
		if ( get_user_option( 'rich_editing' ) == 'true' && ! is_admin() ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_custom_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_custom_button' ) );
		}
	}

	public function register_custom_button( $buttons ) {
		array_push( $buttons, '|', 'emqaCodeEmbed' );
		return $buttons;
	} 

	public function add_custom_tinymce_plugin( $plugin_array ) {
		global $emqa_options;
		if ( is_singular( 'emqa-question' ) || ( $emqa_options['pages']['submit-question'] && is_page( $emqa_options['pages']['submit-question'] ) ) ) {
			$plugin_array['emqaCodeEmbed'] = EMQA_URI . 'assets/js/code-edit-button.js';
		}
		return $plugin_array;
	}
	public function display( $args ) {
		extract( wp_parse_args( $args, array(
				'content'       => '',
				'id'            => 'emqa-custom-content-editor',
				'textarea_name' => 'custom-content',
				'rows'          => 5,
				'wpautop'       => false,
				'media_buttons' => false,
		) ) );

		$emqa_tinymce_css = apply_filters( 'emqa_editor_style', EMQA_URI . 'templates/assets/css/editor-style.css' );
		$toolbar1 = apply_filters( 'emqa_tinymce_toolbar1', 'bold,italic,underline,|,' . 'bullist,numlist,blockquote,|,' . 'link,unlink,|,' . 'image,code,|,'. 'spellchecker,fullscreen,emqaCodeEmbed,|,' );
		wp_editor( $content, $id, array(
			'wpautop'       => $wpautop,
			'media_buttons' => $media_buttons,
			'textarea_name' => $textarea_name,
			'textarea_rows' => $rows,
			'tinymce' => array(
					'toolbar1' => $toolbar1,
					'toolbar2'   => '',
					'content_css' => $emqa_tinymce_css
			),
			'quicktags'     => true,
		) );
	}

	public function toolbar_buttons() {

	}
}

?>