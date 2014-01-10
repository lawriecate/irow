<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class People extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	}

	public function view($id)
	{
		// displays an individuals public profile

		// lookup a person
		$data['person'] = $this->user_model->get_by_id($id);
		if($data['person'] != NULL && $data['person']['setup'] == 1) { // check they're account has been setup
			$data['title'] = $data['person']['name'] . " on iRow";
			$data['memberships'] = $this->user_model->memberships($id);
			$this->load->view('templates/header',$data);
			$this->load->view('profile/public_profile',$data);
			$this->load->view('templates/footer');
		} else {
			return FALSE;
		}
	}

}

