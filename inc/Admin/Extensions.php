<?php

class EMQA_Admin_Extensions {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_extension_menu' ) );
	}

	function register_extensions() {
		global $emqa;
		$extension = array(
			// 'emqa-markdown' => array(
			// 	'name' => __( 'EMQA Markdown', 'emqa' ),
			// 	'url' => 'http://bit.ly/emqa-markdown',
			// 	'img_url' => $emqa->uri . 'assets/img/em-markdown.png'
			// ),

			'emqa-leaderboard' => array(
				'name' => __( 'EMQA Leaderboard', 'emqa' ),
				'url' => 'http://bit.ly/emqa-leaderboard',
				'img_url' => $emqa->uri . 'assets/img/em-leaderboard.png'
			),

			'emqa-captcha' => array(
				'name' => __( 'EMQA Captcha', 'emqa' ),
				'url' => 'http://bit.ly/emqa-captcha',
				'img_url' => $emqa->uri . 'assets/img/em-captcha.png',
			),

			// 'emqa-embed-question' => array(
			// 	'name' => __( 'EMQA Embed Question', 'emqa' ),
			// 	'url' => 'http://bit.ly/emqa-embed-questions',
			// 	'img_url' => $emqa->uri . 'assets/img/em-embedquestion.png'
			// ),

			'emqa-widgets' => array(
				'name' => __( 'EMQA Widgets', 'emqa' ),
				'url'	=> 'http://bit.ly/emqa-widgets',
				'img_url'	=> $emqa->uri . 'assets/img/em-widgets.png'
			),
		);

		return $extension;
	}

	function register_extension_menu() {
		add_submenu_page( 'edit.php?post_type=emqa-question', __( 'Extensions', 'emqa' ), sprintf( '<span style="color: #d54e21;">%s</span>', __( 'Extensions', 'emqa' ) ), 'manage_options', 'emqa-extensions', array( $this, 'extension_menu_layout' ) );
	}

	function extension_menu_layout() {
		$extensions = $this->register_extensions();
		?>
		<div class="wrap">
			<h1>
				<?php echo esc_html(get_admin_page_title()); ?>
				<span class="title-count theme-count"><?php echo count( $extensions ); ?></span>
			</h1>
			<br>
			<div class="theme-browser">
				<div class="themes">
					<?php foreach( $extensions as $slug => $info ) : ?>
						<div class="theme">
							<?php if ( !empty( $info['img_url'] ) ) : ?>
								<div class="theme-screenshot">
									<a target="_blank" href="<?php echo esc_url( $info['url'] ) ?>"><img src="<?php echo esc_url( $info['img_url'] ) ?>"></a>
								</div>
							<?php else : ?>
								<div class="theme-screenshot blank"></div>
							<?php endif; ?>

							<div class="theme-author"></div>

							<h2 class="theme-name" id="<?php echo esc_attr( $slug ) ?>"><span><?php echo esc_attr( $info['name'] ) ?></span></h2>
							<div class="theme-actions">
								<a class="button button-primary" target="_blank" href="<?php echo esc_url( $info['url'] ) ?>"><?php esc_html_e( 'Get It Now!', 'emqa' ); ?></a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="emqa-different">
				<div class="section-header">
					<h2 class="text-center section-title">Differences Between Premium &amp; Free</h2>
					<p class="heading-byline text-center">Differences between the Free and Premium version of em Question &amp; Answer</p>
				</div>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>Features / Options</th>
							<th>Premium Version</th>
							<th>Free Version</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Multi style</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>All Extension</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Markdown Editor</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Anti-spam by Google reCaptchaV2</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Anti-spam by FunCaptcha</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Anti-spam by Akismet</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td>Manual approve answer</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Manual approve question</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td>Anonymous vote</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Mention user</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Permalink friendly SEO</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Upload Files</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Notification Bar</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Ultimate Member Integration</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td>UserPro Integration</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Manager Anonymous info</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Alway Show Admin answer First</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>Login/register Redirect Page</td>
							<td><span class="dashicons dashicons-yes"></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
						</tr>
						<tr>
							<td>The ShortCode</td>
							<td>5</td>
							<td>2</td>
						</tr>
						<tr>
							<td>The Widgets</td>
							<td>7</td>
							<td>4</td>
						</tr>
						<tr>
							<td>Get EMO Questalk Pro</td>
							<td>
								<a href="https://bit.ly/3vVuxYW" target="_blank" class="btn btn-sm btn-danger">Get Pro Version</a>
							<td>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<style>
				.emqa-different .dashicons-yes{
					color: #34a853;
				}
				.emqa-different .dashicons-no-alt{
					color: #d50000;
				}
				.emqa-different table th:nth-child(2), .emqa-different table th:nth-child(3), .emqa-different table td:nth-child(2), .emqa-different table td:nth-child(3){
					text-align: center;
				}
			</style>
		</div>
		<?php
	}
}

?>