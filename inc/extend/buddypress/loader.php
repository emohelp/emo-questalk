<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'EMQA_QA_Component' ) ) :

class EMQA_QA_Component extends BP_Component {

	public function __construct() {
		parent::start(
			'emqa',
			__( 'EMQA', 'em-question-answer' ),
			EMQA_DIR .'inc/extend/buddypress/'
		);
		$this->includes();
		$this->setup_globals();
		$this->fully_loaded();
	}

	public function includes( $includes = array() ) {

		$includes[] = 'functions.php';

		if ( bp_is_active( 'notifications' ) ) {
			$includes[] = 'notifications.php';
		}

		parent::includes( $includes );
	}

	public function setup_globals( $args = array() ) {
		$bp = buddypress();

		// Define a slug, if necessary
		if ( !defined( 'BP_EMQA_SLUG' ) )
			define( 'BP_EMQA_SLUG', 'emqa' );
			// define( 'BP_EMQA_SLUG', $this->id );

		$args = array(
			'path'          => BP_PLUGIN_DIR,
			'slug'          => BP_EMQA_SLUG,
			'root_slug'     => BP_EMQA_SLUG,
			'has_directory' => false,
			'search_string' => __( 'Search EMQA...', 'em-question-answer' ),
		);

		parent::setup_globals( $args );
	}

	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// Stop if there is no user displayed or logged in
		if ( !is_user_logged_in() && !bp_displayed_user_id() )
			return;

		// Define local variable(s)
		$user_domain = '';

		// Add 'EMQA' to the main navigation
		$main_nav = array(
			'name'                => __( 'EMQA', 'em-question-answer' ),
			'slug'                => $this->slug,
			'position'            => 80,
			'screen_function'     => 'dp_emqa_screen_questions',
			'default_subnav_slug' => 'emqa-question',
			'item_css_id'         => $this->id
		);

		// Determine user to use
		if ( bp_displayed_user_id() )
			$user_domain = bp_displayed_user_domain();
		elseif ( bp_loggedin_user_domain() )
			$user_domain = bp_loggedin_user_domain();
		else
			return;

		// User link
		$emqa_link = trailingslashit( $user_domain . $this->slug );

		$sub_nav[] = array(
			'name'            => __( 'Questions', 'em-question-answer' ),
			'slug'            => 'emqa-question',
			'parent_url'      => $emqa_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'dp_emqa_screen_questions',
			'position'        => 20,
			'item_css_id'     => 'topics'
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the admin bar
	 *
	 * @since bbPress (r3552)
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		if ( !bp_use_wp_admin_bar() || defined( 'DOING_AJAX' ) )
			return;
		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$user_domain = bp_loggedin_user_domain();
			$emqa_link = trailingslashit( $user_domain . $this->slug );

			// Add the "My Account" sub menus
			$wp_admin_nav[] = array(
				'parent' => buddypress()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'EMQA', 'em-question-answer' ),
				'href'   => trailingslashit( $emqa_link )
			);
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id.'-question',
				'title'  => __( 'Questions', 'em-question-answer' ),
				'href'   => trailingslashit( $emqa_link )
			);
		 
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	private function fully_loaded() {
		do_action_ref_array( 'bp_emqa_buddypress_loaded', array( $this ) );
	}
}
endif;
