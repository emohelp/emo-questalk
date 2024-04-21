<?php
if ( !defined( 'ABSPATH' ) ) exit;

class EMQA_Autoclosure {
	private $days = 1;
	public function __construct() {
		global $emqa_general_settings;
		if(isset($emqa_general_settings['use-auto-closure']) && $emqa_general_settings['use-auto-closure']){
			if(isset($emqa_general_settings['number-day-auto-closure']) && is_numeric($emqa_general_settings['number-day-auto-closure']) && $emqa_general_settings['number-day-auto-closure']>0){
				
				$this->days = $emqa_general_settings['number-day-auto-closure'];
				
				add_filter( 'cron_schedules', array($this, 'emqa_add_schedule') );
				
				if (! wp_next_scheduled ( 'auto_closure' )) {
					wp_schedule_event(time(), 'half_daily', 'auto_closure');
				}
				
				add_action('auto_closure', array($this, 'do_auto_closure'));
			}
		}else{
			wp_clear_scheduled_hook( 'auto_closure' );
		}
	}
	
	public function do_auto_closure(){
		$days = $this->days;
		$posts = get_posts(array(
			'post_type' => 'emqa-question',
			'date_query' => array(
								array(
									'column' => 'post_modified_gmt',
									'before' => $days.' day ago',
								),
						),
			'meta_query' => array(
								array(
									'key'	=> '_emqa_status',
									'value' => 'closed',
									'compare' =>'!='
								)
						)
		));
		foreach($posts as $value){
			update_post_meta( $value->ID, '_emqa_status', 'closed' );
		}
	}
	
	public function emqa_add_schedule( $schedules ) {
		// add a 'weekly' schedule to the existing set
		/* $schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __('Once Weekly', 'emqa')
		);
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => __('Once a month', 'emqa')
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __('Minutely', 'emqa')
		); */
		$schedules['half_daily'] = array(
			'interval' => 43200,
			'display' => __('Half Daily', 'emqa')
		);
		return $schedules;
	}
	
}
?>