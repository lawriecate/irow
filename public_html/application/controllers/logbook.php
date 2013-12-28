<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logbook extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	   $this->load->model('activity_model');
	}

	public function index() 
	{
		$data['title'] = "Logbook";
		$me = $this->l_auth->current_user_id();
		$data['activities'] = $this->activity_model->list_activities($me);
		$this->load->view('templates/header',$data);
		$this->load->view('logbook/logbook',$data);
		$this->load->view('templates/footer');
	}

	public function ajax_loadpage()
	{
		$me = $this->l_auth->current_user_id();
		$p = $this->input->get('p');
		if($p < 1) {
			$p = 1;
		}
		$response = $this->activity_model->list_activities($me,$p);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

}