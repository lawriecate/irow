<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Club extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('club_model');
	   $this->load->model('user_model');
	}

	public function index()
	{
		// club finder
		$data['title'] = "Rowing Clubs on iRow";
		$data['clubs'] = $this->club_model->get_all();
		$this->load->view('templates/header',$data);
		$this->load->view('club/tempindex');
		$this->load->view('templates/footer');
	}

	public function profile($ref) {
		$data['title'] = "My Club";
		$data['club'] = $this->club_model->get_by_ref($ref);
		$club_id = $data['club']['id'];
		$me = $this->l_auth->current_user_id();
		$data['membership'] = $this->user_model->membership($me,$data['club']['id']);
		$data['coaches'] = $this->club_model->get_coaches($club_id);
		$data['managers'] = $this->club_model->get_managers($club_id);
		if($data['club']) {
			$this->load->view('templates/header',$data);
			$this->load->view('club/view',$data);
			$this->load->view('templates/footer');
		}
	}

	public function ajax_chmembership() {
		$club_ref = $this->input->get('club');
		$club_ob = $this->club_model->get_by_ref($club_ref);
		$club = $club_ob['id'];
		$me=  $this->l_auth->current_user_id();
		$current = $this->user_model->membership($me,$club);
		if($current==FALSE) {
			// make member
			$this->user_model->join_club($me,$club);
			echo "Joined";
		} else {
			// leave club
			$this->user_model->leave_club($me,$club);
			echo "Not Joined";
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */