<?php

class EMQA_Admin_Welcome {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'welcome' ) );
	}

	public function welcome() {
		$activated = get_option( 'emqa_plugin_activated', false );
		if ( $activated ) {
			delete_option( 'emqa_plugin_activated' );
			wp_safe_redirect( esc_url( add_query_arg( array( 'page' => 'emqa-changelog' ), admin_url( 'index.php' ) ) ) );
			exit;
		}
	}

	public function admin_notices() {
		if ( !isset( $_COOKIE['qa-pro-notice'] ) ) {
			echo '<div id="emqa-message" class="notice is-dismissible"><p>To support this plugin and get more features, <a href="http://bit.ly/dwqa-pro" target="_blank">upgrade to EMO Questalk &rarr;</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
	}

	public function admin_menus() {
		add_dashboard_page(
			__( 'Welcome to EMO Questalk', 'emqa' ),
			__( 'Welcome to EMO Questalk', 'emqa' ),
			'manage_options',
			'emqa-about',
			array( $this, 'about_layout' )
		);

		add_dashboard_page(
			__( 'EMO Questalk Changelog', 'emqa' ),
			__( 'EMO Questalk Changelog', 'emqa' ),
			'manage_options',
			'emqa-changelog',
			array( $this, 'changelog_layout' )
		);

		add_dashboard_page(
			__( 'EMO Questalk Credits', 'emqa' ),
			__( 'EMO Questalk Credits', 'emqa' ),
			'manage_options',
			'emqa-credits',
			array( $this, 'credits_layout' )
		);
	}

	public function admin_init() {
		remove_submenu_page( 'index.php', 'emqa-about' );
		remove_submenu_page( 'index.php', 'emqa-changelog' );
		remove_submenu_page( 'index.php', 'emqa-credits' );
	}

	public function page_head() {
		global $emqa;
		?>
		
		<h1><?php
		// translators: %1$s is replaced with the version number
		 printf( __( 'Welcome to EMO Questalk %1$s', 'emqa' ), $emqa->version ) ?></h1>
		<p class="about-text"><?php _e( 'Thank you for installing our WordPress plugin. If you have any question about this plugin, please submit to our <a target="_blank" href="https://www.emohelp.com/question/">Q&A section</a>.', 'emqa' ); ?></p>
		<?php
	}

	public function tabs() {
		$current_tab = isset( $_GET['page'] ) ? $_GET['page'] : 'emqa-about';
	
		?>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'emqa-about' ), admin_url( 'index.php' ) ) ) ?>" class="nav-tab <?php echo 'emqa-about' == $current_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'emqa-changelog' ), admin_url( 'index.php' ) ) ) ?>" class="nav-tab <?php echo 'emqa-changelog' == $current_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Changelog' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'emqa-credits' ), admin_url( 'index.php' ) ) ) ?>" class="nav-tab <?php echo 'emqa-changelog' == $current_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Credits' ); ?></a>
		</h2>
		<?php
	}


	public function about_layout() {
		global $emqa;
		?>
		<div class="wrap about-wrap">
			<?php $this->page_head(); ?>
			<?php $this->tabs(); ?>
			<p class="about-description">In recent weeks, we have been working hard to enhance our EMO Questalk plugin. Apart from getting bugs fixed and providing technical assistance to our users, our team implemented some new cool features to extend functionality for EMO Questalk.</p>

			
			<div class="feature-section two-col">
				<div class="col">
					<div class="media-container">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-1.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
				<div class="col">
					<h3>Core Performance Improvements</h3>
					<p>Our Developers have replaced the old filter solution using ajax with a new solution via WP_Query. We also omitted some queries and filters which were known as the cause for slowness while listing questions.</p>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<h3>Add Questions Listing By Author page</h3>
					<p>We have replaced and turn the author link as a filter when you click on it. This way allows you to see all the questions asked by that user, or all questions that user follows.</p>
				</div>
				<div class="col">
					<div class="media-container">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-2.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<div class="media-container two-col">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-3.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
				<div class="col">
					<h3>Add Breadcrumbs</h3>
					<p>We have created native breadcrumbs for EMO Questalk. You can also integrate this feature with the other breadcrumbs plugin if you like to enhance navigation system your ways.</p>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<h3>Improve 'Subscribe Question' Feature</h3>
					<p>We enhance 'Subscribe' feature (known as Follow) further, allow users who adds comments or answer such question will be set by default to 'subscribe that question (no longer needs an extra step to subscribe). To stop receiving notifications, you simply 'unsubscribe' the question at your choice.</p>
				</div>
				<div class="col">
					<div class="media-container two-col">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-4.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<div class="media-container">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-5.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
				<div class="col">
					<h3>Optimize Questions &#38; Answers Editing</h3>
					<p>When you click Edit Question/ Answer, we replace content with a form where you can modify your questions/answers. The reason behind this approach is to enable you to flexibly edit not only question/answer content but also extra info like title, category and tag. Comment edit feature remains directed to comment edit via dashboard.</p>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<h3>Simplify the question status</h3>
					<p>We set 3 default status: Open, Resolved and Closed. The old status 'Pending' and 'Re-open' will be marked as 'Open'. We aim at making the question category more simple and make the filter ease to follow.</p>
				</div>
				<div class="col">
					<div class="media-container">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-6.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
			</div>
			<hr>
			<div class="feature-section two-col">
				<div class="col">
					<div class="media-container">
						<img src="<?php echo esc_url( $emqa->uri . 'assets/img/emqa-140-7.gif' ) ?>" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 960px) calc((100vw - 116px) * .476), (max-width: 1290px) calc((100vw - 240px) * .476), 500px">
					</div>
				</div>
				<div class="col">
					<h3>Template Structure Updates</h3>
					<p>We’ve rewrite the entire structure of the EMO Questalk template, so we highly recommend you use new these new template files to take advantage of the latest features. We also had the entire functions optimized; in each template file, instead of deploying too many PHP functions, we use “add_action” snippet to allow you integrate EMO Questalk quickly and easily into your themes.</p>
				</div>
			</div>
			<hr>

		</div>
		<?php
	}

	public function changelog_layout() {
		global $emqa;
		?>
		<div class="wrap about-wrap">
			<?php $this->page_head(); ?>
			<?php $this->tabs(); ?>

			<div class="changelog">
				<p><?php echo $this->parse_changelog(); ?></p>
			</div>
		</div>
		<?php
	}

	public function credits_layout() {
		$contributors = $this->get_contributors();
		?>
		<div class="wrap about-wrap">
			<?php $this->page_head(); ?>
			<?php $this->tabs(); ?>

			<ul class="wp-people-group" id="wp-people-group-project-leaders">
			<?php if ( !empty( $contributors ) ) : ?>
				<h3 class="wp-people-group"><?php _e( 'Contributors', 'emqa' ); ?></h3>
				<?php foreach( $contributors as $contributor ) : ?>
					<li class="wp-person" id="wp-person-nacin">
						<a href="<?php echo esc_url( $contributor->html_url ) ?>">
							<img width="60" height="60" src="<?php echo esc_url( $contributor->avatar_url ) ?>" class="gravatar" alt="<?php echo esc_html( $contributor->login ) ?>">
						</a>
						<a href="<?php echo esc_url( $contributor->html_url ) ?>"><?php echo esc_html( $contributor->login ) ?></a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
			</ul>
		</div>
		<?php
	}

	public function parse_changelog() {
		$file = file_exists( EMQA_DIR . 'changelog.txt' ) ? EMQA_DIR . 'changelog.txt' : false;
	
		if ( !$file ) {
			$changelog = '<p>' . __( 'No valid changelog was found.', 'emqa' ) . '</p>';
		} else {
			$changelog_content = wp_remote_get( $file );
			$changelog = nl2br( esc_html( $changelog_content ) );
		}
	
		return $changelog;
	}
	

	public function get_contributors() {
		$response = wp_remote_get( 'https://api.github.com/repos/emohelp/emo-questlak/contributors?per_page=999', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$contributors = wp_remote_retrieve_body( $response );
		$contributors = json_decode( $contributors );

		if ( !is_array( $contributors ) ) return array();

		return $contributors;
	}
}

?>
