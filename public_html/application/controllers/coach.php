<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coach extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	   $this->load->model('activity_model');
	   $this->load->model('measurements_model');
	}

	public function index()
	{
		$data['title'] = "Coach Assistant";
		$this->load->view('templates/header',$data);
		$this->load->view('coach/deny');
		$this->load->view('templates/footer');
	}

	public function log()
	{
		$data['title'] = "Log Activity";
		$data['types'] = $this->activity_model->get_types();
		$this->load->view('templates/header',$data);
		$this->load->view('coach/add',$data);
		$this->load->view('templates/footer');	
	}

	public function logbook()
	{
		$data['title'] = "Coach Logbook";
		$me = $this->l_auth->current_user_id();
		$data['activities'] = $this->activity_model->list_activities($me,1,TRUE);
		$this->load->view('templates/header',$data);
		$this->load->view('coach/logbook',$data);
		$this->load->view('templates/footer');	
	}

	public function ajax_logbook_loadpage()
	{
		$me = $this->l_auth->current_user_id();
		$p = $this->input->get('p');
		if($p < 1) {
			$p = 1;
		}
		$response = $this->activity_model->list_activities($me,$p,TRUE);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function dl_logbook_csv() 
	{
		$me = $this->l_auth->current_user_id();
		$data =  $this->activity_model->list_activities($me,NULL,TRUE);

		$fp =  fopen('php://output', 'w');
		ob_start();
		$headings = array('Date','Name','Label','Split','Time','Distance','Rate','Split (Secs)','System Time');
		 fputcsv($fp, $headings);
		foreach ($data as $fields) {
		    fputcsv($fp, $fields);
		}

		fclose($fp);
		// Get the contents of the output buffer
		$string = ob_get_clean();
		        
		$filename = 'iRow_logbook_' . date('Ymd') .'_' . date('His');
		        
		// Output CSV-specific headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.$filename.'.csv";' );
		header("Content-Transfer-Encoding: binary");
		 
		exit($string);	
	}

	public function ajax_getfields() 
	{
		$type = $this->input->get('type');
		$activity = $this->activity_model->getTypeByID($this->activity_model->getTypeByRef($type));
		$fields = array();
		$fields[] = "name";
		if($activity['show_time'] == "1") {
			$fields[] = "time";
		}
		if($activity['show_distance'] == "1") {
			$fields[] = "distance";
		}
		if($activity['show_split'] == "1") {
			$fields[] = "split";
		}
		if($activity['show_rate'] == "1") {
			$fields[] = "rate";
		}
		$fields[] = "notes";

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($fields));
	}

	public function ajax_namesuggest() 
	{
		$query = $this->input->get('q');
		$me = $this->l_auth->current_user_id();
		$people = $this->activity_model->get_coaches_people($me,$query);

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($people));
	}

	public function ajax_nameadd()
	{
		$name = $this->input->post('name');
		if($name != "") {
			$me = $this->l_auth->current_user_id();

			$newUserId = $this->user_model->create_invited_user($me,$name);

			$newUser = array(
				'user_id'=>$newUserId,
				'name'=>$name
			);
		} else {
			$newUser = FALSE;
		}
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($newUser));
	}

	public function ajax_saveactivity() {
		$this->load->helper('array');
		$coach = $this->l_auth->current_user_id();
		$input_array = ($this->input->post('ac'));
		$activity = current($input_array);

		// TODO: ADD VALIDITY CHECKS
		$valid = TRUE;
		//$person = $activity['person'];
		$split = $activity['split'];
		$time = $activity['time'];
		$distance = $activity['distance'];
		$rate = $activity['rate'];
		$notes = $activity['notes'];

		$date=  $this->input->post('date');
		$typeref = $this->input->post('type');
		$person = $this->input->post('person');

		// check if is person otherwise add
		if(is_numeric($person)) {
			$valid =(bool) $valid AND $this->user_model->get_by_id($person);
		} else {
			$person = $this->user_model->create_invited_user($coach,$person);
		}
			

		////////
		$type = $this->activity_model->getTypeByRef($typeref);
		$c0 = array(
			'distance'=>$distance,
			'time'=>$time,
			'rate'=>$rate,
			'split'=>$split
			);

		if($valid == TRUE ) {
			$add = $this->activity_model->add(
		     		$date,
		     		$person,
		     		$coach,
		     		$type,
		     		array($c0)	,
		     		$notes
		     		);

			$this->output
			->set_content_type('application/json')
			->set_output(json_encode($add));
		} else {

		}
	}

	function ajax_conn() {
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(TRUE));
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */