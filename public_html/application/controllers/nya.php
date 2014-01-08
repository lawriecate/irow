<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nya extends CI_Controller {

	public function index()
	{
		$data['title'] = "Coming Soon";
		$this->load->view('templates/header',$data);
		$this->load->view('templates/comingsoon');
		$this->load->view('templates/footer');
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */