<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Diary extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('activity_model');
	}


	public function index()
	{
		//displays diary html

		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$data['title'] = "Diary";
		$this->load->view('templates/header',$data);
		$data['types'] = $this->activity_model->getTypes();	
		//$data['this_week'] = $this_week_data;
		
		$this->load->view('diary/dashboard',$data);
		$this->load->view('templates/footer');
		
	}

	/*public function view($ref) RELOCATED TO LOGBOOK CONTROLLER
	{
		//$this->load->view('templates/header');
		$exercise = $this->activity_model->get($ref);
		//print_r($this->activity_model->getActivityComponents($exercise['id']));
		if($exercise == FALSE) {
			return FALSE; 
		} else {
			$data['exercise'] = $exercise;
			$this->load->view('diary/view',$data);
		}
//this->load->view('templates/footer');
	}*/

	public function add()
	{
		// POST interface that adds activity record
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
		// displays for to add new measurement
		$this->load->view('templates/header');
		$data['title'] = "Add Measurement";
		$this->load->view('diary/addmeasurement');
		$this->load->view('templates/footer');
	}

	public function ajax_updateexercise() {
		// javascript interface to update an exercise label, notes or time of activity (for homepage)
		$ref = $this->input->post('acid');
		$id = $this->activity_model->get_id_from_ref($ref);

		$update = array();
		$label = $this->input->post('inputLabel');
		if($label != "") {
			$update['label'] = $label;
		}
		$notes = $this->input->post('inputNotes');
		if($notes != "") {
			$update['notes'] = $notes;
		}
		$st = $this->input->post('inputSt');
		if($st != "") {
			$update['sort_time'] = $st;
		}

		$response = $this->activity_model->update($id,$update);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));

	}
	
	public function ajax_logexercise() {
		// javascript interface for logging an exercise
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

	public function ajax_diary_totals() {
		// javascript interface for getting the months of recorded activity for a given year
		$response = array();

		$me = $this->l_auth->current_user_id();
		$years = $this->activity_model->list_years($me);
		
		foreach($years as $year => $total) {
			$response[$year] = array();
			$response[$year]["t"] = $total;
			$response[$year]["m"] = $this->activity_model->list_months($me,$year);
		}
		
		//print_r($this->activity_model->list_days($me,2013,12));

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function ajax_diary_days() {
		// javascript interface for getting the months of recorded activity for a given day
		$year = $this->input->get('year');
		$month =  $this->input->get('month');

		$me = $this->l_auth->current_user_id();
		$days = $this->activity_model->list_days($me,$year,$month);

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($days));
	}

	public function ajax_diary_daylist() {
		// javascript interface for getting the exercise for a given day
		$year = $this->input->get('year');
		$month =  $this->input->get('month');
		$day = $this->input->get('day');

		$me = $this->l_auth->current_user_id();
		$list = $this->activity_model->list_exercises_for_day($me,$year,$month,$day);

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($list));
	}

	public function ajax_logetypes() {
		// javascript interface for getting the types of exercise
		$types = $this->activity_model->get_types();

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($types));
	}
	
	public function ajax_diary_view() {
		// javascript interface for outputting a diary view
		$hashtag = $this->input->get('tag');
		$parts = explode("_",$hashtag);
	
		$response = array();
		
		$command = $parts[0];
		$me = $this->l_auth->current_user_id();
		switch($command) {
			case "":
			case "life": // shows all years of activity  
				$years = $this->activity_model->list_years($me);
				$response["title"] = "All-time";
				$response["graphs"] = FALSE;
				$response["items"] = array();
				$response["breadcrumb"] = array(
							array('t'=>'Diary','href'=>"#life")
						);
				foreach($years as $year => $total) {
					$item = array();
					$item["title"] = "Year $year";
					$item["label"] = "";
					//$item["c"] = $total;
					$item["link"] = "#year_" . $year;
					$response["items"][] = $item;
				}
				break;
			case "year": // lists month in a year
				$year = (int) $parts[1];
				
				$months = $this->activity_model->list_months($me,$year);
				if($months) {
					$response["title"] = "Year " . $year;
					$response["return"]["title"] = "Return to lifetime view";
					$response["return"]["link"] = "#life";
					$response["breadcrumb"] = array(
							array('t'=>'Diary','href'=>"#life"),
						array('t'=>$year,'href'=>"#year_$year")
						);
					$response["items"] = array();
					foreach($months as $month => $count) {
						$monthName = date("F", mktime(0, 0, 0, $month, 10));
						$item = array();
						$item["title"] = "$monthName";
						$item["label"] = "$count exercises";
						$item["back"] = (($count > 0) ? '#E6E6E6' : '#fff');
						@$item["link"] = "#month_".$year."_".$month;
						$response["items"][] = $item;
					}

					$graph1 = array(
						'labels' => array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),
						'datasets' => array(
							array(
								'fillColor' => "rgba(220,220,220,0.5)",
								'strokeColor' => "rgba(220,220,220,1)",
								'pointColor' => "rgba(220,220,220,1)",
								'pointStrokeColor' => "#fff",
								'data'=>$this->activity_model->get_year_averages($me,$year,'2K')
							),
							array(
								'fillColor' => "rgba(232,44,12,0.5)",
								'strokeColor' => "rgba(232,44,12,1)",
								'pointColor' => "rgba(232,44,12,1)",
								'pointStrokeColor' => "#E82C0C",
								'data'=>$this->activity_model->get_year_averages($me,$year,'30min')
							)
							)
						);
					/*$graph2 = array(
						'labels' => array("Jan","Feb","March","Apr","May","June","July","August","Sept","Oct","Nov","Dec"),
						'datasets' => array(
							array(
								'fillColor' => "rgba(220,220,220,0.5)",
								'strokeColor' => "rgba(220,220,220,1)",
								'pointColor' => "rgba(220,220,220,1)",
								'pointStrokeColor' => "#fff",
								'data'=>$this->activity_model->get_month_averages($me,$year,'30min')
							)
							)
						);*/

					$response["graphs"] = array($graph1);
				}
				break;
			case "month": // list days in a month
				$year = (int) $parts[1];
				$month = (int)$parts[2];
				$monthName = date("F", mktime(0, 0, 0, $month, 10));
				$days = $this->activity_model->list_days($me,$year,$month);
				if($days) {
					$response["title"] = date("F Y",mktime(0, 0, 0, $month, 10));
					$response["return"]["title"] = "Return to $year";
					$response["return"]["link"] = "#year_$year";
					$response["breadcrumb"] = array(
						array('t'=>'Diary','href'=>"#life"),
					array('t'=>$year,'href'=>"#year_$year"),
					array('t'=>$monthName,'href'=>"#month_".$year."_".$month)
						);
					$response["items"] = array();

					foreach($days as $day => $count) {

						$item = array();
						$item["title"] = "Day $day";
						$item["label"] = "$count exercises";
						//$item["c"] = $total;
						$item["back"] = (($count > 0) ? '#E6E6E6' : '#fff');
						@$item["link"] = "#day_".$year."_".$month."_".$day;
						$response["items"][] = $item;
					}
					$labels = array();
					for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, $month, $year);$i++) {
						$labels[] = $i;
					}
					$graph1 = array(
						'labels' => $labels,
						'datasets' => array(
							array(
								'fillColor' => "rgba(220,220,220,0.5)",
								'strokeColor' => "rgba(220,220,220,1)",
								'pointColor' => "rgba(220,220,220,1)",
								'pointStrokeColor' => "#fff",
								'data'=>$this->activity_model->get_month_averages($me,$year,$month,'2K')
							),
							array(
								'fillColor' => "rgba(232,44,12,0.5)",
								'strokeColor' => "rgba(232,44,12,1)",
								'pointColor' => "rgba(232,44,12,1)",
								'pointStrokeColor' => "#E82C0C",
								'data'=>$this->activity_model->get_month_averages($me,$year,$month,'30min')
							)
							)
						);
					/*$graph2 = array(
						'labels' => array("Jan","Feb","March","Apr","May","June","July","August","Sept","Oct","Nov","Dec"),
						'datasets' => array(
							array(
								'fillColor' => "rgba(220,220,220,0.5)",
								'strokeColor' => "rgba(220,220,220,1)",
								'pointColor' => "rgba(220,220,220,1)",
								'pointStrokeColor' => "#fff",
								'data'=>$this->activity_model->get_month_averages($me,$year,'30min')
							)
							)
						);*/

					$response["graphs"] = array($graph1);
				
				}
				break;
			case "day": // lists any activities in a given day
				$year = (int) $parts[1];
				$month = (int)$parts[2];
				$day = (int)$parts[3];
				$monthName = date("F", mktime(0, 0, 0, $month, 10));
				$ex = $this->activity_model->list_exercises_for_day($me,$year,$month,$day);

				if($ex || (is_array($ex) && count($ex)==0)) {
					$response["title"] = date("l jS \of F Y",strtotime($year."-".$month."-".$day));
					$response["graphs"] = FALSE;
					$response["return"]["title"] = "Return to $month";
					$response["return"]["link"] = "#month_".$year."_".$month;
					$response["showAdd"] = TRUE;
					$response["showAddDate"] = date("d-m-Y",strtotime($year."/".$month."/".$day));
					$response["breadcrumb"] = array(
						array('t'=>'Diary','href'=>"#life"),
					array('t'=>$year,'href'=>"#year_$year"),
					array('t'=>$monthName,'href'=>"#month_".$year."_".$month),
					array('t'=>$day,'href'=>"#day_".$year."_".$month."_".$day),
						);
					$response["items"] = array();
					foreach($ex as $n => $ex) {
						$item = array();
						$item["title"] = "" . $ex['label'];
						if($ex['avg_split'] != NULL) {
							$item["label"] = $this->activity_model->outputSplit($ex['avg_split']);
						} else {
							$item["label"] = "";
						}
						//$item["c"] = $total;
						$item["back"] = ('#fff');
						$item["link"] = base_url() . "logbook/detail/" . $ex['ref']."/?modal=y";
						$item["link_modal"] = TRUE;
						$response["items"][] = $item;
					}
				}
			break;
			default:
				$response = FALSE;
		}
		
	
		
		
		//print_r($this->activity_model->list_days($me,2013,12));

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}
	
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */