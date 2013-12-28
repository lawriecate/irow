<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Club extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('club_model');
	}

	public function index()
	{
		// club finder
		$data['title'] = "Rowing Clubs on iRow";
		$this->load->view('templates/header',$data);
		//$this->load->view('club/view');
		$this->load->view('templates/footer');
	}

	public function profile($ref) {
		$data['title'] = "My Club";
		$data['club'] = $this->club_model->get_by_ref($ref);
		if($data['club']) {
			$this->load->view('templates/header',$data);
			$this->load->view('club/view',$data);
			$this->load->view('templates/footer');
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */