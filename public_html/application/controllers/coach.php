<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coach extends Secure_Controller {

	public function index()
	{
		$this->load->view('templates/header');
		$this->load->view('coach/add');
		$this->load->view('templates/footer');
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */