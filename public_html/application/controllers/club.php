<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Club extends Secure_Controller {

	public function index()
	{
		$this->load->view('templates/header');
		$this->load->view('club/view');
		$this->load->view('templates/footer');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */