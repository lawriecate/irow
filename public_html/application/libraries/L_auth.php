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
		return (bool) $this->session->userdata('logged_in');
	}

	public function current_user_id() {
		if($this->logged_in()) {
			$user = $this->session->userdata('logged_in');
			return $user['id'];
		}
		return FALSE;
	}

	public function logout() {
		if($this->logged_in()) {
			$this->session->sess_destroy();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function is_disabled() {
		if($this->logged_in()) {
			$user = $this->user_model->get_by_id($this->current_user_id());
			if($user['disabled'] == 1) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

}

?>