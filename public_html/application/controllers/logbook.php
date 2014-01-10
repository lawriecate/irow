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
		// output logbook page
		$data['title'] = "Logbook";
		$me = $this->l_auth->current_user_id();
		$data['activities'] = $this->activity_model->list_activities($me,1);
		$this->load->view('templates/header',$data);
		$this->load->view('logbook/logbook',$data);
		$this->load->view('templates/footer');
	}

	public function detail($ref)
	{
		// show a detail record for one activity
		$modal=FALSE;
		if($this->input->get('modal')=="y") {
			$modal = TRUE;
		}

		$data['title'] = "Activity Record";
		$me = $this->l_auth->current_user_id();
		$data['activity'] = $this->activity_model->get_by_id($this->activity_model->get_id_from_ref($ref));
		// check permission
		if($data['activity']['user'] == $me) {
			if($modal == FALSE) {
				$this->load->view('templates/header',$data);
			}
			$this->load->view('logbook/activity',$data);
			if($modal == FALSE) {
				$this->load->view('templates/footer');
			}
		}	else {
			return FALSE;
		}
	}

	public function ajax_loadpage()
	{
		// javascript interface for loading a page of activities
		$me = $this->l_auth->current_user_id();
		$p = $this->input->get('p');
		if($p < 1) {
			$p = 1;
		}
		$response = $this->activity_model->list_activities($me,$p,1);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function dl_csv() 
	{
		// downloads a CSV file of activities 
		$me = $this->l_auth->current_user_id();
		$data =  $this->activity_model->list_activities($me,NULL);

		$fp =  fopen('php://output', 'w');
		ob_start();
		$headings = array('Date','Label','Split','Time','Distance','Rate','Split (Secs)','System Time','Ref');
		 fputcsv($fp, $headings);
		foreach ($data as $fields) {
		    fputcsv($fp, $fields);
		}

		fclose($fp);
		// Get the contents of the output buffer
		$string = ob_get_clean();
		        
		$filename = 'iRow_logbook_' . date('Ymd') .'_' . date('His');
		        
		// Output CSV-specific headers and forces browser to download page as CSV
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.$filename.'.csv";' );
		header("Content-Transfer-Encoding: binary");
		 
		exit($string);	
	}

}