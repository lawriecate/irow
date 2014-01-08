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
	
	public function analyse() {
		$data['title'] = "Analyse Performance";
		$data['graphs'] = array(
			array('name'=>'2K erg',
				'type'=>'ergd',
				'distance'=>2000),
			array('name'=>'5K erg',
				'type'=>'ergd',
				'distance'=>2000),
			array('name'=>'&frac12; hour erg',
				'type'=>'ergt',
				'time'=>30 * 60)
		);
		$this->load->view('templates/header',$data);
		$this->load->view('coach/analyse',$data);
		$this->load->view('templates/footer');
	}

	public function ajax_graphdata()
	{
		$me = $this->l_auth->current_user_id();
		$distance = $this->input->get('distance');
		$time = $this->input->get('time');
		$type_filter = $this->input->get('type');
		$people = explode(",",$this->input->get('who'));

		$rstart = strtotime($this->input->get('start'));
		$year = date("Y",$rstart);
		$month = date("m",$rstart);
		$day = date("d",$rstart);
		//$rstart_js = "Date(".$year."," . ($month-1) . "," . $day .")";

		$rstart_js = $year."," . ($month) . "," . $day ;
		

		$rend = strtotime($this->input->get('end'));
		$year = date("Y",$rend);
		$month = date("m",$rend);
		$day = date("d",$rend);
		//$rend_js = "Date(".$year."," . ($month-1) . "," . $day .")";
		$rend_js = $year."," . ($month) . "," . $day;

		$valid = TRUE;

		$valid_people = $this->activity_model->get_coaches_people($me,"");
		
		if(count($people) > 0) {
			foreach($people as $person) {
				$found = FALSE;
				foreach($valid_people as $valid_person) {
					
					if($person == $valid_person['user_id']) {
						$found = TRUE;
						break;
					}
				}
				if($found == FALSE) {
					$valid = FALSE;
				}
			}
		}

		$response = array();
		$response['cols'] = array(
				array('type'=>'date','label'=>'Date'),
			);
		foreach ($people as $key => $person) {
			$record= $this->user_model->get_by_id($person);
			$name = $record['name'];
			$response['cols'][] = array('type'=>'number','label'=>$name);
		}
		$response['rows'] = array();
		if($valid == TRUE) {
			//$response['people'] = $people;
			
			$filter = array(
				'sort_time >= ' => date("Y-m-d",$rstart),
				'sort_time <= ' => date("Y-m-d",$rend),
				'type' => '1'
				);
			$activities = $this->activity_model->search_activities($filter,$people);
			$preprocess = array();
			foreach($activities as $activity) {
				$ts = strtotime($activity['sort_time']);

				//$tooltip = '<div style="width:260px;max-height:400px;overflow:scroll" class="well"><h3>Activity Detail</h3><p>'.$activity['avg_split'].'</p></div>';
				foreach ($people as $person) {
					
						//$preprocess[$ts][$person] = NULL;
					
				}
				foreach ($people as $person) {
					if($person == $activity['user']) {
						$preprocess[$ts][$person] = $activity['avg_split'];
					}
				}
				
			}
//print_r($preprocess);
			$finalRows = array();
			foreach($preprocess as $ts => $prerow) {
				$date = "Date" . date("(Y,",$ts) . (date("m",$ts) - 1) . date(",d)",$ts);
				$row = array(
					array('v'=>$date)
				);
				foreach($prerow as $person => $indscore) {
					$row[]=array('v'=>$indscore);
				}
				$finalRows[]=array('c'=>$row);

			}
			$response['rows'] = $finalRows;
			//print_r($response);

		//	$response['gbegin'] = $rstart_js;
		//	$response['gend'] = $rend_js;
		} else {
			$response['error'] = TRUE;
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */