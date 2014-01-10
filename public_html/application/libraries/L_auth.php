<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class L_auth {
	public function __construct()
    {

        $this->load->library('email');
        $this->load->helper('cookie');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('user_model');

    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

	public function logged_in() {
		// check if login session set
		return (bool) $this->session->userdata('logged_in');
	}

	public function current_user_id() {
		// get current user id from session data
		if($this->logged_in()) {
			$user = $this->session->userdata('logged_in');
			return $user['id'];
		}
		return FALSE;
	}

	public function is_admin_logged_in() {
		// lookup if currently logged in user is admin
		if($this->logged_in()) {
			$user = $this->user_model->get_by_id($this->current_user_id());
			return $user['admin'] == "1";
		} else {
			return FALSE;
		}
	}

	public function logout() {
		// destroy session
		if($this->logged_in()) {
			$this->session->sess_destroy();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function is_disabled() {
		// check if current user is suspended
		if($this->logged_in()) {
			$user = $this->user_model->get_by_id($this->current_user_id());
			if($user['disabled'] == 1) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	public function check_setup() {
		// check if current user is in setup mode
		if($this->logged_in()) {
			$user = $this->user_model->get_by_id($this->current_user_id());
			$code = $user['setup'];
			$this->db->from('users_setup_stages'); // look up setup code
			$this->db->where('id',$code);
			$query = $this->db->get();
			$stage = $query->row_array();
			if($stage['redirect'] != "") { // if yes redirect them to correct setup page
				redirect($stage['redirect']);
			}
		}
	}

}

?>