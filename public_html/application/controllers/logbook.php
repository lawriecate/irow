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
		$data['activities'] = $this->activity_model->list_activities($me,1);
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
		$response = $this->activity_model->list_activities($me,$p,1);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function dl_csv() 
	{
		$me = $this->l_auth->current_user_id();
		$data =  $this->activity_model->list_activities($me,NULL);

		$fp =  fopen('php://output', 'w');
		ob_start();
		$headings = array('Date','Label','Split','Time','Distance','Rate','Split (Secs)','System Time');
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

}