<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coach extends Secure_Controller {

	public function index()
	{
		$data['title'] = "Coach Assistant";
		$this->load->view('templates/header',$data);
		$this->load->view('coach/deny');
		$this->load->view('templates/footer');
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */