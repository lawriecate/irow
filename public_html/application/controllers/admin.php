<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Secure_Controller {

	public function index()
	{
		$this->load->view('templates/header');
		$this->load->view('admin/index');
		$this->load->view('templates/footer');
	}

	public function userlist()
	{
		$this->load->view('templates/header');
		$this->load->view('admin/userlist');
		$this->load->view('templates/footer');
	}

	public function clublist()
	{
		$this->load->view('templates/header');
		$this->load->view('admin/clublist');
		$this->load->view('templates/footer');
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */