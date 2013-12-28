<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('club_model');
	   $this->load->model('user_model');
	}


	public function index()
	{
		$data['title'] = "Search iRow";

		$q = $this->input->get('q');
		//if($q != "") {
			$data['query'] = $q;
			$data['results']['Clubs'] = $this->club_model->public_search($q);
			$data['results']['People'] = $this->user_model->public_search($q);
		//}

		$this->load->view('templates/header',$data);
		$this->load->view('search/results',$data);
		$this->load->view('templates/footer');
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */