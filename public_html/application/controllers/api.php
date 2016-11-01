<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API extends API_Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	    $this->load->model('activity_model');
	}

	public function index()
	{
		echo 'Successfully authorized connection';
	}

	public function logbook_feed() 
	{
		$this->load->model('activity_model');
		$me = $this->api_token_user_id;
		$p =1;
		$response = $this->activity_model->list_activities($me,$p,1);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function logbook_add()
	{
		$this->load->model('activity_model');
		$me = $this->api_token_user_id;

		$type = $this->activity_model->getTypeByRef($this->input->post('inputType'));
			$c0 = array(

	     				'distance'=>$this->input->post('inputDistance'),
	     				'time'=>$this->input->post('inputTime'),
	     				'rate'=>$this->input->post('inputRate'),
	     				'split'=>$this->input->post('inputSplit')
	     				);

	     $response =	$this->activity_model->add(
	     		$this->input->post('inputDate'),
	     		$this->l_auth->current_user_id(),
	     		$this->l_auth->current_user_id(),
	     		$type,
	     		array($c0)	,
	     		$this->input->post('inputNotes')
	     		);
	     $this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function exercise_types()
	{
		$types = $this->activity_model->get_types();

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($types));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */