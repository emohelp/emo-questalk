<?php
/**
 *  Plugin Name: EMO Questalk
 *  Description: A WordPress plugin was make by emohelp.com to build an Question Answer system for support, asking and comunitcate with your customer
 *  Author: Emohelp
 *  Author URI: http://www.emohelp.com
 *  Version: 1.0.0
 *  Text Domain: em-question-answer
 *  @since 1.4.0
 */

if ( !class_exists( 'EM_Question_Answer' ) ) :

class EM_Question_Answer {
	private $last_update = 180720161357; //last update time of the plugin

	public function __construct() {
		$this->define_constants();
		$this->includes();

		$this->dir = EMQA_DIR;
		$this->uri = EMQA_URI;
		$this->temp_dir = EMQA_TEMP_DIR;
		$this->temp_uri = EMQA_TEMP_URL;
		$this->stylesheet_dir = EMQA_STYLESHEET_DIR;
		$this->stylesheet_uri = EMQA_STYLESHEET_URL;

		$this->version = '1.0.0';

		// load posttype
		$this->question = new EMQA_Posts_Question();
		$this->answer = new EMQA_Posts_Answer();
		$this->comment = new EMQA_Posts_Comment();
		$this->ajax = new EMQA_Ajax();
		$this->handle = new EMQA_Handle();
		$this->permission = new EMQA_Permission();
		$this->status = new EMQA_Status();
		$this->shortcode = new EMQA_Shortcode();
		$this->template = new EMQA_Template();
		$this->settings = new EMQA_Settings();
		$this->editor = new EMQA_Editor();
		$this->user = new EMQA_User();
		$this->notifications = new EMQA_Notifications();
		
		$this->akismet = new EMQA_Akismet();
		$this->autoclosure = new EMQA_Autoclosure();
		
		$this->filter = new EMQA_Filter();
		$this->session = new EMQA_Session();

		$this->metaboxes = new EMQA_Metaboxes();

		$this->helptab = new EMQA_Helptab();
		$this->pointer_helper = new EMQA_PointerHelper();

		// new EMQA_Admin_Extensions();
		new EMQA_Admin_Welcome();

		// All init action of plugin will be included in
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'plugin_action_links', array( $this, 'go_pro' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_rows_meta' ), 10, 2 );
		register_activation_hook( __FILE__, array( $this, 'activate_hook' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_hook' ) );
		
		add_action( 'bp_include', array($this,'emqa_setup_buddypress'), 10 );
	}
	
	public function emqa_setup_buddypress(){
		// Include the BuddyPress Component
		require( EMQA_DIR . 'inc/extend/buddypress/loader.php' );
		
		// Instantiate BuddyPress for bbPress
		$this->EMQA_Buddypress = new EMQA_QA_Component();	
	}

	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public function includes() {
		require_once EMQA_DIR . 'inc/autoload.php';
		require_once EMQA_DIR . 'inc/helper/functions.php';
		// require_once EMQA_DIR . 'upgrades/upgrades.php';
		require_once EMQA_DIR . 'inc/deprecated.php';
		require_once EMQA_DIR . 'inc/helper/plugin-compatibility.php';
		require_once EMQA_DIR . 'inc/helper/theme-compatibility.php';

		require_once EMQA_DIR . 'inc/widgets/Closed_Question.php';
		require_once EMQA_DIR . 'inc/widgets/Latest_Question.php';
		require_once EMQA_DIR . 'inc/widgets/Popular_Question.php';
		require_once EMQA_DIR . 'inc/widgets/Related_Question.php';
	}

	public function define_constants() {
		$defines = array(
			'EMQA_DIR' => plugin_dir_path( __FILE__ ),
			'EMQA_URI' => plugin_dir_url( __FILE__ ),
			'EMQA_TEMP_DIR' => trailingslashit( get_template_directory() ),
			'EMQA_TEMP_URL' => trailingslashit( get_template_directory_uri() ),
			'EMQA_STYLESHEET_DIR' => trailingslashit( get_stylesheet_directory() ),
			'EMQA_STYLESHEET_URL' => trailingslashit( get_stylesheet_directory_uri() ),
		);

		foreach( $defines as $k => $v ) {
			if ( !defined( $k ) ) {
				define( $k, $v );
			}
		}
	}

	public function widgets_init() {
		$widgets = array(
			'EMQA_Widgets_Closed_Question',
			'EMQA_Widgets_Latest_Question',
			'EMQA_Widgets_Popular_Question',
			'EMQA_Widgets_Related_Question'
		);

		foreach( $widgets as $widget ) {
			register_widget( $widget );
		}
	}

	public function init() {
		global $emqa_sript_vars, $emqa_template, $emqa_general_settings;

		$active_template = $this->template->get_template();

		$locale = get_locale();
		$mo = 'em-question-answer-' . $locale . '.mo';
		
		load_textdomain( 'em-question-answer', WP_LANG_DIR . '/em-questalk-free/' . $mo );
		load_textdomain( 'em-question-answer', plugin_dir_path( __FILE__ ) . 'languages/' . $mo );
		load_plugin_textdomain( 'em-question-answer' );

		//Scripts var

		$question_category_rewrite = $emqa_general_settings['question-category-rewrite'];
		$question_category_rewrite = $question_category_rewrite ? $question_category_rewrite : 'question-category';
		$question_tag_rewrite = $emqa_general_settings['question-tag-rewrite'];
		$question_tag_rewrite = $question_tag_rewrite ? $question_tag_rewrite : 'question-tag';
		$emqa_sript_vars = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
		);

		$this->flush_rules();
	}

	// Update rewrite url when active plugin
	public function activate_hook() {
		$this->permission->prepare_permission_caps();

		flush_rewrite_rules();
		//Auto create question page
		$options = get_option( 'emqa_options' );

		if ( ! isset( $options['pages']['archive-question'] ) || ( isset( $options['pages']['archive-question'] ) && ! get_post( $options['pages']['archive-question'] ) ) ) {
			$args = array(
				'post_title' => __( 'EMQA Questions', 'emqa' ),
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_content'  => '[emqa-list-questions]',
			);
			$question_page = get_page_by_path( sanitize_title( $args['post_title'] ) );
			if ( ! $question_page ) {
				$options['pages']['archive-question'] = wp_insert_post( $args );
			} else {
				// Page exists
				$options['pages']['archive-question'] = $question_page->ID;
			}
		}

		if ( ! isset( $options['pages']['submit-question'] ) || ( isset( $options['pages']['submit-question'] ) && ! get_post( $options['pages']['submit-question'] ) ) ) {

			$args = array(
				'post_title' => __( 'EMQA Ask Question', 'emqa' ),
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_content'  => '[emqa-submit-question-form]',
			);
			$ask_page = get_page_by_path( sanitize_title( $args['post_title'] ) );

			if ( ! $ask_page ) {
				$options['pages']['submit-question'] = wp_insert_post( $args );
			} else {
				// Page exists
				$options['pages']['submit-question'] = $ask_page->ID;
			}
		}

		// Valid page content to ensure shortcode was inserted
		$questions_page_content = get_post_field( 'post_content', $options['pages']['archive-question'] );
		if ( strpos( $questions_page_content, '[emqa-list-questions]' ) === false ) {
			$questions_page_content = str_replace( '[emqa-submit-question-form]', '', $questions_page_content );
			wp_update_post( array(
				'ID'			=> $options['pages']['archive-question'],
				'post_content'	=> $questions_page_content . '[emqa-list-questions]',
			) );
		}

		$submit_question_content = get_post_field( 'post_content', $options['pages']['submit-question'] );
		if ( strpos( $submit_question_content, '[emqa-submit-question-form]' ) === false ) {
			$submit_question_content = str_replace( '[emqa-list-questions]', '', $submit_question_content );
			wp_update_post( array(
				'ID'			=> $options['pages']['submit-question'],
				'post_content'	=> $submit_question_content . '[emqa-submit-question-form]',
			) );
		}

		update_option( 'emqa_options', $options );
		update_option( 'emqa_plugin_activated', true );
		// emqa_posttype_init();

		//update option delay email
		update_option('emqa_enable_email_delay', true);
	}

	public function deactivate_hook() {
		$this->permission->remove_permision_caps();

		wp_clear_scheduled_hook( 'emqa_hourly_event' );

		flush_rewrite_rules();
	}

	public function flush_rules() {
		if ( get_option( 'emqa_plugin_activated', false ) || get_option( 'emqa_plugin_upgraded', false ) ) {
			delete_option( 'emqa_plugin_upgraded' );
			flush_rewrite_rules();
		}
	}

	public function get_last_update() {
		return $this->last_update;
	}

	public function go_pro( $actions, $file ) {
		$file_name = plugin_basename( __FILE__ );
		if ( $file == $file_name ) {
			$actions['emqa_go_pro'] = '<a href="https://bit.ly/3vVuxYW" style="color: red; font-weight: bold">Go Pro!</a>';
			$action = $actions['emqa_go_pro'];
			unset( $actions['emqa_go_pro'] );
			array_unshift( $actions, $action );
		}

		return $actions;
	}

	public function plugin_rows_meta( $meta, $file ) {
		$file_name = plugin_basename( __FILE__ );
		if ( $file == $file_name ) {
			$meta['extensions'] = '<a href="'.admin_url( 'edit.php?post_type=emqa-question&page=emqa-extensions' ).'">Extensions</a>';
			// $meta['facebook'] = '<a href="">Facebook</a>';
		}

		return $meta;
	}
}

function emqa() {
	return EM_Question_Answer::instance();
}

$GLOBALS['emqa'] = emqa();

endif;