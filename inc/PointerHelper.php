<?php  

class EMQA_PointerHelper {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'pointer_load' ), 1000 );
		add_filter( 'emqa_admin_pointers-edit-emqa-question', array( $this, 'register_pointer_testing' ) );
	}

	public function pointer_load( $hook_suffix ) {
 
		// Don't run on WP < 3.3
		if ( get_bloginfo( 'version' ) < '3.3' )
			return;
	 
		$screen = get_current_screen();
		$screen_id = $screen->id;

		// Get pointers for this screen
		$pointers = apply_filters( 'emqa_admin_pointers-' . $screen_id, array() );

		if ( ! $pointers || ! is_array( $pointers ) )
			return;
	 
		// Get dismissed pointers
		$dismissed = get_user_option( 'dismissed_wp_pointers', get_current_user_id() );
		$dismissed = explode( ',', $dismissed );
		$valid_pointers = array();
	 
		// Check pointers and remove dismissed ones.
		foreach ( $pointers as $pointer_id => $pointer ) {
			// Sanity check
			if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
				continue;
			
			$pointer['pointer_id'] = $pointer_id;
	 
			// Add the pointer to $valid_pointers array
			$valid_pointers['pointers'][] = $pointer;
		}
	 
		// No valid pointers? Stop here.
		if ( empty( $valid_pointers ) )
			return;
	 
		// Add pointers style to queue.
		wp_enqueue_style( 'wp-pointer' );
	 
		// Add pointers script to queue. Add custom script.
		wp_enqueue_script(
			'emqa-pointer',                        // Handle
			EMQA_URI . 'assets/js/admin-pointer-helper.js', // Script URL
			array('jquery', 'wp-pointer'),         // Dependencies
			'1.0.0',                               // Version (update this when the script changes)
			true                                   // Load in footer (set to `true` for footer, `false` for header)
		);
			 
		// Add pointer options to script.
		wp_localize_script( 'emqa-pointer', 'emqaPointer', $valid_pointers );
	}

	public function register_pointer_testing( $p ) {
		$p['document'] = array(
			'target' => '#contextual-help-link',
			'options' => array(
				'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'How to use EMO Questalk', 'emqa' ),
					__( 'Documents, Support From Emohelp', 'emqa' )
				),
				'position' => array( 'edge' => 'top', 'align' => 'right' )
			)
		);
		$p['settings'] = array(
			'target' => '#adminmenu a[href="edit.php?post_type=emqa-question&page=emqa-settings"]',
			'options' => array(
				'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'Config your support channel', 'emqa' ),
					__( 'Change comment settings, and create the Submit question page.', 'emqa' )
				),
				'position' => array( 'edge' => 'left', 'align' => 'middle' )
			)
		);
		return $p;
	}
}


?>