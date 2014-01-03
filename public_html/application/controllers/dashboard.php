<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	   $this->load->model('activity_model');
	   $this->load->model('measurements_model');
	}

	public function index() 
	{
		$data['title'] = "My Dashboard";
		$me = $this->l_auth->current_user_id();
		$data['pbs'] = $this->activity_model->get_personal_bests($me);
		//$data['pbs_2k'] = $this->activity_model->erg_history($me,"distance",2);
		$data['height'] = $this->measurements_model->get_height($me);
		$data['weight'] = $this->measurements_model->get_weight($me);
		$data['armspan'] = $this->measurements_model->get_armspan($me);
		$this->load->view('templates/header',$data);
		$this->load->view('dashboard/dashboard',$data	);
		$this->load->view('templates/footer');
	}

	public function ajax_graphdata() {
		$me = $this->l_auth->current_user_id();
		$type = $this->input->get('t');
		$dislen = $this->input->get('d');
		$label = $dislen . "K";
		if($type == 'time') {
			$label = $dislen . " min";
		}
		$recent_scores = $this->activity_model->erg_history($me,$type,$dislen);

		$data = array();
		$cols = array(
				array('type'=>'date','label'=>'Date'),
				array('type'=>'number','label'=>'Score','id'=>'score'),
				array('type'=>'string','role'=>'tooltip','p'=>array('html'=>true))
			);
		$rows = array();
		foreach($recent_scores as $score) {
			$split = $this->activity_model->outputSplit($score['avg_split']);
			$ts =  strtotime($score['sort_time']);
			$date = "Date" . date("(Y,",$ts) . (date("m",$ts) - 1) . date(",d)",$ts);
			$row = array(
				array('v'=>$date,'f'=>null),
				array('v'=>(float)$score['avg_split'],'f'=>$split),
				array('v'=>'<p><strong>'.$split.'</strong><br><a href="#">View all ' . $label . ' scores</a></p>'));
			$rows[] = array('c'=>$row);
		}
		$data['cols'] = $cols;
		$data['rows'] = $rows;
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function ajax_radata() {
		$me = $this->l_auth->current_user_id();

		//$recent_scores = $this->activity_model->list_activities($me);
		//$activity_each_day = $this->activity_model->last_x_days($me);
		//print_r($activity_each_day);

		$data = array();
		$cols = array(
				array('type'=>'date','label'=>'Date'),
				array('type'=>'number','label'=>'No. Activities'),
				array('type'=>'string','role'=>'tooltip','p'=>array('role'=>'tooltip','html'=>true)),

			);
		$rows = array();
		/*foreach($recent_scores as $score) {
			$split = $this->activity_model->outputSplit($score['avg_split']);
			$ts =  strtotime($score['sort_time']);
			$date = "Date" . date("(Y,",$ts) . (date("m",$ts) - 1) . date(",d)",$ts);
			$row = array(
				array('v'=>$date,'f'=>null),
				array('v'=>(float)$score['avg_split'],'f'=>$split),
				array('v'=>'<p><strong>'.$split.'</strong><br><a href="#">View all scores</a></p>'));
			$rows[] = array('c'=>$row);
		}*/
		for($i = 90;$i>=0;$i--) {
			$ts = time() - ($i * 86400);
			$year = date("Y",$ts);
			$month = date("m",$ts);
			$day = date("d",$ts);
			//echo $ts;
			$no = $this->activity_model->count_for_day($me,$ts);
			$date = "Date(".$year."," . ($month -1) . "," . $day .")";

			$list = '<div class="list-group">';
			$items = $this->activity_model->list_exercises_for_day($me,$year,$month,$day);
			foreach($items as $item) {
			$list .= '<a href="#" class="list-group-item">
			    <h4 class="list-group-item-heading">'.$item['label'].'</h4>
			    <p class="list-group-item-text">...</p>
			  </a>';
			}
			$list .= '</div>';

			$tooltip = '<div style="width:260px;max-height:400px;overflow:scroll" class="well"><h3>'.date("M jS",$ts).'</h3>'.$list.'<p><a href="'.base_url().'diary#day_'.date("Y",$ts).'_'.date("m",$ts).'_'.date("d",$ts).'">View in diary</a></p></div>';
			$row = array(
				array('v'=>$date,'f'=>null),
				array('v'=>$no,'f'=>null),
				array('v'=>$tooltip)
				);
			$rows[] = array('c'=>$row);
		}
		$data['cols'] = $cols;
		$data['rows'] = $rows;
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function ajax_updatem() {
		$type = $this->input->get('t');
		$val = $this->input->get('v');
		$me = $this->l_auth->current_user_id();
		switch($type) {
			case "height":
				$this->measurements_model->update_height($me,$val);
				break;
			case "weight":
				$this->measurements_model->update_weight($me,$val);
				break;
			case "armspan":
				$this->measurements_model->update_armspan($me,$val);
				break;
		}
		

	}


}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */