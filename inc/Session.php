<?php

function emqa_add_notice( $message, $type = 'success', $comment = false ) {
	global $emqa;
	$emqa->session->add( $message, $type, $comment );
}

function emqa_clear_notices($comment = false) {
	global $emqa;
	$emqa->session->clear($comment);
}

add_action( 'emqa_before_edit_form', 'emqa_print_notices' );
add_action( 'emqa_before_question_submit_form', 'emqa_print_notices' );
add_action( 'emqa_before_single_question_comment_notice', 'emqa_print_notices');
function emqa_print_notices( $comment = false ) {
	global $emqa;
	echo $emqa->session->print_notices( $comment );
	emqa_clear_notices($comment);
}

function emqa_count_notices( $type = '', $comment = false ) {
	global $emqa;
	return $emqa->session->count( $type, $comment );
}

function emqa_add_wp_error_message( $errors, $comment = false ) {
	if ( is_wp_error( $errors ) ) {
		emqa_add_notice( $errors->get_error_message(), 'error', $comment );
	}
}
function emqa_get_notice_error( $comment = false ) {
	global $emqa;
	$key = $comment ? 'emqa-comment-notices' : 'emqa-notices';
	$all_notices = $emqa->session->get( $key, array() );
	print_r($all_notices);
	if(isset($all_notices['error']) && count($all_notices['error'])>0)
		return $all_notices['error'][0];
	return null;
}




class EMQA_Session {
	protected $_data = array();
	protected $_dirty = false;

	public function __construct() {
		add_action('init', array($this, 'init_session'));
	}

	public function init_session(){
		if(!session_id() && !is_admin()) {
			@ob_start(); // Fix error from user feedback
	        session_start();
	    }
	}

	public function __get( $key ) {
		return $this->get( $key );
	}

	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	public function __isset( $key ) {
		return isset( $_SESSION['emqa_session'][ sanitize_title( $key ) ] );
	}

	public function __unset( $key ) {
		if ( isset( $_SESSION['emqa_session'][ $key ] ) ) {
			unset( $_SESSION['emqa_session'][ $key ] );
			$this->_dirty = true;
		}
	}

	public function get( $key, $default = '' ) {
		$key = sanitize_key( $key );
		return isset( $_SESSION['emqa_session'][ $key ] ) ? maybe_unserialize( $_SESSION['emqa_session'][ $key ] ) : $default;
	}

	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$_SESSION['emqa_session'][ sanitize_key( $key ) ] = maybe_serialize( $value );
			$this->_dirty = true;
		}
	}

	public function add( $message, $type = 'success', $comment = false ) {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before init.', 'emqa' ), '1.4.0' );
			return;
		}

		global $emqa;

		$key = $comment ? 'emqa-comment-notices' : 'emqa-notices';

		$notices = $this->get( $key, array() );

		$notices[ $type ][] = $message;

		$this->set( $key, $notices );
	}

	public function clear($comment = false) {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before init.', 'emqa' ), '1.4.0' );
			return;
		}

		global $emqa;
		if($comment){
			//$this->set( 'emqa-comment-notices', null );
			unset($_SESSION['emqa_session']['emqa-comment-notices']);
		}else{
			// $this->set( 'emqa-notices', null );
			unset($_SESSION['emqa_session']['emqa-notices']);
		}
		
	}

	public function print_notices( $comment = false ) {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before init.', 'emqa' ), '1.4.0' );
			return;
		}

		global $emqa;

		$key = $comment ? 'emqa-comment-notices' : 'emqa-notices';
		$notices = $this->get( $key, array() );
		$types = array( 'error', 'success', 'info' );

		foreach( $types as $type ) {
			if ( $this->count( $type, $comment ) > 0 ) {
				foreach( $notices[ $type ] as $message ) {
					return sprintf( '<p class="emqa-alert emqa-alert-%1$s">%2$s</p>', $type, $message );
				}
			}
		}
		emqa_clear_notices($comment);
	}

	public function count( $type = '', $comment = false ) {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before init.', 'emqa' ), '1.4.0' );
			return;
		}

		$key = $comment ? 'emqa-comment-notices' : 'emqa-notices';
		$all_notices = $this->get( $key, array() );
		$count = 0;
		if ( isset( $all_notices[ $type ] ) ) {
			$count = absint( sizeof( $all_notices[ $type ] ) );
		} elseif ( empty( $type ) ) {
			foreach( $all_notices as $notices ) {
				$count += absint( sizeof( $notices ) );
			}
		}

		return $count;
	}
}