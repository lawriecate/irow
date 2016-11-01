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
		// dislpay page which tells you how to register as a cach
		$data['title'] = "Coach Assistant";
		$this->load->view('templates/header',$data);
		$this->load->view('coach/deny');
		$this->load->view('templates/footer');
	}

	public function invitations() {
		// function to display list of invited users
		$data['title'] = "Invitations";
		$me = $this->l_auth->current_user_id();
		$data['invitations'] = $this->user_model->get_invitations($me);
		$this->load->view('templates/header',$data);
		$this->load->view('coach/invitations',$data);
		$this->load->view('templates/footer');
	}

	public function ajax_invite() {
		$me = $this->l_auth->current_user_id();
		$user = $this->input->post('uid');
		$emailto = $this->input->post('email');
		$response = 'ER_GENERAL';
		if(filter_var($emailto, FILTER_VALIDATE_EMAIL)) {
			$response = $this->user_model->coach_invite($me,$user,$emailto);
		} else {
			$response = 'ER_EMAIL';
		}

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function log()
	{
		// display the mutiple person activity log page
		$data['title'] = "Log Activity";
		$data['types'] = $this->activity_model->get_types();
		$this->load->view('templates/header',$data);
		$this->load->view('coach/add',$data);
		$this->load->view('templates/footer');	
	}

	public function logbook()
	{
		// dispaly the coach logbook
		$data['title'] = "Coach Logbook";
		$me = $this->l_auth->current_user_id();
		$data['activities'] = $this->activity_model->list_activities($me,1,TRUE);
		
		$this->load->view('templates/header',$data);
		$this->load->view('coach/logbook',$data);
		$this->load->view('templates/footer');	
	}

	public function ajax_logbook_loadpage()
	{
		// provide javascript interface for loadng pages of logbook
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
		// generate CSV file of logged activities
		$me = $this->l_auth->current_user_id();
		$data =  $this->activity_model->list_activities($me,NULL,TRUE);

		$fp =  fopen('php://output', 'w');
		ob_start();
		$headings = array('Date','Name','Label','Split','Time','Distance','Rate','Heart Rate','Split (Secs)','System Time');
		 fputcsv($fp, $headings);
		foreach ($data as $fields) {
		    fputcsv($fp, $fields);
		}

		fclose($fp);
		// Get the contents of the output buffer
		$string = ob_get_clean();
		        
		$filename = 'iRow_logbook_' . date('Ymd') .'_' . date('His');
		        
		// Output CSV-specific headers that make browser download the file as CSV
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
		// javascript interface to get what fields each activity can collect
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
		// javascript interface to search for users the coach can manager
		$query = $this->input->get('q');
		$me = $this->l_auth->current_user_id();
		$people = $this->activity_model->get_coaches_people($me,$query);
		//$people[] = array('name'=>'J18 Boys','user_id'=>'GROUP_J18M');
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($people));
	}

	public function ajax_nameadd()
	{
		// javascript interface to add a new user
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
		// javascript interface to save an activity
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
		// javascript interface that tells page user still logged in
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(TRUE));
	}
	
	private function get_analyse_graphs() {
		// temp function to return common graphs
		return  array(
			array('name'=>'2K erg',
				'type'=>'ergd',
				'distance'=>2000),
			array('name'=>'5K erg',
				'type'=>'ergd',
				'distance'=>2000),
			array('name'=>'12K erg',
				'type'=>'ergd',
				'distance'=>12000),
			array('name'=>'&frac12; hour erg',
				'type'=>'ergt',
				'time'=>30 * 60)
		);
	}
	
	public function analyse() {
		// displays graphing page
		$data['title'] = "Analyse Performance";
		$data['graphs'] = $this->get_analyse_graphs();
		$this->load->view('templates/header',$data);
		$this->load->view('coach/analyse',$data);
		$this->load->view('templates/footer');
	}

	public function ajax_graphdata()
	{
		// javascript interface to output erg data in Google Data format
		// if you ever need to understand/edit this, I'm truly sorry
		$me = $this->l_auth->current_user_id(); // read in inputs
		$distance = $this->input->get('distance');
		$time = $this->input->get('time');
		$typeRef = $this->input->get('type');
		$graphTypes = $this->get_analyse_graphs(); // get class defined graph types

		$graph = $graphTypes[$typeRef]; // get an array of information for inputted graph type

		$graphType = $this->activity_model->getTypeByID($this->activity_model->getTypeIDByRef($graph['type'])); // get information about the graph exercise type from db

		$people = explode(",",$this->input->get('who')); // get a list of IDs inputted

		$rstart = strtotime($this->input->get('start')); // create date start end range
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

		$valid_people = $this->activity_model->get_coaches_people($me,""); // get all the people current user is authorized for access
		
		if(count($people) > 0) { // check validity of people entered
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
		$response['cols'] = array( // add date column
				array('type'=>'date','label'=>'Date'),
			);
		foreach ($people as $key => $person) { // add column for each person
			$record= $this->user_model->get_by_id($person);
			$name = $record['name'];
			$response['cols'][] = array('type'=>'number','label'=>$name);
		}
		$response['rows'] = array(); // build rows
		if($valid == TRUE) {
			//$response['people'] = $people;
			$filter = array(
				'sort_time >= ' => date("Y-m-d",$rstart),
				'sort_time <= ' => date("Y-m-d",$rend),
				'type' => $graphType['id']
				);
if(isset($graph['distance']) ) {
$filter = array_merge(array('total_distance'=>$graph['distance']),$filter);
}
if(isset($graph['time']) ) {
$filter = array_merge(array('total_time'=>$graph['time']),$filter);
}

			$activities = $this->activity_model->search_activities($filter,$people); // get activities from db

			$preprocess = array();
			foreach($activities as $activity) { // loop through each activity
		
				$ts = date("Ymd",strtotime($activity['sort_time'])); // give Ymd timestamp to each activty

				//$tooltip = '<div style="width:260px;max-height:400px;overflow:scroll" class="well"><h3>Activity Detail</h3><p>'.$activity['avg_split'].'</p></div>';

				foreach ($people as $person) { // ensure a NULL row is included for each activity date
					
					if($person == $activity['user']) { // if this person is on list
						$preprocess[$ts][$activity['user']] = $activity['avg_split']; // add to preprocess
					} else {
						if(!isset($preprocess[$ts][$person]))  {
							$preprocess[$ts][$person] = null; // if a value still hasn't been added, put in a null value
						}
					}
				}
				
			}

			$finalRows = array(); // build final rows

			foreach($preprocess as $dateymd=> $prerow) {
$ts = strtotime($dateymd);
				$date = "Date" . date("(Y,",$ts) . (date("m",$ts) - 1) . date(",d)",$ts); // pretty date
				$row = array(
					array('v'=>$date) // add date to row
				);

				foreach($prerow as $person => $indscore) {
					if($indscore == null ) { // score is null, set value to null
$row[]=array('v'=>null);
 					} else {
					$row[]=array('v'=>$indscore,'f'=>$this->activity_model->outputSplit($indscore)); // otherwise add score in seconds + pretty split
					}
				}

				$finalRows[]=array('c'=>$row); // add row

			}
			$response['rows'] = $finalRows;
			
		} else {
			$response['error'] = TRUE;
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response)); // output


	}


}

/* End of file coach.php */
/* Location: ./application/controllers/coach.php */