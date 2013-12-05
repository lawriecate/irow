<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Diary extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('activity_model');
	}


	public function index()
	{
		//echo $this->l_auth->current_user_id();
		//print_r($this->activity_model->getActivityComponents('5275ad3e83'));
		$start_of_week = time() - (7 * 60 * 60 * 24);
		$end_of_week = time();
		$this_week_data = ($this->activity_model->list_activities($start_of_week,$end_of_week,$this->l_auth->current_user_id() ));
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		$this->load->view('templates/header');
		$data['types'] = $this->activity_model->getTypes();	
		$data['this_week'] = $this_week_data;
		$this->load->view('diary/dashboard',$data);
		$this->load->view('templates/footer');
		
	}

	public function view($ref)
	{
		//$this->load->view('templates/header');
		$exercise = $this->activity_model->get($ref);
		if($exercise == FALSE) {
			return FALSE; 
		} else {
			$data['exercise'] = $exercise;
			$this->load->view('diary/view',$data);
		}
//this->load->view('templates/footer');
	}

	public function add()
	{
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		if($this->input->post('inputDate') == "")
		{
		    redirect('diary');
		}
		else
		{
			// lookup type, then setup insert
			$type = $this->activity_model->getTypeByRef($this->input->post('inputType'));
			$c0 = array(

	     				'distance'=>$this->input->post('inputDistance'),
	     				'time'=>$this->input->post('inputTime'),
	     				'rate'=>$this->input->post('inputRate'),
	     				'split'=>$this->input->post('inputSplit')
	     				);

	     	$this->activity_model->add(
	     		$this->input->post('inputDate'),
	     		$this->l_auth->current_user_id(),
	     		$this->l_auth->current_user_id(),
	     		$type,
	     		array($c0)	,
	     		$this->input->post('inputNotes')
	     		);
		}
	}

	public function addmeasurement()
	{
		$this->load->view('templates/header');
		$this->load->view('diary/addmeasurement');
		$this->load->view('templates/footer');
	}
	
	public function ajax_logexercise() {
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		// lookup type, then setup insert
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
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */