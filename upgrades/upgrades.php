<?php  
// Exit if accessed directly
// Upgrade functions
if ( !defined( 'ABSPATH' ) ) exit;


class EMQA_Upgrades {
	public static $db_version;
	private static $version = '1.0.0';

	public static function init() {
		self::$db_version = get_option( 'emqa_version', false );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'admin_menu', array( __CLASS__, 'upgrade_screen' ) );
		add_action( 'wp_ajax_emqa-upgrades', array( __CLASS__, 'ajax_upgrades' ) );
	}

	public static function admin_notices() {
		if ( isset( $_GET['page']) && 'emqa-upgrades' == esc_html( $_GET['page'] ) ) {
			return;
		}

		if ( ! self::$db_version || version_compare( self::$db_version, self::$version, '<') ) {
			printf(
				// translators: %1$s and %2$s are replaced with the start and end of the link respectively
				'<div class="error"><p>' . esc_html__( 'EMO Questalk needs to upgrade the database, click %1$shere%2$s to start the upgrade.', 'emqa' ) . '</p></div>',
				'<a href="' . esc_url( admin_url( 'options.php?page=emqa-upgrades' ) ) . '">',
				'</a>'
			);		
		}
	}

	public static function upgrade_screen() {
		add_submenu_page( null, __( 'EMQA Upgrade', 'emqa' ),  __( 'EMQA Upgrade', 'emqa' ), 'manage_options', 'emqa-upgrades', array( __CLASS__, 'proccess_upgrades' ) );
	}

	public static function proccess_upgrades() {
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title(); ?></h2>
			<p><?php _e('The upgrade process has started, please be patient. This could take several minutes. You will be automatically redirected when the upgrade is finished...','emqa') ?></p>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				function emqaUpgradeSendRequest( restart ) {

					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'emqa-upgrades',
							restart: restart,
						},
					})
					.done(function( resp ) {
						if ( resp.success ) {
							if ( resp.data.finish ) {
								document.location.href = '<?php echo admin_url(); ?>';
							} else {
								emqaUpgradeSendRequest( 0 );
							}
						} else {
							console.log( resp.message );
						}
					});
				}

				emqaUpgradeSendRequest( 1 );
				
			});
			</script>
		</div>
		<?php
	}

	public static function upgrade_question_answer_relationship() {
		global $wpdb;
		$cursor = get_option( 'emqa_upgrades_step', 0 );
		$step = 100;
		$length = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON p.ID = pm.post_id WHERE 1=1 AND post_type = 'emqa-answer' AND pm.meta_key = '_question'" );
		if( $cursor <= $length ) {
			$answers = $wpdb->get_results( $wpdb->prepare( "SELECT ID, meta_value as parent FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON p.ID = pm.post_id WHERE 1=1 AND post_type = 'emqa-answer' AND pm.meta_key = '_question' LIMIT %d, %d ", $cursor, $step ) );

			if ( ! empty( $answers ) ) {
				foreach ( $answers as $answer ) {
					$update = wp_update_post( array( 'ID' => $answer->ID, 'post_parent' => $answer->parent ), true );
				}
				$cursor += $step;
				update_option( 'emqa_upgrades_step', $cursor );
				return $cursor;
			} else {
				delete_option( 'emqa_upgrades_step' );
				return 0;
			}
		} else {
			delete_option( 'emqa_upgrades_step' );
			return 0;
		}
	}

	/**
	 * Will run it on next week. time pause here
	 * @return [type] [description]
	 */
	public static function upgrade_question_status() {
		global $wpdb, $emqa_general_settings;
		$cursor = get_option( 'emqa_upgrades_step', 0 );
		$step = 100;
		$length = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE 1=1 AND post_type = 'emqa-question'" );
		if( $cursor <= $length ) {
			$questions = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_date FROM $wpdb->posts p JOIN $wpdb->posts WHERE 1=1 AND post_type = 'emqa-question' LIMIT %d, %d ", $cursor, $step ) );
			if ( ! empty($questions) ) {
				foreach ( $questions as $question ) {
					$answers = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_date, post_author FROM $wpdb->posts WHERE post_type = 'emqa-answer' AND ( post_status = 'publish' OR post_status = 'private' ) AND post_parent = %d ORDER BY post_date DESC", $question->ID ) );
					$overdue = isset($emqa_general_settings['question-overdue-time-frame']) ? intval( $emqa_general_settings['question-overdue-time-frame'] ) : 2;
				}
				$cursor += $step;
				update_option( 'emqa_upgrades_step', $cursor );
				return $cursor;
			} else {
				// Go Next
				delete_option( 'emqa_upgrades_step' );
				return 0;
			}
		} else {
			// Go Next
			delete_option( 'emqa_upgrades_step' );
			return 0;
		}
	}

	public static function ajax_upgrades() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to do this task', 'emqa' ) ) );
		}

		if ( isset( $_POST['restart'] ) && intval( $_POST['restart'] ) ) {
			delete_option( 'emqa_upgrades_start' );
			$start = 0;
		} else {
			$start = get_option( 'emqa_upgrades_start', 0 );
		}

		switch ( $start ) {
			case 0:
				$start += 1;
				update_option( 'emqa_upgrades_start', $start );
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 0,
					'message' => __( 'Just do it..', 'emqa' )
				) );
				break;
			case 1:
				$do_next = self::upgrade_question_answer_relationship();
				if ( ! $do_next ) {
					$start += 1;
					update_option( 'emqa_upgrades_start', $start );
					// translators: %d is replaced with the version number
					$message = sprintf( __( 'Move to next step %d', 'emqa' ), $start );
				} else {
					$message = $do_next;
				}
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 0,
					'message' => $message
				) );
				break;
			
			default:
				delete_option( 'emqa_upgrades_start' );
				update_option( 'emqa_version', self::$version );
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 1,
					'message' => __('Upgrade process is done','emqa')
				) );
				break;
		}
	}
}
EMQA_Upgrades::init();
?>